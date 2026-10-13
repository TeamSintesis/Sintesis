<script>
  // Bilah atas: tombol menu (layar kecil), breadcrumb lokasi, pengalih
  // tema, dan menu pengguna (nama, peran, keluar).
  import { push, router } from 'svelte-spa-router';
  import { findEntity, PHASE_LABELS } from '../lib/entities.meta.js';
  import { session, logout, PERAN_LABELS } from '../lib/auth.js';
  import { tema, gantiTema, TEMA_GELAP } from '../lib/theme.js';
  import { inisial } from '../lib/ui.js';
  import Icon from './Icon.svelte';

  let { idDrawer } = $props();

  // Breadcrumb diturunkan dari lokasi rute: /:resource[/tambah | /:id/ubah].
  let breadcrumb = $derived.by(() => {
    const [resource, kedua, ketiga] = router.location.replace(/^\//, '').split('/');
    const entitas = resource ? findEntity(resource) : null;
    if (!entitas) return [{ label: 'Dashboard' }];
    const jejak = [
      { label: PHASE_LABELS[entitas.phase] },
      { label: entitas.label, href: `#/${entitas.resourceType}` },
    ];
    if (kedua === 'tambah') jejak.push({ label: 'Tambah' });
    else if (ketiga === 'ubah') jejak.push({ label: 'Ubah' });
    else jejak[1] = { label: entitas.label };
    return jejak;
  });

  let sedangKeluar = $state(false);

  async function keluar() {
    sedangKeluar = true;
    await logout();
    push('/login');
  }
</script>

<header class="navbar sticky top-0 z-30 min-h-16 gap-2 border-b border-base-300 bg-base-100/85 px-3 backdrop-blur md:px-6">
  <div class="flex min-w-0 flex-1 items-center gap-2">
    <label for={idDrawer} class="btn btn-ghost btn-square btn-sm lg:hidden" aria-label="Buka menu">
      <Icon nama="menu" />
    </label>

    <div class="breadcrumbs min-w-0 py-0 text-sm">
      <ul>
        <li>
          <a href="#/" class="text-base-content/60" aria-label="Beranda">
            <Icon nama="home" class="size-4" />
          </a>
        </li>
        {#each breadcrumb as item, i}
          <li class={i < breadcrumb.length - 1 ? 'hidden text-base-content/60 sm:inline-flex' : 'font-semibold'}>
            {#if item.href}<a href={item.href}>{item.label}</a>{:else}<span class="truncate">{item.label}</span>{/if}
          </li>
        {/each}
      </ul>
    </div>
  </div>

  <div class="flex items-center gap-1">
    <button
      class="btn btn-ghost btn-square btn-sm"
      onclick={gantiTema}
      aria-label={$tema === TEMA_GELAP ? 'Gunakan tema terang' : 'Gunakan tema gelap'}
      title={$tema === TEMA_GELAP ? 'Tema terang' : 'Tema gelap'}
    >
      <Icon nama={$tema === TEMA_GELAP ? 'sun' : 'moon'} />
    </button>

    {#if $session}
      <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-ghost h-auto gap-3 px-2 py-1.5" aria-label="Menu pengguna">
          <div class="avatar avatar-placeholder">
            <div class="w-9 rounded-full bg-primary text-primary-content">
              <span class="text-sm font-bold">{inisial($session.user.name)}</span>
            </div>
          </div>
          <div class="hidden text-left leading-tight md:block">
            <div class="max-w-40 truncate text-sm font-semibold">{$session.user.name}</div>
            <div class="text-xs font-normal text-base-content/60">
              {PERAN_LABELS[$session.user.peran] ?? $session.user.peran}
            </div>
          </div>
          <Icon nama="chevron-down" class="hidden size-4 text-base-content/50 md:block" />
        </div>
        <div tabindex="-1" class="dropdown-content z-40 mt-2 w-64 rounded-box border border-base-300 bg-base-100 p-2 shadow-xl">
          <div class="px-3 py-2">
            <div class="truncate font-semibold">{$session.user.name}</div>
            {#if $session.user.email}
              <div class="truncate text-xs text-base-content/60">{$session.user.email}</div>
            {/if}
            <span class="badge badge-primary badge-soft badge-sm mt-2">
              {PERAN_LABELS[$session.user.peran] ?? $session.user.peran}
            </span>
          </div>
          <div class="divider my-1"></div>
          <ul class="menu w-full p-0">
            <li>
              <button class="text-error" onclick={keluar} disabled={sedangKeluar}>
                {#if sedangKeluar}
                  <span class="loading loading-spinner loading-xs"></span>
                {:else}
                  <Icon nama="log-out" class="size-4" />
                {/if}
                Keluar
              </button>
            </li>
          </ul>
        </div>
      </div>
    {/if}
  </div>
</header>
