// Berkas ini DIBUAT OTOMATIS oleh spec/generate_frontend_meta.py di
// proyek backend (simppm-backend) -- JANGAN diedit manual.
// Sumber kebenaran: simppm-backend/spec/entities.json.
//
// Untuk memperbarui berkas ini setelah spec/entities.json berubah:
//   cd simppm-backend && python3 spec/generate_frontend_meta.py
//
// Metadata ini mendeskripsikan seluruh 27 entitas SIMPPM (atribut,
// tipe data, dan relasi) sehingga komponen frontend generik
// (ResourceList.svelte, ResourceForm.svelte) dapat merender
// tabel & form untuk SETIAP entitas tanpa kode khusus per-entitas.

/** Label tampilan untuk setiap fase siklus PPM. */
export const PHASE_LABELS = {
  "masukan": "Masukan",
  "proses": "Proses",
  "luaran": "Luaran/Capaian",
  "dampak": "Dampak"
};

/** Urutan fase sesuai siklus Masukan -> Proses -> Luaran -> Dampak. */
export const PHASE_ORDER = ["masukan", "proses", "luaran", "dampak"];

/** Metadata seluruh entitas: atribut, tipe, dan relasi. */
export const ENTITIES = [
  {
    "resourceType": "prodi",
    "model": "Prodi",
    "label": "Program Studi",
    "phase": "masukan",
    "attributes": [
      {
        "name": "nama_prodi",
        "field": "namaProdi",
        "type": "string",
        "nullable": false,
        "label": "Nama prodi",
        "length": 100
      },
      {
        "name": "fakultas",
        "field": "fakultas",
        "type": "string",
        "nullable": true,
        "label": "Fakultas",
        "length": 100
      }
    ],
    "relations": []
  },
  {
    "resourceType": "dosen",
    "model": "Dosen",
    "label": "Dosen/Peneliti",
    "phase": "masukan",
    "attributes": [
      {
        "name": "nidn",
        "field": "nidn",
        "type": "string",
        "nullable": false,
        "label": "Nidn",
        "length": 10
      },
      {
        "name": "nama",
        "field": "nama",
        "type": "string",
        "nullable": false,
        "label": "Nama",
        "length": 100
      },
      {
        "name": "jabatan_fungsional",
        "field": "jabatanFungsional",
        "type": "string",
        "nullable": true,
        "label": "Jabatan fungsional",
        "length": 50
      },
      {
        "name": "bidang_kepakaran",
        "field": "bidangKepakaran",
        "type": "string",
        "nullable": true,
        "label": "Bidang kepakaran",
        "length": 150
      },
      {
        "name": "id_sinta",
        "field": "idSinta",
        "type": "string",
        "nullable": true,
        "label": "Id sinta",
        "length": 20
      },
      {
        "name": "scopus_id",
        "field": "scopusId",
        "type": "string",
        "nullable": true,
        "label": "Scopus id",
        "length": 20
      },
      {
        "name": "orcid",
        "field": "orcid",
        "type": "string",
        "nullable": true,
        "label": "Orcid",
        "length": 25
      }
    ],
    "relations": [
      {
        "field": "prodi",
        "column": "prodi_id",
        "resourceType": "prodi",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "mahasiswa",
    "model": "Mahasiswa",
    "label": "Mahasiswa",
    "phase": "masukan",
    "attributes": [
      {
        "name": "nim",
        "field": "nim",
        "type": "string",
        "nullable": false,
        "label": "Nim",
        "length": 15
      },
      {
        "name": "nama",
        "field": "nama",
        "type": "string",
        "nullable": false,
        "label": "Nama",
        "length": 100
      }
    ],
    "relations": [
      {
        "field": "prodi",
        "column": "prodi_id",
        "resourceType": "prodi",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "renstra",
    "model": "Renstra",
    "label": "Rencana Strategis",
    "phase": "masukan",
    "attributes": [
      {
        "name": "jenis",
        "field": "jenis",
        "type": "enum",
        "nullable": false,
        "label": "Jenis",
        "values": [
          "penelitian",
          "pkm"
        ]
      },
      {
        "name": "periode_mulai",
        "field": "periodeMulai",
        "type": "year",
        "nullable": false,
        "label": "Periode mulai"
      },
      {
        "name": "periode_akhir",
        "field": "periodeAkhir",
        "type": "year",
        "nullable": false,
        "label": "Periode akhir"
      },
      {
        "name": "dokumen_url",
        "field": "dokumenUrl",
        "type": "string",
        "nullable": true,
        "label": "Dokumen url",
        "length": 255
      }
    ],
    "relations": []
  },
  {
    "resourceType": "peta-jalan",
    "model": "PetaJalan",
    "label": "Peta Jalan Penelitian/PkM",
    "phase": "masukan",
    "attributes": [
      {
        "name": "bidang_keilmuan",
        "field": "bidangKeilmuan",
        "type": "string",
        "nullable": false,
        "label": "Bidang keilmuan",
        "length": 150
      },
      {
        "name": "tahun",
        "field": "tahun",
        "type": "year",
        "nullable": false,
        "label": "Tahun"
      }
    ],
    "relations": [
      {
        "field": "renstra",
        "column": "renstra_id",
        "resourceType": "renstra",
        "nullable": false
      },
      {
        "field": "prodi",
        "column": "prodi_id",
        "resourceType": "prodi",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "pedoman",
    "model": "Pedoman",
    "label": "Pedoman dan Kode Etik",
    "phase": "masukan",
    "attributes": [
      {
        "name": "jenis",
        "field": "jenis",
        "type": "string",
        "nullable": false,
        "label": "Jenis",
        "length": 50
      },
      {
        "name": "versi",
        "field": "versi",
        "type": "string",
        "nullable": false,
        "label": "Versi",
        "length": 20
      },
      {
        "name": "tanggal_berlaku",
        "field": "tanggalBerlaku",
        "type": "date",
        "nullable": false,
        "label": "Tanggal berlaku"
      },
      {
        "name": "dokumen_url",
        "field": "dokumenUrl",
        "type": "string",
        "nullable": true,
        "label": "Dokumen url",
        "length": 255
      }
    ],
    "relations": []
  },
  {
    "resourceType": "sarana-prasarana",
    "model": "SaranaPrasarana",
    "label": "Sarana dan Prasarana",
    "phase": "masukan",
    "attributes": [
      {
        "name": "nama_sarpras",
        "field": "namaSarpras",
        "type": "string",
        "nullable": false,
        "label": "Nama sarpras",
        "length": 150
      },
      {
        "name": "lokasi",
        "field": "lokasi",
        "type": "string",
        "nullable": true,
        "label": "Lokasi",
        "length": 100
      },
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "tersedia",
          "digunakan",
          "rusak"
        ],
        "default": "tersedia"
      }
    ],
    "relations": []
  },
  {
    "resourceType": "skema-pendanaan",
    "model": "SkemaPendanaan",
    "label": "Skema Pendanaan",
    "phase": "masukan",
    "attributes": [
      {
        "name": "nama_skema",
        "field": "namaSkema",
        "type": "string",
        "nullable": false,
        "label": "Nama skema",
        "length": 100
      },
      {
        "name": "jenis",
        "field": "jenis",
        "type": "enum",
        "nullable": false,
        "label": "Jenis",
        "values": [
          "internal",
          "eksternal"
        ]
      },
      {
        "name": "plafon_dana",
        "field": "plafonDana",
        "type": "decimal",
        "nullable": false,
        "label": "Plafon dana"
      },
      {
        "name": "sumber_dana",
        "field": "sumberDana",
        "type": "string",
        "nullable": true,
        "label": "Sumber dana",
        "length": 100
      }
    ],
    "relations": []
  },
  {
    "resourceType": "mitra",
    "model": "Mitra",
    "label": "Mitra Eksternal",
    "phase": "masukan",
    "attributes": [
      {
        "name": "nama_mitra",
        "field": "namaMitra",
        "type": "string",
        "nullable": false,
        "label": "Nama mitra",
        "length": 150
      },
      {
        "name": "jenis_mitra",
        "field": "jenisMitra",
        "type": "enum",
        "nullable": false,
        "label": "Jenis mitra",
        "values": [
          "industri",
          "pemda",
          "masyarakat",
          "pt_lain"
        ]
      },
      {
        "name": "kontak",
        "field": "kontak",
        "type": "string",
        "nullable": true,
        "label": "Kontak",
        "length": 100
      },
      {
        "name": "alamat",
        "field": "alamat",
        "type": "string",
        "nullable": true,
        "label": "Alamat",
        "length": 200
      }
    ],
    "relations": []
  },
  {
    "resourceType": "proposal",
    "model": "Proposal",
    "label": "Proposal Penelitian/PkM",
    "phase": "proses",
    "attributes": [
      {
        "name": "judul",
        "field": "judul",
        "type": "string",
        "nullable": false,
        "label": "Judul",
        "length": 250
      },
      {
        "name": "jenis",
        "field": "jenis",
        "type": "enum",
        "nullable": false,
        "label": "Jenis",
        "values": [
          "penelitian",
          "pkm"
        ]
      },
      {
        "name": "tahun_usulan",
        "field": "tahunUsulan",
        "type": "year",
        "nullable": false,
        "label": "Tahun usulan"
      },
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "draft",
          "diajukan",
          "direview",
          "disetujui",
          "ditolak"
        ],
        "default": "draft"
      },
      {
        "name": "rab_total",
        "field": "rabTotal",
        "type": "decimal",
        "nullable": false,
        "label": "Rab total"
      },
      {
        "name": "berisiko_etik",
        "field": "berisikoEtik",
        "type": "boolean",
        "nullable": false,
        "label": "Berisiko etik",
        "default": false
      }
    ],
    "relations": [
      {
        "field": "pengusul",
        "column": "dosen_id",
        "resourceType": "dosen",
        "nullable": false
      },
      {
        "field": "skemaPendanaan",
        "column": "skema_pendanaan_id",
        "resourceType": "skema-pendanaan",
        "nullable": false
      },
      {
        "field": "petaJalan",
        "column": "peta_jalan_id",
        "resourceType": "peta-jalan",
        "nullable": true
      }
    ]
  },
  {
    "resourceType": "anggota-tim",
    "model": "AnggotaTim",
    "label": "Anggota Tim Proposal",
    "phase": "proses",
    "attributes": [
      {
        "name": "peran",
        "field": "peran",
        "type": "string",
        "nullable": false,
        "label": "Peran",
        "length": 50
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      },
      {
        "field": "dosen",
        "column": "dosen_id",
        "resourceType": "dosen",
        "nullable": true
      },
      {
        "field": "mahasiswa",
        "column": "mahasiswa_id",
        "resourceType": "mahasiswa",
        "nullable": true
      }
    ]
  },
  {
    "resourceType": "penilaian",
    "model": "Penilaian",
    "label": "Penilaian Proposal",
    "phase": "proses",
    "attributes": [
      {
        "name": "skor",
        "field": "skor",
        "type": "decimal",
        "nullable": true,
        "label": "Skor"
      },
      {
        "name": "komentar",
        "field": "komentar",
        "type": "text",
        "nullable": true,
        "label": "Komentar"
      },
      {
        "name": "status_finalisasi",
        "field": "statusFinalisasi",
        "type": "boolean",
        "nullable": false,
        "label": "Status finalisasi",
        "default": false
      },
      {
        "name": "tanggal",
        "field": "tanggal",
        "type": "date",
        "nullable": true,
        "label": "Tanggal"
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      },
      {
        "field": "reviewer",
        "column": "dosen_id",
        "resourceType": "dosen",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "klirens-etik",
    "model": "KlirensEtik",
    "label": "Klirens Etik",
    "phase": "proses",
    "attributes": [
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "diajukan",
          "ditinjau",
          "disetujui",
          "ditolak"
        ],
        "default": "diajukan"
      },
      {
        "name": "nomor_sertifikat",
        "field": "nomorSertifikat",
        "type": "string",
        "nullable": true,
        "label": "Nomor sertifikat",
        "length": 50
      },
      {
        "name": "tanggal_terbit",
        "field": "tanggalTerbit",
        "type": "date",
        "nullable": true,
        "label": "Tanggal terbit"
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "kontrak",
    "model": "Kontrak",
    "label": "Kontrak Penugasan",
    "phase": "proses",
    "attributes": [
      {
        "name": "nomor_sk",
        "field": "nomorSk",
        "type": "string",
        "nullable": false,
        "label": "Nomor sk",
        "length": 50
      },
      {
        "name": "tanggal_kontrak",
        "field": "tanggalKontrak",
        "type": "date",
        "nullable": false,
        "label": "Tanggal kontrak"
      },
      {
        "name": "nilai_kontrak",
        "field": "nilaiKontrak",
        "type": "decimal",
        "nullable": false,
        "label": "Nilai kontrak"
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "pencairan-dana",
    "model": "PencairanDana",
    "label": "Pencairan Dana",
    "phase": "proses",
    "attributes": [
      {
        "name": "termin",
        "field": "termin",
        "type": "integer",
        "nullable": false,
        "label": "Termin"
      },
      {
        "name": "jumlah",
        "field": "jumlah",
        "type": "decimal",
        "nullable": false,
        "label": "Jumlah"
      },
      {
        "name": "tanggal",
        "field": "tanggal",
        "type": "date",
        "nullable": true,
        "label": "Tanggal"
      },
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "dijadwalkan",
          "dicairkan",
          "tertunda"
        ],
        "default": "dijadwalkan"
      }
    ],
    "relations": [
      {
        "field": "kontrak",
        "column": "kontrak_id",
        "resourceType": "kontrak",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "logbook",
    "model": "Logbook",
    "label": "Logbook Kemajuan",
    "phase": "proses",
    "attributes": [
      {
        "name": "periode",
        "field": "periode",
        "type": "string",
        "nullable": false,
        "label": "Periode",
        "length": 20
      },
      {
        "name": "isi_kemajuan",
        "field": "isiKemajuan",
        "type": "text",
        "nullable": false,
        "label": "Isi kemajuan"
      },
      {
        "name": "tanggal_isi",
        "field": "tanggalIsi",
        "type": "date",
        "nullable": false,
        "label": "Tanggal isi"
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "monev",
    "model": "Monev",
    "label": "Monitoring dan Evaluasi",
    "phase": "proses",
    "attributes": [
      {
        "name": "catatan",
        "field": "catatan",
        "type": "text",
        "nullable": true,
        "label": "Catatan"
      },
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "terjadwal",
          "selesai"
        ],
        "default": "terjadwal"
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      },
      {
        "field": "reviewer",
        "column": "dosen_id",
        "resourceType": "dosen",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "integrasi-kurikulum",
    "model": "IntegrasiKurikulum",
    "label": "Integrasi Kurikulum",
    "phase": "proses",
    "attributes": [
      {
        "name": "mata_kuliah",
        "field": "mataKuliah",
        "type": "string",
        "nullable": false,
        "label": "Mata kuliah",
        "length": 100
      },
      {
        "name": "rps_tautan",
        "field": "rpsTautan",
        "type": "string",
        "nullable": true,
        "label": "Rps tautan",
        "length": 255
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "publikasi",
    "model": "Publikasi",
    "label": "Publikasi",
    "phase": "luaran",
    "attributes": [
      {
        "name": "judul",
        "field": "judul",
        "type": "string",
        "nullable": false,
        "label": "Judul",
        "length": 250
      },
      {
        "name": "jurnal_prosiding",
        "field": "jurnalProsiding",
        "type": "string",
        "nullable": true,
        "label": "Jurnal prosiding",
        "length": 150
      },
      {
        "name": "indeksasi",
        "field": "indeksasi",
        "type": "enum",
        "nullable": false,
        "label": "Indeksasi",
        "values": [
          "scopus",
          "sinta_1",
          "sinta_2",
          "sinta_3",
          "sinta_4",
          "sinta_5",
          "sinta_6",
          "nasional_non_sinta"
        ]
      },
      {
        "name": "tahun",
        "field": "tahun",
        "type": "year",
        "nullable": false,
        "label": "Tahun"
      },
      {
        "name": "doi",
        "field": "doi",
        "type": "string",
        "nullable": true,
        "label": "Doi",
        "length": 100
      },
      {
        "name": "lisensi",
        "field": "lisensi",
        "type": "string",
        "nullable": true,
        "label": "Lisensi",
        "length": 20
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "hki",
    "model": "Hki",
    "label": "Kekayaan Intelektual",
    "phase": "luaran",
    "attributes": [
      {
        "name": "jenis",
        "field": "jenis",
        "type": "enum",
        "nullable": false,
        "label": "Jenis",
        "values": [
          "paten",
          "hak_cipta",
          "desain_industri",
          "merek",
          "lainnya"
        ]
      },
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "diajukan",
          "diperiksa_substantif",
          "terbit",
          "ditolak"
        ],
        "default": "diajukan"
      },
      {
        "name": "nomor_sertifikat",
        "field": "nomorSertifikat",
        "type": "string",
        "nullable": true,
        "label": "Nomor sertifikat",
        "length": 50
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "produk-adopsi",
    "model": "ProdukAdopsi",
    "label": "Produk/Purwarupa Adopsi",
    "phase": "luaran",
    "attributes": [
      {
        "name": "nama_produk",
        "field": "namaProduk",
        "type": "string",
        "nullable": false,
        "label": "Nama produk",
        "length": 150
      },
      {
        "name": "bukti_adopsi",
        "field": "buktiAdopsi",
        "type": "string",
        "nullable": true,
        "label": "Bukti adopsi",
        "length": 255
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      },
      {
        "field": "mitra",
        "column": "mitra_id",
        "resourceType": "mitra",
        "nullable": true
      }
    ]
  },
  {
    "resourceType": "laporan-akhir",
    "model": "LaporanAkhir",
    "label": "Laporan Akhir",
    "phase": "luaran",
    "attributes": [
      {
        "name": "dokumen_url",
        "field": "dokumenUrl",
        "type": "string",
        "nullable": false,
        "label": "Dokumen url",
        "length": 255
      },
      {
        "name": "tanggal_submit",
        "field": "tanggalSubmit",
        "type": "date",
        "nullable": false,
        "label": "Tanggal submit"
      },
      {
        "name": "status_verifikasi",
        "field": "statusVerifikasi",
        "type": "enum",
        "nullable": false,
        "label": "Status verifikasi",
        "values": [
          "belum",
          "terverifikasi"
        ],
        "default": "belum"
      }
    ],
    "relations": [
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "spj",
    "model": "Spj",
    "label": "Surat Pertanggungjawaban",
    "phase": "luaran",
    "attributes": [
      {
        "name": "jumlah_realisasi",
        "field": "jumlahRealisasi",
        "type": "decimal",
        "nullable": false,
        "label": "Jumlah realisasi"
      },
      {
        "name": "dokumen_url",
        "field": "dokumenUrl",
        "type": "string",
        "nullable": true,
        "label": "Dokumen url",
        "length": 255
      }
    ],
    "relations": [
      {
        "field": "laporanAkhir",
        "column": "laporan_akhir_id",
        "resourceType": "laporan-akhir",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "sitasi",
    "model": "Sitasi",
    "label": "Sitasi",
    "phase": "dampak",
    "attributes": [
      {
        "name": "jumlah_sitasi",
        "field": "jumlahSitasi",
        "type": "integer",
        "nullable": false,
        "label": "Jumlah sitasi",
        "default": 0
      },
      {
        "name": "tahun",
        "field": "tahun",
        "type": "year",
        "nullable": false,
        "label": "Tahun"
      },
      {
        "name": "sumber",
        "field": "sumber",
        "type": "enum",
        "nullable": false,
        "label": "Sumber",
        "values": [
          "google_scholar",
          "sinta",
          "scopus"
        ]
      }
    ],
    "relations": [
      {
        "field": "publikasi",
        "column": "publikasi_id",
        "resourceType": "publikasi",
        "nullable": true
      },
      {
        "field": "dosen",
        "column": "dosen_id",
        "resourceType": "dosen",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "rekognisi",
    "model": "Rekognisi",
    "label": "Rekognisi",
    "phase": "dampak",
    "attributes": [
      {
        "name": "jenis",
        "field": "jenis",
        "type": "string",
        "nullable": false,
        "label": "Jenis",
        "length": 100
      },
      {
        "name": "deskripsi",
        "field": "deskripsi",
        "type": "text",
        "nullable": true,
        "label": "Deskripsi"
      },
      {
        "name": "tahun",
        "field": "tahun",
        "type": "year",
        "nullable": false,
        "label": "Tahun"
      }
    ],
    "relations": [
      {
        "field": "dosen",
        "column": "dosen_id",
        "resourceType": "dosen",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "kerjasama",
    "model": "Kerjasama",
    "label": "Kerja Sama",
    "phase": "dampak",
    "attributes": [
      {
        "name": "ruang_lingkup",
        "field": "ruangLingkup",
        "type": "string",
        "nullable": false,
        "label": "Ruang lingkup",
        "length": 200
      },
      {
        "name": "tanggal_mulai",
        "field": "tanggalMulai",
        "type": "date",
        "nullable": false,
        "label": "Tanggal mulai"
      },
      {
        "name": "tanggal_akhir",
        "field": "tanggalAkhir",
        "type": "date",
        "nullable": true,
        "label": "Tanggal akhir"
      },
      {
        "name": "status",
        "field": "status",
        "type": "enum",
        "nullable": false,
        "label": "Status",
        "values": [
          "aktif",
          "berakhir",
          "diperpanjang"
        ],
        "default": "aktif"
      }
    ],
    "relations": [
      {
        "field": "mitra",
        "column": "mitra_id",
        "resourceType": "mitra",
        "nullable": false
      }
    ]
  },
  {
    "resourceType": "survei-dampak",
    "model": "SurveiDampak",
    "label": "Survei Dampak",
    "phase": "dampak",
    "attributes": [
      {
        "name": "hasil",
        "field": "hasil",
        "type": "text",
        "nullable": true,
        "label": "Hasil"
      },
      {
        "name": "testimoni",
        "field": "testimoni",
        "type": "text",
        "nullable": true,
        "label": "Testimoni"
      }
    ],
    "relations": [
      {
        "field": "mitra",
        "column": "mitra_id",
        "resourceType": "mitra",
        "nullable": false
      },
      {
        "field": "proposal",
        "column": "proposal_id",
        "resourceType": "proposal",
        "nullable": true
      }
    ]
  }
];

/** Pencarian cepat metadata entitas berdasarkan resourceType (slug URL). */
export function findEntity(resourceType) {
  return ENTITIES.find((e) => e.resourceType === resourceType) ?? null;
}

/** Kelompokkan entitas per fase, dengan urutan fase yang konsisten. */
export function entitiesByPhase() {
  const grouped = {};
  for (const phase of PHASE_ORDER) grouped[phase] = [];
  for (const entity of ENTITIES) grouped[entity.phase].push(entity);
  return grouped;
}
