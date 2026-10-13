<script>
  // Navigasi utama: entitas dikelompokkan per fase siklus PPM. Di layar
  // besar tampil tetap (drawer terbuka), di layar kecil sebagai drawer
  // geser yang dibuka dari tombol menu di Topbar (lihat App.svelte).
  import { router } from 'svelte-spa-router';
  import { entitiesByPhase, PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';
  import { FASE_UI } from '../lib/ui.js';
  import Icon from './Icon.svelte';

  const grouped = entitiesByPhase();

  // Cocok untuk "/dosen", "/dosen/tambah", maupun "/dosen/1/ubah".
  function aktif(resourceType) {
    const lokasi = router.location;
    return lokasi === `/${resourceType}` || lokasi.startsWith(`/${resourceType}/`);
  }

  let faseAktif = $derived(
    PHASE_ORDER.find((phase) => grouped[phase].some((e) => aktif(e.resourceType))) ?? null
  );

  // Grup fase dapat dilipat; grup yang memuat menu aktif selalu dibuka.
  let faseTerbuka = $state(Object.fromEntries(PHASE_ORDER.map((p) => [p, true])));
  $effect(() => {
    if (faseAktif) faseTerbuka[faseAktif] = true;
  });
</script>

<aside class="flex min-h-full w-72 flex-col border-r border-base-300 bg-base-100">
  <a href="#/" class="flex items-center gap-3 px-5 py-5">
    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-primary text-primary-content shadow-sm">
      <Icon nama="graduation-cap" class="size-6" />
    </span>
    <span class="min-w-0 leading-tight">
      <span class="block text-lg font-extrabold tracking-tight">SIMPPM</span>
      <span class="block truncate text-xs text-base-content/60">Penelitian &amp; Pengabdian</span>
    </span>
  </a>

  <nav class="scrollbar-tipis flex-1 overflow-y-auto px-3 pb-6" aria-label="Menu utama">
    <ul class="menu w-full gap-0.5 p-0">
      <li>
        <a href="#/" class:menu-active={router.location === '/'} class="font-medium">
          <Icon nama="layout-dashboard" class="size-[1.1rem]" />
          Dashboard
        </a>
      </li>
    </ul>

    {#each PHASE_ORDER as phase}
      {@const ui = FASE_UI[phase]}
      <div class="mt-4">
        <button
          type="button"
          class="flex w-full items-center gap-2 rounded-lg px-3 py-1.5 text-left text-[0.7rem] font-bold tracking-wider text-base-content/50 uppercase hover:text-base-content"
          aria-expanded={faseTerbuka[phase]}
          onclick={() => (faseTerbuka[phase] = !faseTerbuka[phase])}
        >
          <span class="grid size-5 place-items-center rounded-md {ui.latar} {ui.teks}">
            <Icon nama={ui.ikon} class="size-3.5" strokeWidth={2.5} />
          </span>
          <span class="flex-1">{PHASE_LABELS[phase]}</span>
          <span class="badge badge-ghost badge-xs font-semibold">{grouped[phase].length}</span>
          <Icon
            nama="chevron-down"
            class="size-3.5 transition-transform {faseTerbuka[phase] ? '' : '-rotate-90'}"
          />
        </button>

        {#if faseTerbuka[phase]}
          <ul class="menu mt-1 w-full gap-0.5 p-0">
            {#each grouped[phase] as entity}
              <li>
                <a
                  href={`#/${entity.resourceType}`}
                  class="text-[0.84rem]"
                  class:menu-active={aktif(entity.resourceType)}
                  title={entity.label}
                >
                  <span class="truncate">{entity.label}</span>
                </a>
              </li>
            {/each}
          </ul>
        {/if}
      </div>
    {/each}
  </nav>

  <div class="border-t border-base-300 px-5 py-3 text-xs text-base-content/50">
    Siklus PPM: Masukan → Proses → Luaran → Dampak
  </div>
</aside>
