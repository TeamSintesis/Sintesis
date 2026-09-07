<script>
  // Kontrol paginasi generik untuk ResourceList, mengikuti meta JSON:API
  // gaya `page[number]`/`page[size]` (Laravel JSON:API page-based paginator).
  let { page = 1, hasMore = false, loading = false, onChange } = $props();
</script>

<div style="display:flex;align-items:center;gap:12px;justify-content:flex-end;margin-top:12px;">
  <span class="muted" style="font-size:13px;">Halaman {page}</span>
  <button class="btn btn-small" disabled={page <= 1 || loading} onclick={() => onChange(page - 1)}>
    ← Sebelumnya
  </button>
  <button class="btn btn-small" disabled={!hasMore || loading} onclick={() => onChange(page + 1)}>
    Berikutnya →
  </button>
</div>
