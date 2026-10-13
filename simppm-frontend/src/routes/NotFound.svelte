<script>
  // Halaman fallback untuk rute yang tidak dikenali (route `*` pada App.svelte),
  // juga dipakai ResourceList/ResourceForm saat `:resource` tidak dikenal.
  import { session } from '../lib/auth.js';
  import Icon from '../components/Icon.svelte';

  let { pesan = 'Halaman yang Anda tuju tidak ditemukan atau telah dipindahkan.' } = $props();
</script>

<div class="flex items-center justify-center px-4 py-16 {$session ? '' : 'min-h-dvh'}">
  <div class="max-w-md text-center">
    <p class="text-7xl font-extrabold tracking-tight text-primary/20">404</p>
    <h1 class="mt-2 text-2xl font-bold">Halaman tidak ditemukan</h1>
    <p class="mt-2 text-base-content/60">{pesan}</p>
    <a class="btn btn-primary mt-6" href={$session ? '#/' : '#/login'}>
      <Icon nama="arrow-left" class="size-4" />
      {$session ? 'Kembali ke Dashboard' : 'Ke halaman masuk'}
    </a>
  </div>
</div>
