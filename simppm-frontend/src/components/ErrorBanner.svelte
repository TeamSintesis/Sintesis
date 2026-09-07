<script>
  // Menampilkan pesan error berformat JSON:API (array `errors`) yang
  // dilempar oleh lib/api.js (lihat class ApiError), atau string biasa.
  let { error = null } = $props();

  function pesanUntuk(err) {
    if (!err) return [];
    if (Array.isArray(err.errors) && err.errors.length) {
      return err.errors.map((e) => e.detail || e.title || 'Terjadi kesalahan.');
    }
    return [err.message || 'Terjadi kesalahan yang tidak diketahui.'];
  }
</script>

{#if error}
  <div class="alert alert-danger" role="alert">
    {#each pesanUntuk(error) as pesan}
      <div>{pesan}</div>
    {/each}
  </div>
{/if}
