<script>
  // Menampilkan pesan error berformat JSON:API (array `errors`) yang
  // dilempar oleh lib/api.js (lihat class ApiError), atau string biasa.
  import Icon from './Icon.svelte';

  let { error = null } = $props();

  function pesanUntuk(err) {
    if (!err) return [];
    if (Array.isArray(err.errors) && err.errors.length) {
      return err.errors.map((e) => e.detail || e.title || 'Terjadi kesalahan.');
    }
    return [err.message || 'Terjadi kesalahan yang tidak diketahui.'];
  }

  function judulUntuk(err) {
    if (err?.status === 403) return 'Akses ditolak';
    if (err?.status === 422) return 'Data belum valid';
    if (err?.status === 404) return 'Data tidak ditemukan';
    return 'Terjadi kesalahan';
  }
</script>

{#if error}
  <div class="alert alert-error alert-soft mb-4 items-start" role="alert">
    <Icon nama="alert-circle" class="mt-0.5 size-5" />
    <div>
      <h4 class="font-semibold">{judulUntuk(error)}</h4>
      <ul class="mt-1 space-y-0.5 text-sm">
        {#each pesanUntuk(error) as pesan}
          <li>{pesan}</li>
        {/each}
      </ul>
    </div>
  </div>
{/if}
