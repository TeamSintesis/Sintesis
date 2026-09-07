#!/usr/bin/env python3
"""
Generator kode boilerplate untuk backend SIMPPM.
Membaca spec/entities.json lalu menghasilkan:
- database/migrations/*.php
- app/Models/*.php
- database/factories/*Factory.php

Skrip ini HANYA digunakan sekali saat pembuatan prototipe untuk menjaga
konsistensi penamaan di 27 entitas. Bukan bagian dari runtime aplikasi.
"""
import json
import os
import re
from datetime import datetime, timedelta

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

with open(os.path.join(BASE, "spec", "entities.json")) as f:
    SPEC = json.load(f)

ENTITIES = {e["table"]: e for e in SPEC["entities"]}


# ---------------------------------------------------------------------------
# Peta kepemilikan (ownership) data oleh dosen, digunakan untuk membangkitkan
# metode pemilikDosenId() pada model yang relevan (dipakai oleh Policy).
# ---------------------------------------------------------------------------
DIRECT_OWNER_COLUMN = {
    "proposal": "dosen_id",
    "sitasi": "dosen_id",
    "rekognisi": "dosen_id",
}
OWNER_VIA_PROPOSAL = {
    "anggota_tim", "penilaian", "klirens_etik", "kontrak", "logbook",
    "monev", "integrasi_kurikulum", "publikasi", "hki", "produk_adopsi",
    "laporan_akhir",
}
OWNER_VIA_KONTRAK = {"pencairan_dana"}
OWNER_VIA_LAPORAN_AKHIR = {"spj"}

# Peta kepemilikan oleh mitra eksternal (kolom mitra_id langsung tersedia
# pada seluruh entitas berikut).
DIRECT_OWNER_MITRA_COLUMN = {
    "produk_adopsi": "mitra_id",
    "kerjasama": "mitra_id",
    "survei_dampak": "mitra_id",
}


def to_snake(name):
    return re.sub(r'(?<!^)(?=[A-Z])', '_', name).lower()


def topo_sort(entities):
    """Urutkan entitas berdasarkan dependensi foreign key agar migrasi tidak gagal."""
    order = []
    visited = set()

    def visit(table):
        if table in visited:
            return
        visited.add(table)
        for fk in entities[table]["foreign_keys"]:
            ref = fk["references"]
            if ref in entities:
                visit(ref)
        order.append(table)

    for t in entities:
        visit(t)
    return order


ORDER = topo_sort(ENTITIES)

# ---------------------------------------------------------------------------
# 1. MIGRATIONS
# ---------------------------------------------------------------------------
MIG_DIR = os.path.join(BASE, "database", "migrations")
os.makedirs(MIG_DIR, exist_ok=True)

base_time = datetime(2026, 1, 1, 0, 0, 0)


def col_definition(col):
    name = col["name"]
    t = col["type"]
    lines = []
    if t == "string":
        length = col.get("length", 255)
        lines.append(f"$table->string('{name}', {length})")
    elif t == "text":
        lines.append(f"$table->text('{name}')")
    elif t == "integer":
        lines.append(f"$table->integer('{name}')")
    elif t == "year":
        lines.append(f"$table->unsignedSmallInteger('{name}')")
    elif t == "date":
        lines.append(f"$table->date('{name}')")
    elif t == "boolean":
        lines.append(f"$table->boolean('{name}')")
    elif t == "decimal":
        p = col.get("precision", 15)
        s = col.get("scale", 2)
        lines.append(f"$table->decimal('{name}', {p}, {s})")
    elif t == "enum":
        values = ", ".join(f"'{v}'" for v in col["values"])
        lines.append(f"$table->enum('{name}', [{values}])")
    else:
        raise ValueError(f"Tipe kolom tidak dikenal: {t}")

    line = lines[0]
    if col.get("default") is not None:
        d = col["default"]
        if isinstance(d, bool):
            d = "true" if d else "false"
        elif isinstance(d, str):
            d = f"'{d}'"
        line += f"->default({d})"
    if col.get("nullable"):
        line += "->nullable()"
    if col.get("unique"):
        line += "->unique()"
    return line + ";"


def fk_definition(fk):
    col = fk["column"]
    ref_table = fk["references"]
    line = f"$table->foreignId('{col}')"
    if fk.get("nullable"):
        line += "->nullable()"
    line += f"->constrained('{ref_table}')"
    if fk.get("nullable"):
        line += "->nullOnDelete()"
    else:
        line += "->cascadeOnDelete()"
    return line + ";"


for i, table in enumerate(ORDER):
    e = ENTITIES[table]
    ts = (base_time + timedelta(seconds=i)).strftime("%Y_%m_%d_%H%M%S")
    filename = f"{ts}_create_{table}_table.php"
    body_lines = ["            $table->id();"]
    for fk in e["foreign_keys"]:
        note = fk.get("note", "")
        if note:
            body_lines.append(f"            // {note}")
        body_lines.append("            " + fk_definition(fk))
    for col in e["columns"]:
        note = col.get("note", "")
        if note:
            body_lines.append(f"            // {note}")
        body_lines.append("            " + col_definition(col))
    body_lines.append("            $table->timestamps();")
    body = "\n".join(body_lines)

    label = e["label"]
    content = f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

// Migrasi tabel {table}: {label}
// Dibuat otomatis dari kamus data SIMPPM (fase {e['phase']}).
return new class extends Migration
{{
    /**
     * Menjalankan migrasi untuk membuat tabel {table}.
     */
    public function up(): void
    {{
        Schema::create('{table}', function (Blueprint $table) {{
{body}
        }});
    }}

    /**
     * Membatalkan migrasi dengan menghapus tabel {table}.
     */
    public function down(): void
    {{
        Schema::dropIfExists('{table}');
    }}
}};
"""
    with open(os.path.join(MIG_DIR, filename), "w") as f:
        f.write(content)

print(f"Berhasil membuat {len(ORDER)} berkas migrasi.")

# ---------------------------------------------------------------------------
# 2. MODELS
# ---------------------------------------------------------------------------
MODEL_DIR = os.path.join(BASE, "app", "Models")
os.makedirs(MODEL_DIR, exist_ok=True)

# Kumpulkan relasi balik (hasMany) untuk setiap tabel yang dirujuk
reverse_relations = {t: [] for t in ENTITIES}
for table, e in ENTITIES.items():
    for fk in e["foreign_keys"]:
        ref = fk["references"]
        if ref in reverse_relations:
            reverse_relations[ref].append((table, fk))


def relation_method_name(fk):
    if fk.get("relation_name"):
        return fk["relation_name"]
    ref = fk["references"]
    model = ENTITIES[ref]["model"]
    return model[0].lower() + model[1:]


def cast_for(col):
    t = col["type"]
    if t == "decimal":
        return f"'{col['name']}' => 'decimal:{col.get('scale', 2)}'"
    if t == "boolean":
        return f"'{col['name']}' => 'boolean'"
    if t == "date":
        return f"'{col['name']}' => 'date'"
    return None


for table in ORDER:
    e = ENTITIES[table]
    model = e["model"]
    fillable = [f"'{fk['column']}'" for fk in e["foreign_keys"]]
    fillable += [f"'{c['name']}'" for c in e["columns"]]
    fillable_str = ",\n        ".join(fillable)

    casts = [cast_for(c) for c in e["columns"]]
    casts = [c for c in casts if c]
    casts_str = ",\n            ".join(casts)

    rel_methods = []
    for fk in e["foreign_keys"]:
        ref = fk["references"]
        ref_model = ENTITIES[ref]["model"]
        method = relation_method_name(fk)
        rel_methods.append(f"""
    /**
     * Relasi ke {ENTITIES[ref]['label']} (belongsTo).
     */
    public function {method}(): \\Illuminate\\Database\\Eloquent\\Relations\\BelongsTo
    {{
        return $this->belongsTo({ref_model}::class, '{fk['column']}');
    }}""")

    for child_table, fk in reverse_relations[table]:
        child_model = ENTITIES[child_table]["model"]
        method = child_model[0].lower() + child_model[1:] + "List"
        rel_methods.append(f"""
    /**
     * Relasi ke daftar {ENTITIES[child_table]['label']} terkait (hasMany).
     */
    public function {method}(): \\Illuminate\\Database\\Eloquent\\Relations\\HasMany
    {{
        return $this->hasMany({child_model}::class, '{fk['column']}');
    }}""")

    # ------------------------------------------------------------------
    # Metode pemilikDosenId(): menelusuri rantai relasi hingga menemukan
    # dosen pemilik data (pengusul), digunakan oleh Policy untuk otorisasi
    # berbasis kepemilikan (bukan sekadar peran).
    # ------------------------------------------------------------------
    owner_method = None
    if table in DIRECT_OWNER_COLUMN:
        col = DIRECT_OWNER_COLUMN[table]
        owner_method = f"""
    /**
     * Kembalikan ID dosen pemilik/pengusul data ini, untuk keperluan
     * otorisasi berbasis kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {{
        return $this->{col};
    }}"""
    elif table in OWNER_VIA_PROPOSAL:
        owner_method = """
    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi proposal), untuk keperluan otorisasi berbasis kepemilikan
     * pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->proposal?->dosen_id;
    }"""
    elif table in OWNER_VIA_KONTRAK:
        owner_method = """
    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi kontrak -> proposal), untuk keperluan otorisasi berbasis
     * kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->kontrak?->proposal?->dosen_id;
    }"""
    elif table in OWNER_VIA_LAPORAN_AKHIR:
        owner_method = """
    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi laporanAkhir -> proposal), untuk keperluan otorisasi
     * berbasis kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->laporanAkhir?->proposal?->dosen_id;
    }"""

    if owner_method:
        rel_methods.append(owner_method)

    if table in DIRECT_OWNER_MITRA_COLUMN:
        mitra_col = DIRECT_OWNER_MITRA_COLUMN[table]
        rel_methods.append(f"""
    /**
     * Kembalikan ID mitra eksternal pemilik data ini, untuk keperluan
     * otorisasi berbasis kepemilikan pada Policy.
     */
    public function pemilikMitraId(): ?int
    {{
        return $this->{mitra_col};
    }}""")

    rel_code = "\n".join(rel_methods)

    casts_block = ""
    if casts_str:
        casts_block = f"""
    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            {casts_str}
    ];
"""

    content = f"""<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;

/**
 * Model {model} ({e['label']}).
 *
 * Merepresentasikan entitas {table} pada fase {e['phase']} SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class {model} extends Model
{{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = '{table}';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        {fillable_str}
    ];
{casts_block}{rel_code}
}}
"""
    with open(os.path.join(MODEL_DIR, f"{model}.php"), "w") as f:
        f.write(content)

print(f"Berhasil membuat {len(ORDER)} berkas model.")

# ---------------------------------------------------------------------------
# 3. FACTORIES
# ---------------------------------------------------------------------------
FACTORY_DIR = os.path.join(BASE, "database", "factories")
os.makedirs(FACTORY_DIR, exist_ok=True)


def faker_for(col, table=None):
    """Petakan kolom ke ekspresi Faker yang realistis (locale id_ID)."""
    name = col["name"]
    t = col["type"]

    # Kolom 'status' dan 'berisiko_etik' pada Proposal sengaja TIDAK
    # diacak: Proposal dijadikan entitas induk (FK) oleh banyak entitas
    # lain (Kontrak, Logbook, Penilaian, dst). Jika nilai bawaan acak
    # kebetulan menghasilkan status lanjutan (mis. 'direview') sekaligus
    # berisiko_etik=true, ProposalObserver akan menjalankan aturan bisnis
    # (klirens etik/penilaian) pada saat pembuatan data induk yang tidak
    # relevan, menyebabkan efek samping tak terduga di seluruh test lain.
    # Skenario status/etik khusus tersedia lewat factory state
    # (lihat diajukan()/direview()/berisikoEtik() di ProposalFactory).
    if table == "proposal" and name == "status":
        return "'draft'"
    if table == "proposal" and name == "berisiko_etik":
        return "false"

    if t == "enum":
        values = ", ".join(f"'{v}'" for v in col["values"])
        return f"fake()->randomElement([{values}])"
    if t == "year":
        return "fake()->numberBetween(2022, 2027)"
    if t == "date":
        return "fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d')"
    if t == "boolean":
        return "fake()->boolean(20)"
    if t == "text":
        return "fake()->realText(200)"
    if t == "integer":
        return "fake()->numberBetween(1, 5)"
    if t == "decimal":
        p = col.get("precision", 15)
        max_val = min(10 ** (p - 2) - 1, 500_000_000)
        return f"fake()->numberBetween(1_000_000, {max_val})"
    if t == "string":
        if name == "nidn":
            return "fake()->unique()->numerify('##########')"
        if name == "nim":
            return "fake()->unique()->numerify('###############')"
        if name in ("id_sinta", "scopus_id", "orcid", "doi"):
            return "fake()->unique()->numerify('##########')"
        if "nama" in name:
            return "fake()->name()"
        if "judul" in name:
            return "fake()->sentence(8)"
        if "url" in name or "tautan" in name or "bukti" in name:
            return "fake()->url()"
        if "nomor" in name:
            return "fake()->unique()->numerify('###/UN/" + name.upper() + "/####')"
        if "kontak" in name:
            return "fake()->phoneNumber()"
        if "alamat" in name:
            return "fake()->address()"
        if "email" in name:
            return "fake()->safeEmail()"
        if name in ("periode", "versi"):
            return "fake()->bothify('v#.#')"
        length = col.get("length", 50)
        return f"fake()->text({min(length, 60)})"
    return "fake()->word()"


for table in ORDER:
    e = ENTITIES[table]
    model = e["model"]

    fk_lines = []
    for fk in e["foreign_keys"]:
        ref = fk["references"]
        ref_model = ENTITIES[ref]["model"]
        if fk.get("nullable"):
            fk_lines.append(
                f"            '{fk['column']}' => {ref_model}::factory(),"
            )
        else:
            fk_lines.append(
                f"            '{fk['column']}' => {ref_model}::factory(),"
            )

    col_lines = []
    for col in e["columns"]:
        expr = faker_for(col, table)
        col_lines.append(f"            '{col['name']}' => {expr},")

    body = "\n".join(fk_lines + col_lines)

    referenced_models = sorted({
        ENTITIES[fk["references"]]["model"] for fk in e["foreign_keys"]
    } - {model})
    extra_imports = "\n".join(
        f"use App\\Models\\{m};" for m in referenced_models
    )
    if extra_imports:
        extra_imports = "\n" + extra_imports

    # Proposal butuh factory state eksplisit (bukan nilai acak) untuk
    # skenario status/berisiko_etik khusus, karena nilai bawaannya
    # sengaja dibuat aman ('draft'/false) -- lihat catatan pada
    # faker_for() di atas.
    proposal_states = ""
    if table == "proposal":
        proposal_states = """

    /**
     * State: Proposal telah diajukan (melewati tahap draft).
     */
    public function diajukan(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'diajukan']);
    }

    /**
     * State: Proposal sedang direview.
     */
    public function direview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'direview']);
    }

    /**
     * State: Proposal ditandai berisiko etik (memerlukan Klirens Etik).
     */
    public function berisikoEtik(): static
    {
        return $this->state(fn (array $attributes) => ['berisiko_etik' => true]);
    }"""

    content = f"""<?php

namespace Database\\Factories;

use App\\Models\\{model};{extra_imports}
use Illuminate\\Database\\Eloquent\\Factories\\Factory;

/**
 * Factory untuk model {model} ({e['label']}).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<{model}>
 */
class {model}Factory extends Factory
{{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<{model}>
     */
    protected $model = {model}::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {{
        return [
{body}
        ];
    }}{proposal_states}
}}
"""
    with open(os.path.join(FACTORY_DIR, f"{model}Factory.php"), "w") as f:
        f.write(content)

print(f"Berhasil membuat {len(ORDER)} berkas factory.")
