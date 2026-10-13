<script>
  // Kontrol paginasi generik untuk ResourceList, mengikuti meta JSON:API
  // gaya `page[number]`/`page[size]` (Laravel JSON:API page-based paginator).
  import Icon from './Icon.svelte';

  let { page = 1, lastPage = 1, total = null, pageSize = 15, loading = false, onChange } = $props();

  // Nomor halaman yang ditampilkan: selalu halaman pertama & terakhir,
  // ditambah halaman di sekitar halaman aktif; celah ditandai "…".
  let nomorHalaman = $derived.by(() => {
    const hasil = [];
    for (let i = 1; i <= lastPage; i++) {
      if (i === 1 || i === lastPage || Math.abs(i - page) <= 1) hasil.push(i);
      else if (hasil[hasil.length - 1] !== '…') hasil.push('…');
    }
    return hasil;
  });

  let dari = $derived(total ? (page - 1) * pageSize + 1 : 0);
  let sampai = $derived(total ? Math.min(page * pageSize, total) : 0);
</script>

<div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
  <p class="text-sm text-base-content/60">
    {#if total !== null}
      Menampilkan <span class="font-semibold text-base-content">{dari}–{sampai}</span> dari
      <span class="font-semibold text-base-content">{total}</span> data
    {:else}
      Halaman <span class="font-semibold text-base-content">{page}</span> dari {lastPage}
    {/if}
  </p>

  {#if lastPage > 1}
    <div class="join">
      <button
        class="join-item btn btn-sm"
        aria-label="Halaman sebelumnya"
        disabled={page <= 1 || loading}
        onclick={() => onChange(page - 1)}
      >
        <Icon nama="chevron-left" class="size-4" />
      </button>
      {#each nomorHalaman as n}
        {#if n === '…'}
          <button class="join-item btn btn-sm btn-disabled hidden sm:inline-flex" tabindex="-1">…</button>
        {:else}
          <button
            class="join-item btn btn-sm {n === page ? 'btn-primary' : 'hidden sm:inline-flex'}"
            aria-current={n === page ? 'page' : undefined}
            disabled={loading}
            onclick={() => n !== page && onChange(n)}
          >
            {n}
          </button>
        {/if}
      {/each}
      <button
        class="join-item btn btn-sm"
        aria-label="Halaman berikutnya"
        disabled={page >= lastPage || loading}
        onclick={() => onChange(page + 1)}
      >
        <Icon nama="chevron-right" class="size-4" />
      </button>
    </div>
  {/if}
</div>
