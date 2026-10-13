<script>
  // Dialog konfirmasi (modal daisyUI) pengganti window.confirm(), dipakai
  // untuk aksi yang tidak dapat dibatalkan seperti menghapus data.
  import Icon from './Icon.svelte';

  let {
    terbuka = false,
    judul = 'Konfirmasi',
    pesan = '',
    labelKonfirmasi = 'Ya, lanjutkan',
    memproses = false,
    onKonfirmasi,
    onBatal,
  } = $props();

  /** @type {HTMLDialogElement} */
  let dialog;

  $effect(() => {
    if (!dialog) return;
    if (terbuka && !dialog.open) dialog.showModal();
    if (!terbuka && dialog.open) dialog.close();
  });
</script>

<dialog bind:this={dialog} class="modal modal-bottom sm:modal-middle" onclose={() => terbuka && onBatal?.()}>
  <div class="modal-box">
    <div class="flex items-start gap-4">
      <div class="grid size-11 shrink-0 place-items-center rounded-full bg-error/10 text-error">
        <Icon nama="trash" />
      </div>
      <div>
        <h3 class="text-lg font-bold">{judul}</h3>
        <p class="mt-1 text-sm text-base-content/70">{pesan}</p>
      </div>
    </div>
    <div class="modal-action">
      <button class="btn btn-ghost" onclick={() => onBatal?.()} disabled={memproses}>Batal</button>
      <button class="btn btn-error" onclick={() => onKonfirmasi?.()} disabled={memproses}>
        {#if memproses}<span class="loading loading-spinner loading-sm"></span>{/if}
        {labelKonfirmasi}
      </button>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button aria-label="Tutup">tutup</button>
  </form>
</dialog>
