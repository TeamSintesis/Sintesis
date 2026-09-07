<script>
  let { label = 'Memuat…' } = $props();
</script>

<div style="display:flex;align-items:center;gap:10px;padding:24px 0;color:var(--color-text-muted);">
  <div class="spinner"></div>
  <span>{label}</span>
</div>
