<script>
  // Kontrol paginasi generik untuk ResourceList, mengikuti meta JSON:API
  // gaya `page[number]`/`page[size]` (Laravel JSON:API page-based paginator).
  let { page = 1, hasMore = false, loading = false, onChange } = $props();
</script>

<div style="display:flex;align-items:center;gap:10px;justify-content:flex-end;margin-top:16px;">
  <button class="btn btn-small" disabled={page <= 1 || loading} onclick={() => onChange(page - 1)}>
    ← Sebelumnya
  </button>
  <span class="muted" style="font-size:13px;">Halaman <strong>{page}</strong></span>
  <button class="btn btn-small" disabled={!hasMore || loading} onclick={() => onChange(page + 1)}>
    Berikutnya →
  </button>
</div>

<style>
  .muted {
    color: #6b7280;
    user-select: none;
  }

  .muted strong {
    color: #111827;
    font-weight: 600;
  }

  .btn.btn-small {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    line-height: 1;
    color: #374151;
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: all 0.15s ease-in-out;
  }

  .btn.btn-small:hover:not(:disabled) {
    background-color: #f9fafb;
    border-color: #9ca3af;
    color: #111827;
  }

  .btn.btn-small:focus-visible {
    outline: 2px solid #2563eb;
    outline-offset: 1px;
  }

  .btn.btn-small:disabled {
    opacity: 0.55;
    cursor: not-allowed;
    background-color: #f3f4f6;
    border-color: #e5e7eb;
    box-shadow: none;
  }
</style>