<script>
  // Root shell aplikasi: menyusun rute (svelte-spa-router, mode hash) dan
  // menerapkan penjaga rute (route guard) -- rute selain /login memerlukan
  // sesi aktif (lihat lib/auth.js#isAuthenticated). Karena backend memakai
  // token Bearer tanpa cookie sesi, penjaga ini murni logika di sisi klien.
  import Router from 'svelte-spa-router';
  import { wrap } from 'svelte-spa-router/wrap';
  import { push, router } from 'svelte-spa-router';

  import { isAuthenticated, session } from './lib/auth.js';

  import Login from './routes/Login.svelte';
  import Dashboard from './routes/Dashboard.svelte';
  import ResourceList from './routes/ResourceList.svelte';
  import ResourceForm from './routes/ResourceForm.svelte';
  import NotFound from './routes/NotFound.svelte';

  import Sidebar from './components/Sidebar.svelte';
  import Topbar from './components/Topbar.svelte';
  import Toaster from './components/Toaster.svelte';

  /** Pre-condition rute: hanya lanjut bila pengguna sudah login. */
  const wajibLogin = () => isAuthenticated();

  const routes = {
    '/login': Login,
    '/': wrap({ component: Dashboard, conditions: [wajibLogin] }),
    '/:resource/tambah': wrap({ component: ResourceForm, conditions: [wajibLogin] }),
    '/:resource/:id/ubah': wrap({ component: ResourceForm, conditions: [wajibLogin] }),
    '/:resource': wrap({ component: ResourceList, conditions: [wajibLogin] }),
    '*': NotFound,
  };

  function tanganiConditionsFailed() {
    push('/login');
  }

  const ID_DRAWER = 'drawer-navigasi';

  // Halaman login ditampilkan tanpa sidebar/topbar (layar penuh).
  let tampilkanShell = $derived($session !== null && router.location !== '/login');

  // Drawer (layar kecil) ditutup otomatis setiap kali berpindah halaman.
  let drawerTerbuka = $state(false);
  $effect(() => {
    void router.location;
    drawerTerbuka = false;
  });
</script>

{#if tampilkanShell}
  <div class="drawer lg:drawer-open">
    <input id={ID_DRAWER} type="checkbox" class="drawer-toggle" bind:checked={drawerTerbuka} />
    <div class="drawer-content flex min-h-dvh min-w-0 flex-col">
      <Topbar idDrawer={ID_DRAWER} />
      <main class="flex-1 px-4 py-6 md:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-7xl">
          <Router {routes} onConditionsFailed={tanganiConditionsFailed} restoreScrollState />
        </div>
      </main>
      <footer class="px-4 py-4 text-center text-xs text-base-content/50 md:px-8">
        SIMPPM — Sistem Informasi Pengelolaan Penelitian dan Pengabdian kepada Masyarakat
      </footer>
    </div>
    <div class="drawer-side z-40">
      <label for={ID_DRAWER} aria-label="Tutup menu" class="drawer-overlay"></label>
      <Sidebar />
    </div>
  </div>
{:else}
  <Router {routes} onConditionsFailed={tanganiConditionsFailed} />
{/if}

<Toaster />
