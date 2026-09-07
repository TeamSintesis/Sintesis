#!/usr/bin/env python3
"""
Generator Feature Test JSON:API (tests/Feature/*Test.php) untuk seluruh
27 entitas SIMPPM, berdasarkan spec/entities.json.

Setiap kelas uji yang dihasilkan menguji:
1. Permintaan tanpa autentikasi ditolak (401) pada endpoint index.
2. Peran yang berwenang (selalu mencakup admin_lppm/pengelola) dapat
   melihat daftar (200) dan membuat data baru (201) sesuai Policy.
3. Peran yang TIDAK berwenang menerima 403 saat mencoba membuat data,
   sesuai aturan RBAC per kategori entitas.
4. Data yang berhasil dibuat dapat dilihat detailnya (200) oleh peran
   yang berwenang.

Nilai atribut uji dibangun otomatis dari tipe kolom pada entities.json
agar lolos aturan validasi (Request) yang jenis rule-nya diturunkan dari
sumber data yang sama (lihat generate_jsonapi.py).
"""
import json
import os

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SPEC_PATH = os.path.join(BASE, "spec", "entities.json")
TEST_DIR = os.path.join(BASE, "tests", "Feature")
os.makedirs(TEST_DIR, exist_ok=True)

with open(SPEC_PATH) as f:
    SPEC = json.load(f)

ENTITIES = {e["table"]: e for e in SPEC["entities"]}
ORDER = [e["table"] for e in SPEC["entities"]]


def to_camel(snake: str) -> str:
    parts = snake.split("_")
    return parts[0] + "".join(p.capitalize() for p in parts[1:])


def php_value_for(table: str, col: dict) -> str:
    """Kembalikan ekspresi PHP (bukan literal Python) untuk nilai kolom uji."""
    t = col["type"]
    name = col["name"]

    if t == "enum":
        return f"'{col['values'][0]}'"
    if t == "boolean":
        return "false"
    if t == "integer":
        return "1"
    if t == "year":
        return "'2026'"
    if t == "date":
        return "'2026-01-15'"
    if t == "decimal":
        precision = col.get("precision", 15)
        if precision <= 5:
            return "'85.00'"
        return "'5000000.00'"
    if t == "text":
        return f"'Contoh isi {name.replace('_', ' ')} untuk keperluan pengujian otomatis.'"

    # string biasa
    if col.get("unique"):
        length = col.get("length", 20)
        pattern = "#" * min(length, 15)
        return f"(string) fake()->unique()->numerify('{pattern}')"
    if "url" in name or "tautan" in name or "bukti" in name:
        return "'https://contoh.simppm.test/berkas.pdf'"
    return f"'Contoh {name.replace('_', ' ')}'"


HEADER_TMPL = '''<?php

namespace Tests\\Feature;

use App\\Models\\User;
{use_models}
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\Feature\\Concerns\\BantuanUjiJsonApi;
use Tests\\TestCase;

/**
 * Feature Test JSON:API untuk resource "{resource_type}" ({label}).
 *
{doc}
 *
 * Dibuat otomatis oleh spec/generate_tests.py berdasarkan
 * spec/entities.json, agar aturan validasi & RBAC yang diuji selalu
 * konsisten dengan definisi entitas yang sebenarnya.
 */
class {model}Test extends TestCase
{{
    use RefreshDatabase;
    use BantuanUjiJsonApi;

{relasi_method}
{attr_method}
    /**
     * Permintaan tanpa autentikasi harus ditolak dengan status 401.
     */
    public function test_tanpa_autentikasi_ditolak(): void
    {{
        $response = $this->getJson('/api/v1/{resource_type}', $this->headerJsonApi());

        $response->assertStatus(401);
    }}

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang melihat daftar data
     * pada seluruh kategori entitas SIMPPM.
     */
    public function test_pengelola_dapat_melihat_daftar(): void
    {{
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->getJson('/api/v1/{resource_type}', $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }}

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang membuat data baru
     * pada seluruh kategori entitas SIMPPM (lihat {model}Policy::create()).
     */
    public function test_pengelola_dapat_membuat_data(): void
    {{
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->postJson('/api/v1/{resource_type}', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(201);
    }}

    /**
     * Peran '{forbidden_role}' tidak termasuk peran yang berwenang membuat
     * data "{resource_type}" (lihat {model}Policy::create()), sehingga
     * harus ditolak dengan status 403.
     */
    public function test_peran_tidak_berwenang_ditolak_saat_membuat(): void
    {{
        $this->sebagaiPeran(User::{forbidden_const});

        $response = $this->postJson('/api/v1/{resource_type}', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(403);
    }}

    /**
     * Data yang berhasil dibuat harus dapat dilihat detailnya oleh
     * pengelola LPPM.
     */
    public function test_pengelola_dapat_melihat_detail(): void
    {{
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $dibuat = $this->postJson('/api/v1/{resource_type}', $this->payloadValid(), $this->headerJsonApi());
        $dibuat->assertStatus(201);
        $id = $dibuat->json('data.id');

        $response = $this->getJson("/api/v1/{resource_type}/{{$id}}", $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $id);
    }}
}}
'''

FORBIDDEN_ROLE_CONST = {
    "dosen": "PERAN_DOSEN",
    "mahasiswa": "PERAN_MAHASISWA",
    "mitra_eksternal": "PERAN_MITRA_EKSTERNAL",
}

# Kategori -> peran yang TIDAK berwenang membuat data (dipetakan dari
# app/Policies/*Policy.php yang sudah dibuat pada spec/generate_policies.py).
FORBIDDEN_ROLE = {}
for t in ["prodi", "dosen", "mahasiswa", "renstra", "peta_jalan", "pedoman",
          "sarana_prasarana", "skema_pendanaan", "mitra"]:
    FORBIDDEN_ROLE[t] = "dosen"
FORBIDDEN_ROLE["proposal"] = "mahasiswa"
for t in ["anggota_tim", "logbook", "integrasi_kurikulum"]:
    FORBIDDEN_ROLE[t] = "mahasiswa"
for t in ["klirens_etik", "kontrak", "pencairan_dana"]:
    FORBIDDEN_ROLE[t] = "dosen"
for t in ["penilaian", "monev"]:
    FORBIDDEN_ROLE[t] = "dosen"
for t in ["publikasi", "hki", "laporan_akhir", "spj"]:
    FORBIDDEN_ROLE[t] = "mahasiswa"
FORBIDDEN_ROLE["produk_adopsi"] = "mahasiswa"
for t in ["sitasi", "rekognisi"]:
    FORBIDDEN_ROLE[t] = "mahasiswa"
for t in ["kerjasama", "survei_dampak"]:
    FORBIDDEN_ROLE[t] = "dosen"


def build_relasi_method(table, e):
    """Bangun method buatRelasiPendukung() yang membuat seluruh entitas FK
    yang diperlukan dan mengembalikan array relationships JSON:API."""
    if not e["foreign_keys"]:
        return ""

    lines = []
    use_lines = []
    rel_entries = []
    for fk in e["foreign_keys"]:
        ref = fk["references"]
        ref_model = ENTITIES[ref]["model"]
        ref_resource_type = ENTITIES[ref]["resource_type"]
        relation_name = fk.get("relation_name") or to_camel(ref)
        var = f"${relation_name}"
        lines.append(f"        {var} = \\App\\Models\\{ref_model}::factory()->create();")
        rel_entries.append(
            f"                '{relation_name}' => ['data' => ['type' => '{ref_resource_type}', 'id' => (string) {var}->id]],"
        )

    rel_body = "\n".join(rel_entries)
    body = "\n".join(lines)
    return f"""    /**
     * Buat entitas terkait (FK) yang diperlukan agar data
     * "{e['resource_type']}" valid, lalu kembalikan sebagai array
     * relationships JSON:API.
     */
    protected function relasiPendukung(): array
    {{
{body}

        return [
{rel_body}
        ];
    }}

"""


def build_attr_and_payload_method(table, e):
    attr_entries = []
    for col in e["columns"]:
        field_name = to_camel(col["name"])
        value = php_value_for(table, col)
        attr_entries.append(f"            '{field_name}' => {value},")
    attr_body = "\n".join(attr_entries)

    has_fk = bool(e["foreign_keys"])
    relationships_part = (
        "'relationships' => $this->relasiPendukung(),\n                "
        if has_fk else ""
    )

    return f"""    /**
     * Bangun dokumen JSON:API valid untuk permintaan create resource ini.
     */
    protected function payloadValid(): array
    {{
        return [
            'data' => [
                'type' => '{e["resource_type"]}',
                {relationships_part}'attributes' => [
{attr_body}
                ],
            ],
        ];
    }}

"""


DOC_BY_CATEGORY = {
    "masukan": " * Kategori: Fase Masukan (data master) -- baca terbuka, tulis hanya\n * oleh pengelola LPPM.",
    "proposal": " * Kategori: Proposal -- dikelola oleh dosen pengusul (draft) dan\n * pengelola LPPM.",
    "pengusul_kelola": " * Kategori: dikelola oleh dosen pengusul proposal terkait dan\n * pengelola LPPM.",
    "pengelola_only": " * Kategori: sepenuhnya dikelola oleh pengelola LPPM.",
    "reviewer": " * Kategori: dikelola oleh reviewer yang ditugaskan dan pengelola LPPM.",
    "luaran": " * Kategori: Fase Luaran -- dikelola oleh dosen pengusul dan\n * pengelola LPPM.",
    "produk_adopsi": " * Kategori: dikelola oleh dosen pengusul ATAU mitra eksternal, serta\n * pengelola LPPM.",
    "dampak_dosen": " * Kategori: Fase Dampak (milik dosen) -- dikelola oleh dosen pemilik\n * data dan pengelola LPPM.",
    "dampak_mitra": " * Kategori: Fase Dampak (milik mitra) -- dikelola oleh mitra eksternal\n * pemilik data dan pengelola LPPM.",
}

CATEGORY_OF = {}
for t in ["prodi", "dosen", "mahasiswa", "renstra", "peta_jalan", "pedoman",
          "sarana_prasarana", "skema_pendanaan", "mitra"]:
    CATEGORY_OF[t] = "masukan"
CATEGORY_OF["proposal"] = "proposal"
for t in ["anggota_tim", "logbook", "integrasi_kurikulum"]:
    CATEGORY_OF[t] = "pengusul_kelola"
for t in ["klirens_etik", "kontrak", "pencairan_dana"]:
    CATEGORY_OF[t] = "pengelola_only"
for t in ["penilaian", "monev"]:
    CATEGORY_OF[t] = "reviewer"
for t in ["publikasi", "hki", "laporan_akhir", "spj"]:
    CATEGORY_OF[t] = "luaran"
CATEGORY_OF["produk_adopsi"] = "produk_adopsi"
for t in ["sitasi", "rekognisi"]:
    CATEGORY_OF[t] = "dampak_dosen"
for t in ["kerjasama", "survei_dampak"]:
    CATEGORY_OF[t] = "dampak_mitra"


def write_test(table):
    e = ENTITIES[table]
    model = e["model"]
    resource_type = e["resource_type"]
    forbidden_role = FORBIDDEN_ROLE[table]
    forbidden_const = FORBIDDEN_ROLE_CONST[forbidden_role]
    category = CATEGORY_OF[table]
    doc = DOC_BY_CATEGORY[category]

    relasi_method = build_relasi_method(table, e)
    attr_method = build_attr_and_payload_method(table, e)

    use_models = ""  # model FK diakses via FQCN langsung, tidak perlu use tambahan

    content = HEADER_TMPL.format(
        use_models=use_models,
        resource_type=resource_type,
        label=e["label"],
        doc=doc,
        model=model,
        relasi_method=relasi_method,
        attr_method=attr_method,
        forbidden_role=forbidden_role,
        forbidden_const=forbidden_const,
    )

    with open(os.path.join(TEST_DIR, f"{model}Test.php"), "w") as f:
        f.write(content)
    print(f"  - {model}Test.php")


if __name__ == "__main__":
    print("Membuat Feature Test untuk seluruh 27 entitas...")
    for t in ORDER:
        write_test(t)
    print(f"Selesai. Total Feature Test dibuat: {len(ORDER)}")
