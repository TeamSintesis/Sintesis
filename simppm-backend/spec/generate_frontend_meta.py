#!/usr/bin/env python3
"""
Generator metadata frontend (src/lib/entities.meta.js) untuk aplikasi Svelte
SIMPPM, diturunkan dari spec/entities.json -- sumber kebenaran tunggal yang
sama yang dipakai untuk menghasilkan migrations, model, dan schema JSON:API
di sisi backend (lihat generate.py, generate_jsonapi.py).

Tujuan: frontend TIDAK menuliskan ulang secara manual daftar 27 entitas,
kolom, tipe data, dan relasinya -- cukup membaca berkas metadata JS yang
diregenerasi dari spesifikasi yang sama, sehingga backend dan frontend
selalu konsisten walau basis kodenya sepenuhnya terpisah (uncoupled).

Aturan konversi penamaan (harus SAMA dengan yang dipakai
laravel-json-api/laravel di sisi backend -- lihat app/JsonApi/V1/*/*.php):
  - Atribut: snake_case kolom database -> camelCase field JSON:API.
  - Relasi (belongsTo): nama relasi (relation_name pada FK, atau nama
    kolom tanpa akhiran "_id" bila tidak didefinisikan) -> camelCase.
  - Tipe resource JSON:API relasi: nama tabel yang direferensikan dengan
    "_" diganti "-" (mis. "skema_pendanaan" -> "skema-pendanaan").

Jalankan ulang skrip ini setiap kali spec/entities.json berubah:
    python3 spec/generate_frontend_meta.py
"""
import json
import os
import re

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
ENTITIES_PATH = os.path.join(BASE, "spec", "entities.json")
OUTPUT_PATH = os.path.join(
    os.path.dirname(BASE), "simppm-frontend", "src", "lib", "entities.meta.js"
)

PHASE_LABELS = {
    "masukan": "Masukan",
    "proses": "Proses",
    "luaran": "Luaran/Capaian",
    "dampak": "Dampak",
}

PHASE_ORDER = ["masukan", "proses", "luaran", "dampak"]


def to_camel(snake: str) -> str:
    """Konversi snake_case menjadi camelCase (sama seperti konversi
    otomatis laravel-json-api/laravel pada nama field & relasi)."""
    parts = snake.split("_")
    return parts[0] + "".join(p.capitalize() for p in parts[1:])


def to_resource_type(table_name: str) -> str:
    """Konversi nama tabel (snake_case) menjadi resource_type JSON:API
    (kebab-case), sesuai konvensi yang dipakai generate_jsonapi.py."""
    return table_name.replace("_", "-")


def relation_field_name(fk: dict) -> str:
    name = fk.get("relation_name")
    if not name:
        col = fk["column"]
        name = re.sub(r"_id$", "", col)
    return to_camel(name)


def build_meta():
    with open(ENTITIES_PATH, encoding="utf-8") as fh:
        spec = json.load(fh)

    entities = []
    for e in spec["entities"]:
        attributes = []
        for col in e["columns"]:
            attr = {
                "name": col["name"],
                "field": to_camel(col["name"]),
                "type": col["type"],
                "nullable": col.get("nullable", True),
                "label": col["name"].replace("_", " ").capitalize(),
            }
            if col["type"] == "enum":
                attr["values"] = col.get("values", [])
            if "default" in col:
                attr["default"] = col["default"]
            if col["type"] in ("string", "text") and "length" in col:
                attr["length"] = col["length"]
            attributes.append(attr)

        relations = []
        for fk in e.get("foreign_keys", []):
            relations.append(
                {
                    "field": relation_field_name(fk),
                    "column": fk["column"],
                    "resourceType": to_resource_type(fk["references"]),
                    "nullable": fk.get("nullable", True),
                }
            )

        entities.append(
            {
                "resourceType": e["resource_type"],
                "model": e["model"],
                "label": e["label"],
                "phase": e["phase"],
                "attributes": attributes,
                "relations": relations,
            }
        )

    # Urutkan berdasarkan urutan siklus 4 fase (Masukan -> Proses -> Luaran -> Dampak)
    entities.sort(key=lambda x: PHASE_ORDER.index(x["phase"]))

    return entities


def render_js(entities):
    header = (
        "// Berkas ini DIBUAT OTOMATIS oleh spec/generate_frontend_meta.py di\n"
        "// proyek backend (simppm-backend) -- JANGAN diedit manual.\n"
        "// Sumber kebenaran: simppm-backend/spec/entities.json.\n"
        "//\n"
        "// Untuk memperbarui berkas ini setelah spec/entities.json berubah:\n"
        "//   cd simppm-backend && python3 spec/generate_frontend_meta.py\n"
        "//\n"
        "// Metadata ini mendeskripsikan seluruh 27 entitas SIMPPM (atribut,\n"
        "// tipe data, dan relasi) sehingga komponen frontend generik\n"
        "// (ResourceList.svelte, ResourceForm.svelte) dapat merender\n"
        "// tabel & form untuk SETIAP entitas tanpa kode khusus per-entitas.\n\n"
        "/** Label tampilan untuk setiap fase siklus PPM. */\n"
        "export const PHASE_LABELS = " + json.dumps(PHASE_LABELS, ensure_ascii=False, indent=2) + ";\n\n"
        "/** Urutan fase sesuai siklus Masukan -> Proses -> Luaran -> Dampak. */\n"
        "export const PHASE_ORDER = " + json.dumps(PHASE_ORDER, ensure_ascii=False) + ";\n\n"
        "/** Metadata seluruh entitas: atribut, tipe, dan relasi. */\n"
        "export const ENTITIES = " + json.dumps(entities, ensure_ascii=False, indent=2) + ";\n\n"
        "/** Pencarian cepat metadata entitas berdasarkan resourceType (slug URL). */\n"
        "export function findEntity(resourceType) {\n"
        "  return ENTITIES.find((e) => e.resourceType === resourceType) ?? null;\n"
        "}\n\n"
        "/** Kelompokkan entitas per fase, dengan urutan fase yang konsisten. */\n"
        "export function entitiesByPhase() {\n"
        "  const grouped = {};\n"
        "  for (const phase of PHASE_ORDER) grouped[phase] = [];\n"
        "  for (const entity of ENTITIES) grouped[entity.phase].push(entity);\n"
        "  return grouped;\n"
        "}\n"
    )
    return header


def main():
    entities = build_meta()
    js = render_js(entities)
    os.makedirs(os.path.dirname(OUTPUT_PATH), exist_ok=True)
    with open(OUTPUT_PATH, "w", encoding="utf-8") as fh:
        fh.write(js)
    print(f"Metadata frontend ditulis ke: {OUTPUT_PATH}")
    print(f"Total entitas: {len(entities)}")


if __name__ == "__main__":
    main()
