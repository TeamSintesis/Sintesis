<script>
  // Komponen generik: form tambah/ubah untuk ENTITAS APA PUN, ditentukan
  // lewat parameter rute `:resource` (dan `:id` bila mode ubah). Jenis input
  // per kolom (teks/angka/tanggal/enum/checkbox) serta dropdown relasi
  // diturunkan otomatis dari metadata di lib/entities.meta.js.
  import { push } from 'svelte-spa-router';
  import { findEntity } from '../lib/entities.meta.js';
  import { getResource, createResource, updateResource, listResource, ApiError } from '../lib/api.js';
  import { kolomLabelUtama } from '../lib/format.js';
  import ErrorBanner from '../components/ErrorBanner.svelte';
  import Spinner from '../components/Spinner.svelte';

  let { params } = $props();

  let entityMeta = $derived(findEntity(params.resource));
  let modeUbah = $derived(Boolean(params.id));

  let form = $state({});
  let relasiValues = $state({});
  /** @type {Record<string, Array<{id: string, label: string}>>} */
  let opsiRelasi = $state({});
  let memuat = $state(true);
  let menyimpan = $state(false);
  let error = $state(null);
  /** @type {Record<string, string>} */
  let errorField = $state({});

  function nilaiAwal(atribut) {
    if (atribut.type === 'boolean') return atribut.default ?? false;
    return atribut.default ?? '';
  }

  async function muatOpsiRelasi(meta) {
    const hasil = {};
    await Promise.all(
      meta.relations.map(async (relasi) => {
        try {
          const payload = await listResource(relasi.resourceType, { page: 1, pageSize: 100 });
          const relatedMeta = findEntity(relasi.resourceType);
          const kolom = relatedMeta ? kolomLabelUtama(relatedMeta) : null;
          hasil[relasi.field] = (payload.data ?? []).map((item) => ({
            id: item.id,
            label: kolom ? (item.attributes?.[kolom] ?? `#${item.id}`) : `#${item.id}`,
          }));
        } catch {
          hasil[relasi.field] = [];
        }
      })
    );
    opsiRelasi = hasil;
  }

  async function inisialisasi() {
    if (!entityMeta) return;
    memuat = true;
    error = null;
    errorField = {};

    const formBaru = {};
    for (const atribut of entityMeta.attributes) formBaru[atribut.field] = nilaiAwal(atribut);
    const relasiBaru = {};
    for (const relasi of entityMeta.relations) relasiBaru[relasi.field] = '';

    await muatOpsiRelasi(entityMeta);

    if (modeUbah) {
      try {
        // Sertakan include untuk semua relasi agar linkage (data.id) ikut
        // terbawa -- tanpa include, backend hanya mengembalikan links.
        const includeRelasi = entityMeta.relations.map((r) => r.field);
        const payload = await getResource(entityMeta.resourceType, params.id, { include: includeRelasi });
        const resource = payload.data;
        for (const atribut of entityMeta.attributes) {
          const v = resource.attributes?.[atribut.field];
          formBaru[atribut.field] = v === null || v === undefined ? nilaiAwal(atribut) : v;
        }
        for (const relasi of entityMeta.relations) {
          relasiBaru[relasi.field] = resource.relationships?.[relasi.field]?.data?.id ?? '';
        }
      } catch (err) {
        error = err;
      }
    }

    form = formBaru;
    relasiValues = relasiBaru;
    memuat = false;
  }

  $effect(() => {
    // Lacak params.resource & params.id sebagai dependensi reaktif agar
    // form dimuat ulang saat berpindah antar-entitas atau antar-record.
    void params.resource;
    void params.id;
    inisialisasi();
  });

  function labelPointer(pointer) {
    // Pointer JSON:API berbentuk "/data/attributes/namaProdi" atau
    // "/data/relationships/prodi" -- ambil segmen field terakhir.
    return pointer ? pointer.split('/').pop() : null;
  }

  async function tanganiSubmit(event) {
    event.preventDefault();
    menyimpan = true;
    error = null;
    errorField = {};

    const attributes = {};
    for (const atribut of entityMeta.attributes) {
      let v = form[atribut.field];
      // Skema JSON:API backend memakai Number::make HANYA untuk kolom
      // bertipe "integer"; kolom "decimal" & "year" tetap Str::make (agar
      // presisi desimal aman & konsisten dgn kolom string lain), meski
      // ditampilkan sebagai <input type="number"> di form (yang otomatis
      // membuat nilai terikat berupa Number di Svelte) -- jadi keduanya
      // wajib dikembalikan ke string sebelum dikirim, sesuai kontrak API.
      if (atribut.type === 'integer' && v !== '' && v !== null) v = Number(v);
      if (['decimal', 'year'].includes(atribut.type) && v !== '' && v !== null) v = String(v);
      if (v === '' && atribut.nullable) v = null;
      attributes[atribut.field] = v;
    }

    const relationships = {};
    for (const relasi of entityMeta.relations) {
      const id = relasiValues[relasi.field];
      if (id) relationships[relasi.field] = { data: { type: relasi.resourceType, id: String(id) } };
    }

    try {
      if (modeUbah) {
        await updateResource(entityMeta.resourceType, params.id, attributes, relationships);
      } else {
        await createResource(entityMeta.resourceType, attributes, relationships);
      }
      push(`/${entityMeta.resourceType}`);
    } catch (err) {
      error = err;
      if (err instanceof ApiError) {
        const map = {};
        for (const e of err.errors) {
          const field = labelPointer(e.source?.pointer);
          if (field) map[field] = e.detail || e.title;
        }
        errorField = map;
      }
    } finally {
      menyimpan = false;
    }
  }
</script>

{#if !entityMeta}
  <div class="alert alert-danger">Entitas "{params.resource}" tidak dikenali.</div>
{:else}
  <div style="margin-bottom:16px;">
    <h1 style="font-size:20px;">{modeUbah ? 'Ubah' : 'Tambah'} {entityMeta.label}</h1>
  </div>

  <ErrorBanner {error} />

  {#if memuat}
    <Spinner label="Memuat form…" />
  {:else}
    <form class="card" style="padding:24px;max-width:640px;" onsubmit={tanganiSubmit}>
      {#each entityMeta.attributes as atribut}
        <div class="field">
          <label for={atribut.field}>{atribut.label}{atribut.nullable ? '' : ' *'}</label>

          {#if atribut.type === 'enum'}
            <select id={atribut.field} bind:value={form[atribut.field]} required={!atribut.nullable}>
              {#if atribut.nullable}<option value="">-- pilih --</option>{/if}
              {#each atribut.values as opsi}
                <option value={opsi}>{opsi}</option>
              {/each}
            </select>
          {:else if atribut.type === 'boolean'}
            <label style="flex-direction:row;align-items:center;gap:8px;font-weight:400;">
              <input id={atribut.field} type="checkbox" bind:checked={form[atribut.field]} />
              Ya
            </label>
          {:else if atribut.type === 'text'}
            <textarea id={atribut.field} rows="3" bind:value={form[atribut.field]} required={!atribut.nullable}></textarea>
          {:else if atribut.type === 'date'}
            <input id={atribut.field} type="date" bind:value={form[atribut.field]} required={!atribut.nullable} />
          {:else if atribut.type === 'decimal'}
            <input id={atribut.field} type="number" step="0.01" bind:value={form[atribut.field]} required={!atribut.nullable} />
          {:else if atribut.type === 'year'}
            <input id={atribut.field} type="number" step="1" min="1900" max="2100" bind:value={form[atribut.field]} required={!atribut.nullable} />
          {:else if atribut.type === 'integer'}
            <input id={atribut.field} type="number" step="1" bind:value={form[atribut.field]} required={!atribut.nullable} />
          {:else}
            <input id={atribut.field} type="text" maxlength={atribut.length ?? undefined} bind:value={form[atribut.field]} required={!atribut.nullable} />
          {/if}

          {#if errorField[atribut.field]}
            <div class="error">{errorField[atribut.field]}</div>
          {/if}
        </div>
      {/each}

      {#each entityMeta.relations as relasi}
        <div class="field">
          <label for={relasi.field}>{relasi.field}{relasi.nullable ? '' : ' *'}</label>
          <select id={relasi.field} bind:value={relasiValues[relasi.field]} required={!relasi.nullable}>
            <option value="">-- pilih --</option>
            {#each opsiRelasi[relasi.field] ?? [] as opsi}
              <option value={opsi.id}>{opsi.label}</option>
            {/each}
          </select>
          {#if errorField[relasi.field]}
            <div class="error">{errorField[relasi.field]}</div>
          {/if}
        </div>
      {/each}

      <div style="display:flex;gap:10px;margin-top:8px;">
        <button class="btn btn-primary" type="submit" disabled={menyimpan}>
          {menyimpan ? 'Menyimpan…' : 'Simpan'}
        </button>
        <a class="btn" href={`#/${entityMeta.resourceType}`}>Batal</a>
      </div>
    </form>
  {/if}
{/if}
