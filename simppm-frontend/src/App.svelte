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

  // Halaman login ditampilkan tanpa sidebar/topbar (layar penuh).
  let tampilkanShell = $derived($session !== null && router.location !== '/login');
</script>

{#if tampilkanShell}
  <div class="app-shell">
    <Sidebar />
    <div class="app-main">
      <Topbar />
      <div class="app-content">
        <Router {routes} onConditionsFailed={tanganiConditionsFailed} />
      </div>
    </div>
  </div>
{:else}
  <Router {routes} onConditionsFailed={tanganiConditionsFailed} />
{/if}

<style>
  .app-shell {
    display: flex;
    min-height: 100vh;
  }
  .app-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
  }
  .app-content {
    flex: 1;
    padding: 24px;
    overflow-x: hidden;
  }
</style>
