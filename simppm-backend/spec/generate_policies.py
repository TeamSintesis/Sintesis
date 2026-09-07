#!/usr/bin/env python3
"""
Generator kelas Policy (app/Policies/*.php) untuk seluruh 27 model SIMPPM.

Aturan RBAC diturunkan dari 8 aktor pada Use_Case_Diagram_SIMPPM.md dan
disusun per fase (Masukan/Proses/Luaran/Dampak) sesuai catatan desain pada
sesi sebelumnya. Dibuat sekali sebagai skrip generator (bukan dijalankan
berulang di CI) karena aturan per-model cukup spesifik untuk ditulis
manual per kategori, bukan diturunkan murni dari entities.json.
"""
import os

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
POLICY_DIR = os.path.join(BASE, "app", "Policies")
os.makedirs(POLICY_DIR, exist_ok=True)

HEADER = """<?php

namespace App\\Policies;

use App\\Models\\{model};
use App\\Models\\User;
use App\\Policies\\Concerns\\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model {model} ({label}).
 *
{doc}
 */
class {model}Policy
{{
    use BantuanOtorisasi;
{body}
}}
"""

# ---------------------------------------------------------------------------
# KATEGORI 1: FASE MASUKAN (data master/referensi)
# Baca terbuka untuk seluruh pengguna terautentikasi; tulis hanya oleh
# pengelola LPPM (admin_lppm/ketua_lppm).
# ---------------------------------------------------------------------------
MASUKAN_DOC = (
    " * Fase Masukan: data master/referensi bersifat baca-terbuka bagi\n"
    " * seluruh pengguna terautentikasi, sedangkan penulisan (create/\n"
    " * update/delete) dibatasi hanya untuk pengelola LPPM (admin_lppm\n"
    " * atau ketua_lppm)."
)
MASUKAN_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar data master.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua pengguna terautentikasi boleh melihat detail data master.
     */
    public function view(User $user, {model} ${var}): bool
    {
        return true;
    }

    /**
     * Hanya pengelola LPPM yang boleh menambah data master baru.
     */
    public function create(User $user): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh mengubah data master.
     */
    public function update(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data master.
     */
    public function delete(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user);
    }
"""

MASUKAN_TABLES = [
    "prodi", "dosen", "mahasiswa", "renstra", "peta_jalan",
    "pedoman", "sarana_prasarana", "skema_pendanaan", "mitra",
]

# ---------------------------------------------------------------------------
# KATEGORI 2: PROPOSAL (kepemilikan dosen + transisi status draft)
# ---------------------------------------------------------------------------
PROPOSAL_DOC = (
    " * Fase Proses (Proposal): dosen pengusul mengelola proposal miliknya\n"
    " * sendiri selama masih berstatus draft; setelah diajukan, hanya\n"
    " * pengelola LPPM yang dapat mengubah/menghapusnya (misalnya untuk\n"
    " * memproses review). Peran pemantau (prodi_gkm/pimpinan) dan reviewer\n"
    " * memiliki akses baca penuh untuk keperluan monitoring & evaluasi."
)
PROPOSAL_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar proposal
     * (dosen hanya akan melihat detail miliknya sendiri saat membuka
     * satu proposal, lihat method view()).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Pengusul, pengelola LPPM, reviewer, dan pemantau boleh melihat
     * detail satu proposal.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $user->memilikiPeran(User::PERAN_REVIEWER)
            || $this->adalahPemilikDosen($user, $proposal);
    }

    /**
     * Dosen boleh mengajukan proposal baru; pengelola LPPM juga dapat
     * menambahkan proposal atas nama dosen jika diperlukan.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_DOSEN) || $this->adalahPengelola($user);
    }

    /**
     * Pengusul hanya dapat mengubah proposal miliknya selama masih
     * berstatus draft. Pengelola LPPM dapat mengubah proposal kapan pun
     * (misalnya untuk memproses transisi status review).
     */
    public function update(User $user, Proposal $proposal): bool
    {
        if ($this->adalahPengelola($user)) {
            return true;
        }

        return $this->adalahPemilikDosen($user, $proposal) && $proposal->status === 'draft';
    }

    /**
     * Pengusul hanya dapat menghapus proposal miliknya selama masih
     * berstatus draft. Pengelola LPPM dapat menghapus kapan pun.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        if ($this->adalahPengelola($user)) {
            return true;
        }

        return $this->adalahPemilikDosen($user, $proposal) && $proposal->status === 'draft';
    }
"""

# ---------------------------------------------------------------------------
# KATEGORI 3: Entitas turunan proposal yang dikelola oleh PENGUSUL DOSEN
# (AnggotaTim, Logbook) — dosen pengusul + pengelola LPPM dapat mengelola;
# pemantau hanya baca.
# ---------------------------------------------------------------------------
PENGUSUL_KELOLA_DOC_TMPL = (
    " * Fase Proses ({label}): dikelola oleh dosen pengusul proposal\n"
    " * terkait (ditelusuri melalui relasi proposal) dan oleh pengelola\n"
    " * LPPM. Peran pemantau (prodi_gkm/pimpinan) hanya memiliki akses\n"
    " * baca untuk keperluan monitoring."
)
PENGUSUL_KELOLA_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar data.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Dosen pengusul proposal terkait, pengelola LPPM, dan pemantau boleh
     * melihat detail data.
     */
    public function view(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, ${var});
    }

    /**
     * Dosen (untuk data miliknya) atau pengelola LPPM boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_DOSEN) || $this->adalahPengelola($user);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh mengubah.
     */
    public function update(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, ${var});
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh menghapus.
     */
    public function delete(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, ${var});
    }
"""

PENGUSUL_KELOLA_TABLES = ["anggota_tim", "logbook", "integrasi_kurikulum"]

# ---------------------------------------------------------------------------
# KATEGORI 4: PENGELOLA-ONLY (KlirensEtik, Kontrak, PencairanDana) — dosen
# pengusul hanya baca, seluruh tulis oleh pengelola LPPM.
# ---------------------------------------------------------------------------
PENGELOLA_ONLY_DOC_TMPL = (
    " * Fase Proses ({label}): sepenuhnya dikelola (create/update/delete)\n"
    " * oleh pengelola LPPM (admin_lppm/ketua_lppm). Dosen pengusul\n"
    " * proposal terkait dan peran pemantau hanya memiliki akses baca."
)
PENGELOLA_ONLY_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar data.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Dosen pengusul proposal terkait, pengelola LPPM, dan pemantau boleh
     * melihat detail data.
     */
    public function view(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, ${var});
    }

    /**
     * Hanya pengelola LPPM yang boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh mengubah data.
     */
    public function update(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data.
     */
    public function delete(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user);
    }
"""

PENGELOLA_ONLY_TABLES = ["klirens_etik", "kontrak", "pencairan_dana"]

# ---------------------------------------------------------------------------
# KATEGORI 5: REVIEWER-MANAGED (Penilaian, Monev) — reviewer yang ditugaskan
# (dosen_id = reviewer, BUKAN pengusul) mengelola data; pengusul proposal
# hanya baca.
# ---------------------------------------------------------------------------
REVIEWER_DOC_TMPL = (
    " * Fase Proses ({label}): dikelola oleh reviewer yang ditugaskan\n"
    " * (kolom dosen_id merepresentasikan reviewer, bukan pengusul\n"
    " * proposal) serta oleh pengelola LPPM. Dosen pengusul proposal\n"
    " * terkait dan peran pemantau hanya memiliki akses baca."
)
REVIEWER_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar data.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Reviewer yang ditugaskan, dosen pengusul proposal terkait,
     * pengelola LPPM, dan pemantau boleh melihat detail data.
     */
    public function view(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahReviewerYangDitugaskan($user, ${var})
            || $this->adalahPemilikDosen($user, ${var});
    }

    /**
     * Reviewer atau pengelola LPPM boleh menambah data penilaian/monev.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_REVIEWER) || $this->adalahPengelola($user);
    }

    /**
     * Hanya reviewer yang ditugaskan pada data tersebut atau pengelola
     * LPPM yang boleh mengubahnya.
     */
    public function update(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user) || $this->adalahReviewerYangDitugaskan($user, ${var});
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data penilaian/monev.
     */
    public function delete(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user);
    }
"""

REVIEWER_TABLES = ["penilaian", "monev"]

# ---------------------------------------------------------------------------
# KATEGORI 6: FASE LUARAN (Publikasi, Hki, LaporanAkhir, Spj) — dikelola
# oleh dosen pengusul + pengelola LPPM (untuk verifikasi).
# ---------------------------------------------------------------------------
LUARAN_DOC_TMPL = (
    " * Fase Luaran ({label}): dikelola oleh dosen pengusul proposal\n"
    " * terkait (sebagai pelapor luaran) dan diverifikasi oleh pengelola\n"
    " * LPPM. Peran pemantau (prodi_gkm/pimpinan) memiliki akses baca\n"
    " * untuk keperluan evaluasi capaian."
)
LUARAN_BODY = PENGUSUL_KELOLA_BODY  # aturan sama dengan kategori 3
LUARAN_TABLES = ["publikasi", "hki", "laporan_akhir", "spj"]

# ---------------------------------------------------------------------------
# KATEGORI 7: PRODUK ADOPSI (dosen pengusul ATAU mitra dapat mengelola,
# karena adopsi produk sering dikonfirmasi oleh mitra penerima).
# ---------------------------------------------------------------------------
PRODUK_ADOPSI_DOC = (
    " * Fase Luaran (Produk Diadopsi Mitra): dikelola oleh dosen pengusul\n"
    " * proposal terkait ATAU mitra eksternal penerima produk, serta oleh\n"
    " * pengelola LPPM. Peran pemantau memiliki akses baca."
)
PRODUK_ADOPSI_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar data.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Dosen pengusul, mitra penerima, pengelola LPPM, dan pemantau boleh
     * melihat detail data.
     */
    public function view(User $user, ProdukAdopsi $produkAdopsi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $produkAdopsi)
            || $this->adalahPemilikMitra($user, $produkAdopsi);
    }

    /**
     * Dosen, mitra eksternal, atau pengelola LPPM boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_DOSEN, User::PERAN_MITRA_EKSTERNAL)
            || $this->adalahPengelola($user);
    }

    /**
     * Dosen pengusul, mitra penerima, atau pengelola LPPM boleh mengubah.
     */
    public function update(User $user, ProdukAdopsi $produkAdopsi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemilikDosen($user, $produkAdopsi)
            || $this->adalahPemilikMitra($user, $produkAdopsi);
    }

    /**
     * Hanya dosen pengusul atau pengelola LPPM yang boleh menghapus.
     */
    public function delete(User $user, ProdukAdopsi $produkAdopsi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $produkAdopsi);
    }
"""

# ---------------------------------------------------------------------------
# KATEGORI 8: FASE DAMPAK milik DOSEN (Sitasi, Rekognisi)
# ---------------------------------------------------------------------------
DAMPAK_DOSEN_DOC_TMPL = (
    " * Fase Dampak ({label}): dikelola oleh dosen pemilik data secara\n"
    " * langsung serta oleh pengelola LPPM. Peran pemantau memiliki akses\n"
    " * baca untuk keperluan evaluasi dampak jangka panjang."
)
DAMPAK_DOSEN_BODY = PENGUSUL_KELOLA_BODY  # pola sama: cek pemilikDosenId()
DAMPAK_DOSEN_TABLES = ["sitasi", "rekognisi"]

# ---------------------------------------------------------------------------
# KATEGORI 9: FASE DAMPAK milik MITRA (Kerjasama, SurveiDampak)
# ---------------------------------------------------------------------------
DAMPAK_MITRA_DOC_TMPL = (
    " * Fase Dampak ({label}): diisi/dikelola oleh mitra eksternal\n"
    " * pemilik data serta oleh pengelola LPPM. Peran pemantau memiliki\n"
    " * akses baca untuk keperluan evaluasi dampak kerja sama."
)
DAMPAK_MITRA_BODY = """
    /**
     * Semua pengguna terautentikasi boleh melihat daftar data.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Mitra pemilik data, pengelola LPPM, dan pemantau boleh melihat
     * detail data.
     */
    public function view(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikMitra($user, ${var});
    }

    /**
     * Mitra eksternal atau pengelola LPPM boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_MITRA_EKSTERNAL) || $this->adalahPengelola($user);
    }

    /**
     * Mitra pemilik data atau pengelola LPPM boleh mengubah.
     */
    public function update(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikMitra($user, ${var});
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data mitra/dampak.
     */
    public function delete(User $user, {model} ${var}): bool
    {
        return $this->adalahPengelola($user);
    }
"""

DAMPAK_MITRA_TABLES = ["kerjasama", "survei_dampak"]


def var_name(model: str) -> str:
    return model[0].lower() + model[1:]


def write_policy(table_to_model: dict, table: str, label: str, doc: str, body_tmpl: str):
    model = table_to_model[table]
    var = var_name(model)
    body = body_tmpl.replace("{model}", model).replace("{var}", var)
    content = HEADER.format(model=model, label=label, doc=doc, body=body)
    with open(os.path.join(POLICY_DIR, f"{model}Policy.php"), "w") as f:
        f.write(content)
    print(f"  - {model}Policy.php")


if __name__ == "__main__":
    import json

    spec = json.load(open(os.path.join(BASE, "spec", "entities.json")))
    entities = {e["table"]: e for e in spec["entities"]}
    table_to_model = {t: e["model"] for t, e in entities.items()}

    print("Membuat Policy Fase Masukan...")
    for t in MASUKAN_TABLES:
        write_policy(table_to_model, t, entities[t]["label"], MASUKAN_DOC, MASUKAN_BODY)

    print("Membuat Policy Proposal...")
    write_policy(table_to_model, "proposal", entities["proposal"]["label"], PROPOSAL_DOC, PROPOSAL_BODY)

    print("Membuat Policy dikelola pengusul (AnggotaTim/Logbook/IntegrasiKurikulum)...")
    for t in PENGUSUL_KELOLA_TABLES:
        doc = PENGUSUL_KELOLA_DOC_TMPL.format(label=entities[t]["label"])
        write_policy(table_to_model, t, entities[t]["label"], doc, PENGUSUL_KELOLA_BODY)

    print("Membuat Policy khusus pengelola (KlirensEtik/Kontrak/PencairanDana)...")
    for t in PENGELOLA_ONLY_TABLES:
        doc = PENGELOLA_ONLY_DOC_TMPL.format(label=entities[t]["label"])
        write_policy(table_to_model, t, entities[t]["label"], doc, PENGELOLA_ONLY_BODY)

    print("Membuat Policy reviewer (Penilaian/Monev)...")
    for t in REVIEWER_TABLES:
        doc = REVIEWER_DOC_TMPL.format(label=entities[t]["label"])
        write_policy(table_to_model, t, entities[t]["label"], doc, REVIEWER_BODY)

    print("Membuat Policy Fase Luaran (Publikasi/Hki/LaporanAkhir/Spj)...")
    for t in LUARAN_TABLES:
        doc = LUARAN_DOC_TMPL.format(label=entities[t]["label"])
        write_policy(table_to_model, t, entities[t]["label"], doc, LUARAN_BODY)

    print("Membuat Policy ProdukAdopsi...")
    write_policy(table_to_model, "produk_adopsi", entities["produk_adopsi"]["label"], PRODUK_ADOPSI_DOC, PRODUK_ADOPSI_BODY)

    print("Membuat Policy Fase Dampak milik dosen (Sitasi/Rekognisi)...")
    for t in DAMPAK_DOSEN_TABLES:
        doc = DAMPAK_DOSEN_DOC_TMPL.format(label=entities[t]["label"])
        write_policy(table_to_model, t, entities[t]["label"], doc, DAMPAK_DOSEN_BODY)

    print("Membuat Policy Fase Dampak milik mitra (Kerjasama/SurveiDampak)...")
    for t in DAMPAK_MITRA_TABLES:
        doc = DAMPAK_MITRA_DOC_TMPL.format(label=entities[t]["label"])
        write_policy(table_to_model, t, entities[t]["label"], doc, DAMPAK_MITRA_BODY)

    total = (
        len(MASUKAN_TABLES) + 1 + len(PENGUSUL_KELOLA_TABLES)
        + len(PENGELOLA_ONLY_TABLES) + len(REVIEWER_TABLES)
        + len(LUARAN_TABLES) + 1 + len(DAMPAK_DOSEN_TABLES) + len(DAMPAK_MITRA_TABLES)
    )
    print(f"Selesai. Total Policy dibuat: {total}")
