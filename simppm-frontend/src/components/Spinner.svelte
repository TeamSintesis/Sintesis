<script>
  let { label = 'Memuat…' } = $props();
</script>

<div class="flex items-center justify-center gap-3 py-10 text-base-content/60">
  <span class="loading loading-spinner loading-md text-primary"></span>
  <span class="text-sm">{label}</span>
</div>
