<script>
  // Komponen generik: form tambah/ubah untuk ENTITAS APA PUN, ditentukan
  // lewat parameter rute `:resource` (dan `:id` bila mode ubah). Jenis input
  // per kolom (teks/angka/tanggal/enum/checkbox) serta dropdown relasi
  // diturunkan otomatis dari metadata di lib/entities.meta.js.
  import { untrack } from 'svelte';
  import { push } from 'svelte-spa-router';
  import { findEntity, PHASE_LABELS } from '../lib/entities.meta.js';
  import { getResource, createResource, updateResource, listResource, ApiError } from '../lib/api.js';
  import { kolomLabelUtama } from '../lib/format.js';
  import { FASE_UI, labelEnum, labelField, labelEntitasRelasi } from '../lib/ui.js';
  import { tampilkanToast } from '../lib/toast.js';
  import ErrorBanner from '../components/ErrorBanner.svelte';
  import Icon from '../components/Icon.svelte';
  import NotFound from './NotFound.svelte';

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
    untrack(() => inisialisasi());
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
      tampilkanToast(modeUbah ? 'Perubahan berhasil disimpan.' : `${entityMeta.label} berhasil ditambahkan.`);
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
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } finally {
      menyimpan = false;
    }
  }

  /** Atribut yang ditampilkan di form (stempel waktu dikelola backend). */
  let atributForm = $derived(
    entityMeta ? entityMeta.attributes.filter((a) => a.name !== 'created_at' && a.name !== 'updated_at') : []
  );

  /** Teks panjang & checkbox memakai lebar penuh pada grid dua kolom. */
  function lebarPenuh(atribut) {
    return atribut.type === 'text' || (atribut.type === 'string' && (atribut.length ?? 0) > 150);
  }
</script>

{#snippet keterangan(field, teksBantu)}
  {#if errorField[field]}
    <p class="mt-1.5 text-xs text-error">{errorField[field]}</p>
  {:else if teksBantu}
    <p class="mt-1.5 text-xs text-base-content/50">{teksBantu}</p>
  {/if}
{/snippet}

{#if !entityMeta}
  <NotFound pesan={`Entitas "${params.resource}" tidak dikenali.`} />
{:else}
  {@const ui = FASE_UI[entityMeta.phase]}

  <div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-start gap-3">
      <a
        href={`#/${entityMeta.resourceType}`}
        class="btn btn-ghost btn-square btn-sm mt-1"
        aria-label="Kembali ke daftar {entityMeta.label}"
      >
        <Icon nama="arrow-left" />
      </a>
      <div>
        <span class="badge badge-sm badge-soft {ui.badge} mb-1 font-semibold">Fase {PHASE_LABELS[entityMeta.phase]}</span>
        <h1 class="text-2xl font-bold tracking-tight">{modeUbah ? 'Ubah' : 'Tambah'} {entityMeta.label}</h1>
        <p class="text-sm text-base-content/60">
          {modeUbah ? 'Perbarui informasi data di bawah ini.' : 'Lengkapi formulir berikut untuk menambahkan data baru.'}
          Kolom bertanda <span class="text-error">*</span> wajib diisi.
        </p>
      </div>
    </div>

    <ErrorBanner {error} />

    {#if memuat}
      <div class="rounded-box border border-base-300 bg-base-100 p-6">
        <div class="grid gap-6 md:grid-cols-2">
          {#each Array(6) as _}
            <div class="space-y-2">
              <div class="skeleton h-4 w-32"></div>
              <div class="skeleton h-10 w-full"></div>
            </div>
          {/each}
        </div>
      </div>
    {:else}
      <form class="rounded-box border border-base-300 bg-base-100 shadow-sm" onsubmit={tanganiSubmit}>
        <!-- Atribut -->
        <section class="p-5 md:p-6">
          <h2 class="font-semibold">Informasi {entityMeta.label}</h2>
          <p class="text-sm text-base-content/60">Data utama yang tersimpan pada entitas ini.</p>

          <div class="mt-5 grid gap-x-6 gap-y-5 md:grid-cols-2">
            {#each atributForm as atribut}
              {@const galat = Boolean(errorField[atribut.field])}
              <div class={lebarPenuh(atribut) ? 'md:col-span-2' : ''}>
                {#if atribut.type === 'boolean'}
                  <span class="mb-1.5 block text-sm font-medium">{atribut.label}</span>
                  <label class="flex h-10 cursor-pointer items-center gap-3">
                    <input
                      id={atribut.field}
                      type="checkbox"
                      class="toggle toggle-primary"
                      bind:checked={form[atribut.field]}
                    />
                    <span class="text-sm">{form[atribut.field] ? 'Ya' : 'Tidak'}</span>
                  </label>
                  {@render keterangan(atribut.field)}
                {:else}
                  <label for={atribut.field} class="mb-1.5 block text-sm font-medium">
                    {atribut.label}{#if !atribut.nullable}<span class="text-error"> *</span>{/if}
                  </label>

                  {#if atribut.type === 'enum'}
                    <select
                      id={atribut.field}
                      class="select w-full"
                      class:select-error={galat}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    >
                      {#if atribut.nullable || form[atribut.field] === ''}<option value="">— Pilih —</option>{/if}
                      {#each atribut.values as opsi}
                        <option value={opsi}>{labelEnum(opsi)}</option>
                      {/each}
                    </select>
                    {@render keterangan(atribut.field)}
                  {:else if atribut.type === 'text'}
                    <textarea
                      id={atribut.field}
                      rows="4"
                      class="textarea w-full"
                      class:textarea-error={galat}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    ></textarea>
                    {@render keterangan(atribut.field)}
                  {:else if atribut.type === 'date'}
                    <input
                      id={atribut.field}
                      type="date"
                      class="input w-full"
                      class:input-error={galat}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    />
                    {@render keterangan(atribut.field)}
                  {:else if atribut.type === 'decimal'}
                    <input
                      id={atribut.field}
                      type="number"
                      step="0.01"
                      class="input w-full"
                      class:input-error={galat}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    />
                    {@render keterangan(atribut.field, 'Angka desimal, gunakan titik sebagai pemisah.')}
                  {:else if atribut.type === 'year'}
                    <input
                      id={atribut.field}
                      type="number"
                      step="1"
                      min="1900"
                      max="2100"
                      placeholder="mis. {new Date().getFullYear()}"
                      class="input w-full"
                      class:input-error={galat}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    />
                    {@render keterangan(atribut.field)}
                  {:else if atribut.type === 'integer'}
                    <input
                      id={atribut.field}
                      type="number"
                      step="1"
                      class="input w-full"
                      class:input-error={galat}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    />
                    {@render keterangan(atribut.field)}
                  {:else}
                    <input
                      id={atribut.field}
                      type="text"
                      class="input w-full"
                      class:input-error={galat}
                      maxlength={atribut.length ?? undefined}
                      bind:value={form[atribut.field]}
                      required={!atribut.nullable}
                    />
                    {@render keterangan(
                      atribut.field,
                      atribut.length ? `${String(form[atribut.field] ?? '').length}/${atribut.length} karakter` : null
                    )}
                  {/if}
                {/if}
              </div>
            {/each}
          </div>
        </section>

        <!-- Relasi -->
        {#if entityMeta.relations.length}
          <section class="border-t border-base-300 p-5 md:p-6">
            <h2 class="font-semibold">Keterkaitan data</h2>
            <p class="text-sm text-base-content/60">Hubungkan data ini dengan entitas lain dalam siklus PPM.</p>

            <div class="mt-5 grid gap-x-6 gap-y-5 md:grid-cols-2">
              {#each entityMeta.relations as relasi}
                {@const galat = Boolean(errorField[relasi.field])}
                <div>
                  <label for={relasi.field} class="mb-1.5 block text-sm font-medium">
                    {labelField(relasi.field)}{#if !relasi.nullable}<span class="text-error"> *</span>{/if}
                  </label>
                  <select
                    id={relasi.field}
                    class="select w-full"
                    class:select-error={galat}
                    bind:value={relasiValues[relasi.field]}
                    required={!relasi.nullable}
                  >
                    <option value="">— Pilih {labelEntitasRelasi(relasi).toLowerCase()} —</option>
                    {#each opsiRelasi[relasi.field] ?? [] as opsi}
                      <option value={opsi.id}>{opsi.label}</option>
                    {/each}
                  </select>
                  {@render keterangan(
                    relasi.field,
                    (opsiRelasi[relasi.field] ?? []).length === 0
                      ? `Belum ada data ${labelEntitasRelasi(relasi)} yang dapat dipilih.`
                      : `Sumber: ${labelEntitasRelasi(relasi)}`
                  )}
                </div>
              {/each}
            </div>
          </section>
        {/if}

        <!-- Aksi -->
        <div class="sticky bottom-0 flex flex-col-reverse gap-2 rounded-b-box border-t border-base-300 bg-base-100/95 p-4 backdrop-blur sm:flex-row sm:justify-end md:px-6">
          <a class="btn btn-ghost" href={`#/${entityMeta.resourceType}`}>Batal</a>
          <button class="btn btn-primary sm:min-w-36" type="submit" disabled={menyimpan}>
            {#if menyimpan}
              <span class="loading loading-spinner loading-sm"></span> Menyimpan…
            {:else}
              <Icon nama="save" class="size-4" /> {modeUbah ? 'Simpan perubahan' : 'Simpan'}
            {/if}
          </button>
        </div>
      </form>
    {/if}
  </div>
{/if}
