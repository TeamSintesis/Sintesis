<script>
  // Komponen generik: menampilkan daftar (tabel + paginasi) untuk ENTITAS
  // APA PUN, ditentukan lewat parameter rute `:resource`. Kolom tabel dan
  // format nilai diturunkan otomatis dari metadata di lib/entities.meta.js,
  // sehingga tidak perlu membuat komponen tabel khusus per entitas.
  import { findEntity } from '../lib/entities.meta.js';
  import { listResource, deleteResource } from '../lib/api.js';
  import { formatNilaiTabel, potongTeks, bangunLookupIncluded, labelRelasi } from '../lib/format.js';
  import ErrorBanner from '../components/ErrorBanner.svelte';
  import Spinner from '../components/Spinner.svelte';
  import Pagination from '../components/Pagination.svelte';

  let { params } = $props();

  let entityMeta = $derived(findEntity(params.resource));
  /** Kolom atribut yang ditampilkan di tabel (sembunyikan stempel waktu). */
  let kolomAtribut = $derived(
    entityMeta ? entityMeta.attributes.filter((a) => a.name !== 'created_at' && a.name !== 'updated_at') : []
  );

  let rows = $state([]);
  let includedLookup = $state({});
  let halaman = $state(1);
  let halamanTerakhir = $state(1);
  let memuat = $state(true);
  let error = $state(null);
  let sedangHapusId = $state(null);

  async function muatDaftar(nomorHalaman) {
    if (!entityMeta) return;
    memuat = true;
    error = null;
    try {
      const includeFields = entityMeta.relations.map((r) => r.field);
      const payload = await listResource(entityMeta.resourceType, {
        page: nomorHalaman,
        pageSize: 15,
        include: includeFields,
      });
      rows = payload.data ?? [];
      includedLookup = bangunLookupIncluded(payload);
      halaman = payload?.meta?.page?.currentPage ?? nomorHalaman;
      halamanTerakhir = payload?.meta?.page?.lastPage ?? 1;
    } catch (err) {
      error = err;
      rows = [];
    } finally {
      memuat = false;
    }
  }

  // Muat ulang setiap kali entitas yang aktif berganti (navigasi menu) atau
  // halaman berubah. `params.resource` dibaca di dalam efek agar Svelte
  // melacaknya sebagai dependensi reaktif.
  $effect(() => {
    const resource = params.resource;
    if (resource) muatDaftar(1);
  });

  async function hapusBaris(row) {
    if (!confirm('Hapus data ini? Tindakan tidak dapat dibatalkan.')) return;
    sedangHapusId = row.id;
    try {
      await deleteResource(entityMeta.resourceType, row.id);
      await muatDaftar(halaman);
    } catch (err) {
      error = err;
    } finally {
      sedangHapusId = null;
    }
  }

  function nilaiSel(row, atribut) {
    const mentah = row.attributes?.[atribut.field];
    const teks = formatNilaiTabel(atribut, mentah);
    return atribut.type === 'text' || atribut.type === 'string' ? potongTeks(teks, 80) : teks;
  }

</script>

{#if !entityMeta}
  <div class="alert alert-danger">Entitas "{params.resource}" tidak dikenali.</div>
{:else}
  <div class="list-header">
    <div>
      <h1 id="PageTitle" style="font-size:20px;">{entityMeta.label}</h1>
      <p class="muted" style="margin:2px 0 0;font-size:13px;">
        {halamanTerakhir > 1 ? `Halaman ${halaman} dari ${halamanTerakhir}` : `${rows.length} data`}
      </p>
    </div>
    <a class="btn btn-primary" href={`#/${entityMeta.resourceType}/tambah`}>+ Tambah</a>
  </div>

  <ErrorBanner {error} />

  <div class="card">
    {#if memuat}
      <div style="padding:0 16px;"><Spinner label="Memuat data…" /></div>
    {:else if rows.length === 0}
      <p class="muted" style="padding:24px;text-align:center;">Belum ada data.</p>
    {:else}
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              {#each kolomAtribut as atribut}
                <th>{atribut.label}</th>
              {/each}
              {#each entityMeta.relations as relasi}
                <th>{relasi.field}</th>
              {/each}
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {#each rows as row (row.id)}
              <tr>
                {#each kolomAtribut as atribut}
                  <td>{nilaiSel(row, atribut)}</td>
                {/each}
                {#each entityMeta.relations as relasi}
                  <td>{labelRelasi(row, relasi, includedLookup)}</td>
                {/each}
                <td style="white-space:nowrap;">
                  <a class="btn btn-small" href={`#/${entityMeta.resourceType}/${row.id}/ubah`}>Ubah</a>
                  <button
                    class="btn btn-small btn-danger"
                    style="margin-left:6px;"
                    disabled={sedangHapusId === row.id}
                    onclick={() => hapusBaris(row)}
                  >
                    {sedangHapusId === row.id ? 'Menghapus…' : 'Hapus'}
                  </button>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    {/if}
  </div>

  {#if !memuat && rows.length > 0}
    <Pagination page={halaman} hasMore={halaman < halamanTerakhir} loading={memuat} onChange={muatDaftar} />
  {/if}
{/if}

<style>
  .list-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 16px;
  }
</style>
