#!/usr/bin/env python3
"""
Generator lapisan JSON:API (Schema, Query, CollectionQuery, Request) untuk
seluruh 27 entitas SIMPPM, berdasarkan spec/entities.json.

Skrip ini juga memperbarui:
- app/JsonApi/V1/Server.php (daftar allSchemas())
- routes/api.php (registrasi seluruh resource JSON:API)

Konvensi namespace: setiap entitas mendapat namespace tersendiri
App\\JsonApi\\V1\\{ModelName} (TIDAK dipluralkan) agar konsisten dan
menghindari pluralisasi bahasa Inggris yang keliru untuk kata Indonesia
(misalnya "Dosen" tidak dijadikan "Dosens").
"""
import json
import os

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SPEC_PATH = os.path.join(BASE, "spec", "entities.json")

with open(SPEC_PATH) as f:
    SPEC = json.load(f)

ENTITIES = {e["table"]: e for e in SPEC["entities"]}
ORDER = [e["table"] for e in SPEC["entities"]]  # urutan sesuai definisi (aman FK)


def to_camel(snake: str) -> str:
    """Ubah snake_case menjadi camelCase untuk nama field JSON:API."""
    parts = snake.split("_")
    return parts[0] + "".join(p.capitalize() for p in parts[1:])


def field_class_for(col):
    t = col["type"]
    if t in ("string", "text", "enum", "date", "year"):
        return "Str"
    if t == "integer":
        return "Number"
    if t == "decimal":
        return "Str"  # nilai desimal diserialisasikan sebagai string agar presisi aman
    if t == "boolean":
        return "Boolean"
    return "Str"


JSONAPI_DIR = os.path.join(BASE, "app", "JsonApi", "V1")
os.makedirs(JSONAPI_DIR, exist_ok=True)

# Kumpulkan relasi balik (hasMany) per tabel induk, agar Schema induk juga
# dapat mengekspos relasi tersebut (opsional, hanya untuk tabel yang benar2
# dirujuk). Nama relasi mengikuti konvensi {modelCamel}List pada model.
reverse_relations = {t: [] for t in ORDER}
for table, e in ENTITIES.items():
    for fk in e["foreign_keys"]:
        ref = fk["references"]
        reverse_relations[ref].append({
            "child_table": table,
            "child_model": e["model"],
            "field_name": to_camel(e["model"][0].lower() + e["model"][1:]) + "List",
        })

server_schema_lines = []
route_lines = []

for table in ORDER:
    e = ENTITIES[table]
    model = e["model"]
    resource_type = e["resource_type"]
    ns = f"App\\JsonApi\\V1\\{model}"
    dir_path = os.path.join(JSONAPI_DIR, model)
    os.makedirs(dir_path, exist_ok=True)

    # ------------------------------------------------------------------
    # SCHEMA
    # ------------------------------------------------------------------
    field_lines = ["            ID::make(),"]
    filter_lines = ["            WhereIdIn::make($this),"]
    used_field_classes = set()

    for fk in e["foreign_keys"]:
        ref = fk["references"]
        ref_model = ENTITIES[ref]["model"]
        ref_resource_type = ENTITIES[ref]["resource_type"]
        relation_name = fk.get("relation_name") or to_camel(ref)
        # ->type(...) WAJIB diset eksplisit: tanpa ini, laravel-json-api
        # menebak tipe resource dari nama field (mis. 'pengusul' menjadi
        # 'pengusuls'), bukan dari tipe resource model tujuan yang
        # sebenarnya (mis. 'dosen'). Ini konsisten dengan perbaikan bug
        # TypeResolver yang sama pada static type() method di Schema.
        field_lines.append(
            f"            BelongsTo::make('{relation_name}')->type('{ref_resource_type}'),"
        )
        filter_lines.append(
            f"            Where::make('{relation_name}Id', '{fk['column']}'),"
        )

    for col in e["columns"]:
        field_name = to_camel(col["name"])
        col_field_class = field_class_for(col)
        used_field_classes.add(col_field_class)
        field_lines.append(
            f"            {col_field_class}::make('{field_name}', '{col['name']}')" +
            ("->sortable()" if col["type"] in ("date", "year", "integer") else "") +
            ","
        )
        if col["type"] in ("enum", "boolean", "year"):
            filter_lines.append(
                f"            Where::make('{field_name}', '{col['name']}'),"
            )

    for rel in reverse_relations[table]:
        child_resource_type = ENTITIES[rel["child_table"]]["resource_type"]
        # ->type(...) juga wajib di sini agar tidak salah menebak tipe
        # resource anak dari akhiran 'List' pada nama field relasi.
        field_lines.append(
            f"            HasMany::make('{rel['field_name']}')->type('{child_resource_type}')->readOnly(),"
        )

    field_lines.append(
        "            DateTime::make('createdAt')->sortable()->readOnly(),"
    )
    field_lines.append(
        "            DateTime::make('updatedAt')->sortable()->readOnly(),"
    )

    fields_body = "\n".join(field_lines)
    filters_body = "\n".join(filter_lines)

    field_imports = "\n".join(
        f"use LaravelJsonApi\\Eloquent\\Fields\\{c};" for c in sorted(used_field_classes)
    )

    schema_content = f"""<?php

namespace {ns};

// Skema JSON:API untuk entitas {model} ({e['label']}).
// Berkas ini dibuat otomatis oleh spec/generate_jsonapi.py — jangan diubah
// manual, ubah spec/entities.json lalu jalankan ulang generator.

use App\\Models\\{model};
use LaravelJsonApi\\Eloquent\\Contracts\\Paginator;
use LaravelJsonApi\\Eloquent\\Fields\\DateTime;
use LaravelJsonApi\\Eloquent\\Fields\\ID;
{field_imports}
use LaravelJsonApi\\Eloquent\\Fields\\Relations\\BelongsTo;
use LaravelJsonApi\\Eloquent\\Fields\\Relations\\HasMany;
use LaravelJsonApi\\Eloquent\\Filters\\Where;
use LaravelJsonApi\\Eloquent\\Filters\\WhereIdIn;
use LaravelJsonApi\\Eloquent\\Pagination\\PagePagination;
use LaravelJsonApi\\Eloquent\\Schema;

/**
 * Skema JSON:API untuk resource "{resource_type}".
 *
 * Memetakan atribut database (snake_case) ke field JSON:API (camelCase)
 * serta mendefinisikan relasi dan filter yang tersedia bagi klien.
 */
class {model}Schema extends Schema
{{
    /**
     * Model Eloquent yang bersesuaian dengan skema ini.
     *
     * @var string
     */
    public static string $model = {model}::class;

    /**
     * Tentukan jenis resource JSON:API secara eksplisit (bukan hasil tebakan
     * otomatis dari nama kelas), agar sesuai dengan resource_type pada
     * spec/entities.json — termasuk untuk kata Indonesia yang tidak boleh
     * dipluralkan mengikuti aturan bahasa Inggris (misalnya "dosen", bukan
     * "dosens").
     *
     * @return string
     */
    public static function type(): string
    {{
        return '{resource_type}';
    }}

    /**
     * Daftar field (atribut & relasi) yang diekspos melalui API.
     *
     * @return array
     */
    public function fields(): array
    {{
        return [
{fields_body}
        ];
    }}

    /**
     * Daftar filter query yang dapat digunakan klien (?filter[...]=...).
     *
     * @return array
     */
    public function filters(): array
    {{
        return [
{filters_body}
        ];
    }}

    /**
     * Konfigurasi paginasi berbasis halaman (page[number], page[size]).
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {{
        return PagePagination::make();
    }}
}}
"""
    with open(os.path.join(dir_path, f"{model}Schema.php"), "w") as f:
        f.write(schema_content)

    # ------------------------------------------------------------------
    # QUERY (single resource)
    # ------------------------------------------------------------------
    query_content = f"""<?php

namespace {ns};

// Aturan validasi parameter query untuk permintaan satu resource
// "{resource_type}" (GET /{resource_type}/{{id}}).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\\Laravel\\Http\\Requests\\ResourceQuery;
use LaravelJsonApi\\Validation\\Rule as JsonApiRule;

class {model}Query extends ResourceQuery
{{
    /**
     * Aturan validasi untuk parameter query permintaan.
     *
     * @return array
     */
    public function rules(): array
    {{
        return [
            'fields' => ['nullable', 'array', JsonApiRule::fieldSets()],
            'include' => ['nullable', 'string', JsonApiRule::includePaths()],
            'withCount' => ['nullable', 'string', JsonApiRule::countable()],
        ];
    }}
}}
"""
    with open(os.path.join(dir_path, f"{model}Query.php"), "w") as f:
        f.write(query_content)

    # ------------------------------------------------------------------
    # COLLECTION QUERY
    # ------------------------------------------------------------------
    collection_query_content = f"""<?php

namespace {ns};

// Aturan validasi parameter query untuk permintaan koleksi
// "{resource_type}" (GET /{resource_type}).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\\Laravel\\Http\\Requests\\ResourceQuery;
use LaravelJsonApi\\Validation\\Rule as JsonApiRule;

class {model}CollectionQuery extends ResourceQuery
{{
    /**
     * Aturan validasi untuk parameter query permintaan koleksi.
     *
     * @return array
     */
    public function rules(): array
    {{
        return [
            'fields' => ['nullable', 'array', JsonApiRule::fieldSets()],
            'filter' => ['nullable', 'array', JsonApiRule::filter()],
            'include' => ['nullable', 'string', JsonApiRule::includePaths()],
            'page' => ['nullable', 'array', JsonApiRule::page()],
            'sort' => ['nullable', 'string', JsonApiRule::sort()],
            'withCount' => ['nullable', 'string', JsonApiRule::countable()],
        ];
    }}
}}
"""
    with open(os.path.join(dir_path, f"{model}CollectionQuery.php"), "w") as f:
        f.write(collection_query_content)

    # ------------------------------------------------------------------
    # REQUEST (validasi body create/update)
    # ------------------------------------------------------------------
    rule_lines = []
    for fk in e["foreign_keys"]:
        relation_name = fk.get("relation_name") or to_camel(fk["references"])
        required = "required" if not fk.get("nullable") else "nullable"
        rule_lines.append(
            f"            '{relation_name}' => ['{required}'],"
        )
    for col in e["columns"]:
        field_name = to_camel(col["name"])
        required = "nullable" if col.get("nullable") else "required"
        if col["type"] == "enum":
            values = ", ".join(f"'{v}'" for v in col["values"])
            rule_lines.append(
                f"            '{field_name}' => ['{required}', Rule::in([{values}])],"
            )
        elif col["type"] == "boolean":
            rule_lines.append(f"            '{field_name}' => ['{required}', 'boolean'],")
        elif col["type"] == "integer":
            rule_lines.append(f"            '{field_name}' => ['{required}', 'integer'],")
        elif col["type"] == "decimal":
            rule_lines.append(f"            '{field_name}' => ['{required}', 'numeric'],")
        elif col["type"] == "year":
            rule_lines.append(f"            '{field_name}' => ['{required}', 'integer', 'digits:4'],")
        elif col["type"] == "date":
            rule_lines.append(f"            '{field_name}' => ['{required}', 'date'],")
        else:
            length = col.get("length", 255)
            rule_lines.append(
                f"            '{field_name}' => ['{required}', 'string', 'max:{length}'],"
            )
    rules_body = "\n".join(rule_lines)

    request_content = f"""<?php

namespace {ns};

// Aturan validasi body permintaan create/update untuk resource
// "{resource_type}". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\\Validation\\Rule;
use LaravelJsonApi\\Laravel\\Http\\Requests\\ResourceRequest;

class {model}Request extends ResourceRequest
{{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {{
        return [
{rules_body}
        ];
    }}
}}
"""
    with open(os.path.join(dir_path, f"{model}Request.php"), "w") as f:
        f.write(request_content)

    server_schema_lines.append(f"            \\App\\JsonApi\\V1\\{model}\\{model}Schema::class,")
    route_lines.append(f"    $server->resource('{resource_type}', JsonApiController::class);")

print(f"Berhasil membuat lapisan JSON:API (Schema/Query/CollectionQuery/Request) untuk {len(ORDER)} entitas.")

# ---------------------------------------------------------------------------
# Perbarui app/JsonApi/V1/Server.php
# ---------------------------------------------------------------------------
server_path = os.path.join(BASE, "app", "JsonApi", "V1", "Server.php")
with open(server_path) as f:
    server_src = f.read()

schemas_block = "return [\n" + "\n".join(server_schema_lines) + "\n        ];"
server_src = server_src.replace(
    "return [\n            // @TODO\n        ];",
    schemas_block,
)
with open(server_path, "w") as f:
    f.write(server_src)

print("Berhasil memperbarui Server.php dengan 27 skema.")

# ---------------------------------------------------------------------------
# Tulis routes/api.php
# ---------------------------------------------------------------------------
routes_body = "\n".join(route_lines)
routes_content = f"""<?php

// Registrasi rute JSON:API untuk seluruh 27 resource SIMPPM (server "v1").
// Dibuat otomatis oleh spec/generate_jsonapi.py, ditambah rute autentikasi
// manual (login/logout) di bagian bawah berkas.

use App\\Http\\Controllers\\Auth\\AuthController;
use Illuminate\\Support\\Facades\\Route;
use LaravelJsonApi\\Laravel\\Facades\\JsonApiRoute;
use LaravelJsonApi\\Laravel\\Http\\Controllers\\JsonApiController;
use LaravelJsonApi\\Laravel\\Routing\\ResourceRegistrar;

JsonApiRoute::server('v1')->prefix('v1')->middleware('auth:sanctum')->resources(function (ResourceRegistrar $server) {{
{routes_body}
}});

// Rute autentikasi berbasis token Sanctum (stateless, untuk SPA Svelte
// yang uncoupled / terpisah dari backend).
Route::prefix('v1/auth')->group(function () {{
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
}});
"""
with open(os.path.join(BASE, "routes", "api.php"), "w") as f:
    f.write(routes_content)

print("Berhasil menulis routes/api.php.")
