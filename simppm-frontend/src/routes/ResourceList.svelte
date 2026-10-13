<script>
  // Komponen generik: menampilkan daftar (tabel + paginasi) untuk ENTITAS
  // APA PUN, ditentukan lewat parameter rute `:resource`. Kolom tabel dan
  // format nilai diturunkan otomatis dari metadata di lib/entities.meta.js,
  // sehingga tidak perlu membuat komponen tabel khusus per entitas.
  // Layar kecil menampilkan kartu per baris alih-alih tabel lebar.
  import { untrack } from 'svelte';
  import { findEntity, PHASE_LABELS } from '../lib/entities.meta.js';
  import { listResource, deleteResource } from '../lib/api.js';
  import { formatNilaiTabel, potongTeks, bangunLookupIncluded, labelRelasi, kolomLabelUtama } from '../lib/format.js';
  import { FASE_UI, kelasBadgeEnum, labelEnum, labelField } from '../lib/ui.js';
  import { tampilkanToast } from '../lib/toast.js';
  import ErrorBanner from '../components/ErrorBanner.svelte';
  import Pagination from '../components/Pagination.svelte';
  import ConfirmDialog from '../components/ConfirmDialog.svelte';
  import Icon from '../components/Icon.svelte';
  import NotFound from './NotFound.svelte';

  let { params } = $props();

  let entityMeta = $derived(findEntity(params.resource));
  /** Kolom atribut yang ditampilkan di tabel (sembunyikan stempel waktu). */
  let kolomAtribut = $derived(
    entityMeta ? entityMeta.attributes.filter((a) => a.name !== 'created_at' && a.name !== 'updated_at') : []
  );
  let kolomJudul = $derived(entityMeta ? kolomLabelUtama(entityMeta) : null);

  const PILIHAN_UKURAN = [10, 15, 25, 50];

  let rows = $state([]);
  let includedLookup = $state({});
  let halaman = $state(1);
  let halamanTerakhir = $state(1);
  let totalData = $state(null);
  let ukuranHalaman = $state(15);
  let kataKunci = $state('');
  let memuat = $state(true);
  let error = $state(null);

  /** Baris yang sedang dikonfirmasi untuk dihapus. */
  let barisHapus = $state(null);
  let sedangHapus = $state(false);

  async function muatDaftar(nomorHalaman) {
    if (!entityMeta) return;
    memuat = true;
    error = null;
    try {
      const includeFields = entityMeta.relations.map((r) => r.field);
      const payload = await listResource(entityMeta.resourceType, {
        page: nomorHalaman,
        pageSize: ukuranHalaman,
        include: includeFields,
      });
      rows = payload.data ?? [];
      includedLookup = bangunLookupIncluded(payload);
      halaman = payload?.meta?.page?.currentPage ?? nomorHalaman;
      halamanTerakhir = payload?.meta?.page?.lastPage ?? 1;
      totalData = payload?.meta?.page?.total ?? null;
    } catch (err) {
      error = err;
      rows = [];
    } finally {
      memuat = false;
    }
  }

  // Muat ulang setiap kali entitas yang aktif berganti (navigasi menu).
  // `params.resource` dibaca di dalam efek agar Svelte melacaknya sebagai
  // dependensi reaktif; sisanya dijalankan tanpa pelacakan agar perubahan
  // state lain (mis. ukuran halaman) tidak memicu pemuatan ganda.
  $effect(() => {
    const resource = params.resource;
    untrack(() => {
      kataKunci = '';
      if (resource) muatDaftar(1);
    });
  });

  function gantiUkuran(event) {
    ukuranHalaman = Number(event.currentTarget.value);
    muatDaftar(1);
  }

  async function konfirmasiHapus() {
    const row = barisHapus;
    sedangHapus = true;
    try {
      await deleteResource(entityMeta.resourceType, row.id);
      barisHapus = null;
      tampilkanToast(`${entityMeta.label} berhasil dihapus.`);
      // Bila baris terakhir di halaman terakhir dihapus, mundur satu halaman.
      const tujuan = rows.length === 1 && halaman > 1 ? halaman - 1 : halaman;
      await muatDaftar(tujuan);
    } catch (err) {
      barisHapus = null;
      error = err;
      tampilkanToast('Gagal menghapus data.', 'gagal');
    } finally {
      sedangHapus = false;
    }
  }

  function judulBaris(row) {
    const nilai = kolomJudul ? row.attributes?.[kolomJudul] : null;
    return nilai ? String(nilai) : `#${row.id}`;
  }

  // Pencarian cepat di sisi klien, hanya terhadap data halaman aktif.
  let barisTampil = $derived.by(() => {
    const q = kataKunci.trim().toLowerCase();
    if (!q || !entityMeta) return rows;
    return rows.filter((row) => {
      const teks = [
        ...kolomAtribut.map((a) => formatNilaiTabel(a, row.attributes?.[a.field])),
        ...entityMeta.relations.map((r) => labelRelasi(row, r, includedLookup)),
      ]
        .join(' ')
        .toLowerCase();
      return teks.includes(q);
    });
  });
</script>

{#snippet nilaiSel(row, atribut, panjang)}
  {@const mentah = row.attributes?.[atribut.field]}
  {#if mentah === null || mentah === undefined || mentah === ''}
    <span class="text-base-content/30">—</span>
  {:else if atribut.type === 'enum'}
    <span class="badge badge-sm whitespace-nowrap {kelasBadgeEnum(mentah)}">{labelEnum(mentah)}</span>
  {:else if atribut.type === 'boolean'}
    <span class="badge badge-sm {mentah ? 'badge-success badge-soft' : 'badge-ghost'}">{mentah ? 'Ya' : 'Tidak'}</span>
  {:else if atribut.type === 'decimal' || atribut.type === 'integer'}
    <span class="tabular-nums">{formatNilaiTabel(atribut, mentah)}</span>
  {:else if atribut.type === 'date'}
    <span class="whitespace-nowrap">{formatNilaiTabel(atribut, mentah)}</span>
  {:else}
    {potongTeks(formatNilaiTabel(atribut, mentah), panjang)}
  {/if}
{/snippet}

{#snippet tombolAksi(row)}
  <div class="flex justify-end gap-1">
    <a
      class="btn btn-ghost btn-sm btn-square tooltip tooltip-left"
      data-tip="Ubah"
      href={`#/${entityMeta.resourceType}/${row.id}/ubah`}
      aria-label="Ubah {judulBaris(row)}"
    >
      <Icon nama="pencil" class="size-4" />
    </a>
    <button
      class="btn btn-ghost btn-sm btn-square tooltip tooltip-left text-error"
      data-tip="Hapus"
      aria-label="Hapus {judulBaris(row)}"
      onclick={() => (barisHapus = row)}
    >
      <Icon nama="trash" class="size-4" />
    </button>
  </div>
{/snippet}

{#if !entityMeta}
  <NotFound pesan={`Entitas "${params.resource}" tidak dikenali.`} />
{:else}
  {@const ui = FASE_UI[entityMeta.phase]}

  <!-- Judul halaman -->
  <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="flex items-start gap-3">
      <span class="grid size-11 shrink-0 place-items-center rounded-xl {ui.latar} {ui.teks}">
        <Icon nama={ui.ikon} />
      </span>
      <div>
        <span class="badge badge-sm badge-soft {ui.badge} mb-1 font-semibold">Fase {PHASE_LABELS[entityMeta.phase]}</span>
        <h1 class="text-2xl font-bold tracking-tight">{entityMeta.label}</h1>
        <p class="text-sm text-base-content/60">
          {#if totalData !== null}{totalData} data tercatat{:else}Daftar data {entityMeta.label.toLowerCase()}{/if}
        </p>
      </div>
    </div>
    <div class="flex gap-2">
      <button
        class="btn btn-square"
        onclick={() => muatDaftar(halaman)}
        disabled={memuat}
        aria-label="Muat ulang"
        title="Muat ulang"
      >
        <Icon nama="refresh" class="size-4 {memuat ? 'animate-spin' : ''}" />
      </button>
      <a class="btn btn-primary flex-1 sm:flex-none" href={`#/${entityMeta.resourceType}/tambah`}>
        <Icon nama="plus" class="size-4" /> Tambah data
      </a>
    </div>
  </div>

  <ErrorBanner {error} />

  <div class="rounded-box border border-base-300 bg-base-100 shadow-sm">
    <!-- Bilah alat -->
    <div class="flex flex-col gap-3 border-b border-base-300 p-4 sm:flex-row sm:items-center sm:justify-between">
      <label class="input input-sm w-full sm:max-w-xs">
        <Icon nama="search" class="size-4 opacity-50" />
        <input type="search" placeholder="Cari di halaman ini…" bind:value={kataKunci} />
      </label>
      <label class="flex items-center gap-2 text-sm text-base-content/60">
        Tampilkan
        <select class="select select-sm w-20" value={ukuranHalaman} onchange={gantiUkuran}>
          {#each PILIHAN_UKURAN as n}
            <option value={n}>{n}</option>
          {/each}
        </select>
        per halaman
      </label>
    </div>

    {#if memuat && rows.length === 0}
      <div class="space-y-3 p-4">
        {#each Array(5) as _}
          <div class="skeleton h-10 w-full"></div>
        {/each}
      </div>
    {:else if barisTampil.length === 0}
      <div class="flex flex-col items-center px-6 py-16 text-center">
        <span class="grid size-14 place-items-center rounded-full bg-base-200 text-base-content/40">
          <Icon nama={kataKunci ? 'search' : 'database'} class="size-7" />
        </span>
        {#if kataKunci}
          <h3 class="mt-4 font-semibold">Tidak ada hasil untuk “{kataKunci}”</h3>
          <p class="mt-1 text-sm text-base-content/60">Pencarian hanya mencakup data pada halaman ini.</p>
          <button class="btn btn-ghost btn-sm mt-4" onclick={() => (kataKunci = '')}>Hapus pencarian</button>
        {:else}
          <h3 class="mt-4 font-semibold">Belum ada data</h3>
          <p class="mt-1 text-sm text-base-content/60">Mulai dengan menambahkan data {entityMeta.label.toLowerCase()} pertama.</p>
          <a class="btn btn-primary btn-sm mt-4" href={`#/${entityMeta.resourceType}/tambah`}>
            <Icon nama="plus" class="size-4" /> Tambah data
          </a>
        {/if}
      </div>
    {:else}
      <!-- Tabel (layar sedang ke atas) -->
      <div class="scrollbar-tipis hidden overflow-x-auto md:block" class:opacity-60={memuat}>
        <table class="table">
          <thead>
            <tr class="bg-base-200/60 text-xs tracking-wide text-base-content/60 uppercase">
              <th class="w-12">#</th>
              {#each kolomAtribut as atribut}
                <th class="whitespace-nowrap">{atribut.label}</th>
              {/each}
              {#each entityMeta.relations as relasi}
                <th class="whitespace-nowrap">{labelField(relasi.field)}</th>
              {/each}
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {#each barisTampil as row, i (row.id)}
              <tr class="hover:bg-base-200/50">
                <td class="text-base-content/40 tabular-nums">{(halaman - 1) * ukuranHalaman + i + 1}</td>
                {#each kolomAtribut as atribut}
                  <td class={atribut.field === kolomJudul ? 'min-w-48 font-medium' : 'min-w-24'}>
                    {@render nilaiSel(row, atribut, 80)}
                  </td>
                {/each}
                {#each entityMeta.relations as relasi}
                  <td class="min-w-32">
                    {#if row.relationships?.[relasi.field]?.data}
                      <span class="inline-flex items-center gap-1.5 text-base-content/80">
                        <Icon nama="link" class="size-3.5 shrink-0 text-base-content/40" />
                        {potongTeks(labelRelasi(row, relasi, includedLookup), 50)}
                      </span>
                    {:else}
                      <span class="text-base-content/30">—</span>
                    {/if}
                  </td>
                {/each}
                <td>{@render tombolAksi(row)}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>

      <!-- Kartu (layar kecil) -->
      <ul class="divide-y divide-base-300 md:hidden" class:opacity-60={memuat}>
        {#each barisTampil as row (row.id)}
          <li class="p-4">
            <div class="flex items-start justify-between gap-3">
              <h3 class="min-w-0 font-semibold break-words">{potongTeks(judulBaris(row), 90)}</h3>
              {@render tombolAksi(row)}
            </div>
            <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1.5 text-sm">
              {#each kolomAtribut.filter((a) => a.field !== kolomJudul) as atribut}
                <dt class="text-base-content/50">{atribut.label}</dt>
                <dd class="min-w-0 break-words">{@render nilaiSel(row, atribut, 60)}</dd>
              {/each}
              {#each entityMeta.relations as relasi}
                <dt class="text-base-content/50">{labelField(relasi.field)}</dt>
                <dd class="min-w-0 break-words">{labelRelasi(row, relasi, includedLookup)}</dd>
              {/each}
            </dl>
          </li>
        {/each}
      </ul>
    {/if}

    {#if rows.length > 0}
      <div class="border-t border-base-300 p-4">
        <Pagination
          page={halaman}
          lastPage={halamanTerakhir}
          total={totalData}
          pageSize={ukuranHalaman}
          loading={memuat}
          onChange={muatDaftar}
        />
      </div>
    {/if}
  </div>

  <ConfirmDialog
    terbuka={barisHapus !== null}
    judul="Hapus data?"
    pesan={barisHapus
      ? `Data “${potongTeks(judulBaris(barisHapus), 60)}” pada ${entityMeta.label} akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`
      : ''}
    labelKonfirmasi="Hapus"
    memproses={sedangHapus}
    onKonfirmasi={konfirmasiHapus}
    onBatal={() => (barisHapus = null)}
  />
{/if}
