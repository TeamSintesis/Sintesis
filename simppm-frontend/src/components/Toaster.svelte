<script>
  // Wadah toast global (lib/toast.js), diletakkan sekali di App.svelte.
  import { daftarToast, tutupToast } from '../lib/toast.js';
  import Icon from './Icon.svelte';

  const GAYA = {
    sukses: { kelas: 'alert-success', ikon: 'check-circle' },
    gagal: { kelas: 'alert-error', ikon: 'alert-circle' },
    info: { kelas: 'alert-info', ikon: 'info' },
  };
</script>

<div class="toast toast-end toast-bottom z-50" aria-live="polite">
  {#each $daftarToast as t (t.id)}
    <div role="status" class="alert {GAYA[t.jenis]?.kelas ?? 'alert-info'} shadow-lg">
      <Icon nama={GAYA[t.jenis]?.ikon ?? 'info'} />
      <span class="text-sm font-medium">{t.pesan}</span>
      <button class="btn btn-ghost btn-xs btn-square" aria-label="Tutup" onclick={() => tutupToast(t.id)}>
        <Icon nama="x" class="size-4" />
      </button>
    </div>
  {/each}
</div>
