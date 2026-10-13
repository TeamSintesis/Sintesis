<script>
  // Ringkasan jumlah data setiap entitas, dikelompokkan per fase siklus
  // PPM. Jumlah diambil dari meta paginasi JSON:API (page[size]=1).
  import { onMount } from 'svelte';
  import { entitiesByPhase, PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';
  import { listResource } from '../lib/api.js';
  import { session, PERAN_LABELS } from '../lib/auth.js';
  import { FASE_UI, salam } from '../lib/ui.js';
  import Icon from '../components/Icon.svelte';

  const grouped = entitiesByPhase();

  /** resourceType -> jumlah data, atau null bila gagal dimuat (mis. 403). */
  let totals = $state({});
  let memuat = $state(true);

  const tanggalHariIni = new Intl.DateTimeFormat('id-ID', { dateStyle: 'full' }).format(new Date());

  onMount(async () => {
    const semuaEntitas = PHASE_ORDER.flatMap((p) => grouped[p]);
    await Promise.all(
      semuaEntitas.map(async (entity) => {
        try {
          const payload = await listResource(entity.resourceType, { page: 1, pageSize: 1 });
          totals[entity.resourceType] = payload?.meta?.page?.total ?? 0;
        } catch {
          totals[entity.resourceType] = null;
        }
      })
    );
    memuat = false;
  });

  function totalFase(phase) {
    return grouped[phase].reduce((jumlah, e) => jumlah + (totals[e.resourceType] ?? 0), 0);
  }

  let totalSemua = $derived(PHASE_ORDER.reduce((jumlah, p) => jumlah + totalFase(p), 0));

  function gulirKe(phase) {
    document.getElementById(`fase-${phase}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  const angka = (n) => new Intl.NumberFormat('id-ID').format(n);
</script>

<div class="space-y-8">
  <!-- Sambutan -->
  <section class="relative overflow-hidden rounded-box bg-gradient-to-br from-primary to-secondary p-6 text-primary-content shadow-sm md:p-8">
    <div class="pointer-events-none absolute -top-16 -right-10 size-64 rounded-full bg-white/10 blur-2xl"></div>
    <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-sm text-primary-content/80">{tanggalHariIni}</p>
        <h1 class="mt-1 text-2xl font-extrabold tracking-tight md:text-3xl">
          {salam()}, {$session?.user?.name ?? 'Pengguna'}
        </h1>
        <p class="mt-2 max-w-xl text-sm text-primary-content/85 md:text-base">
          Anda masuk sebagai
          <span class="badge badge-sm border-0 bg-white/20 font-semibold text-primary-content">
            {PERAN_LABELS[$session?.user?.peran] ?? $session?.user?.peran}
          </span>. Pantau dan kelola data seluruh siklus penelitian &amp; pengabdian kepada masyarakat.
        </p>
      </div>
      <div class="rounded-box bg-white/15 px-5 py-4 backdrop-blur md:text-right">
        <div class="text-xs font-semibold tracking-wider text-primary-content/80 uppercase">Total data</div>
        <div class="mt-1 text-3xl font-extrabold tabular-nums">
          {#if memuat}<span class="loading loading-dots loading-md"></span>{:else}{angka(totalSemua)}{/if}
        </div>
      </div>
    </div>
  </section>

  <!-- Siklus PPM -->
  <section>
    <h2 class="mb-3 text-sm font-bold tracking-wider text-base-content/50 uppercase">Siklus PPM</h2>
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
      {#each PHASE_ORDER as phase, i}
        {@const ui = FASE_UI[phase]}
        <button
          type="button"
          onclick={() => gulirKe(phase)}
          class="group relative rounded-box border border-base-300 border-t-4 {ui.garis} bg-base-100 p-4 text-left transition hover:-translate-y-0.5 hover:shadow-md md:p-5"
        >
          <div class="flex items-center justify-between">
            <span class="grid size-10 place-items-center rounded-xl {ui.latar} {ui.teks}">
              <Icon nama={ui.ikon} />
            </span>
            <span class="text-xs font-semibold text-base-content/40">Fase {i + 1}</span>
          </div>
          <div class="mt-4 font-bold">{PHASE_LABELS[phase]}</div>
          <div class="mt-1 flex items-baseline gap-1.5">
            <span class="text-2xl font-extrabold tabular-nums">
              {#if memuat}<span class="skeleton inline-block h-7 w-12 align-middle"></span>{:else}{angka(totalFase(phase))}{/if}
            </span>
            <span class="text-xs text-base-content/50">data · {grouped[phase].length} entitas</span>
          </div>
          {#if i < PHASE_ORDER.length - 1}
            <span class="absolute top-1/2 -right-3 z-10 hidden -translate-y-1/2 rounded-full border border-base-300 bg-base-100 p-1 text-base-content/40 lg:block">
              <Icon nama="chevron-right" class="size-3.5" />
            </span>
          {/if}
        </button>
      {/each}
    </div>
  </section>

  <!-- Rincian per fase -->
  {#each PHASE_ORDER as phase}
    {@const ui = FASE_UI[phase]}
    <section id="fase-{phase}" class="scroll-mt-24">
      <div class="mb-4 flex items-start gap-3">
        <span class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg {ui.latar} {ui.teks}">
          <Icon nama={ui.ikon} class="size-[1.1rem]" />
        </span>
        <div>
          <h2 class="text-lg font-bold">{PHASE_LABELS[phase]}</h2>
          <p class="text-sm text-base-content/60">{ui.deskripsi}</p>
        </div>
      </div>

      <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        {#each grouped[phase] as entity}
          {@const total = totals[entity.resourceType]}
          <div class="group flex items-center gap-4 rounded-box border border-base-300 bg-base-100 p-4 transition hover:border-primary/40 hover:shadow-sm">
            <a href={`#/${entity.resourceType}`} class="min-w-0 flex-1">
              <div class="truncate text-sm font-medium text-base-content/70 group-hover:text-primary">{entity.label}</div>
              <div class="mt-1 text-2xl font-extrabold tabular-nums">
                {#if memuat}
                  <span class="skeleton inline-block h-7 w-14 align-middle"></span>
                {:else if total === null}
                  <span class="tooltip tooltip-right text-base text-base-content/30" data-tip="Tidak dapat dimuat (akses dibatasi)">—</span>
                {:else}
                  {angka(total)}
                {/if}
              </div>
            </a>
            <div class="flex shrink-0 gap-1">
              <a
                href={`#/${entity.resourceType}/tambah`}
                class="btn btn-ghost btn-sm btn-square opacity-60 group-hover:opacity-100"
                aria-label="Tambah {entity.label}"
                title="Tambah data"
              >
                <Icon nama="plus" class="size-4" />
              </a>
              <a
                href={`#/${entity.resourceType}`}
                class="btn btn-ghost btn-sm btn-square opacity-60 group-hover:opacity-100"
                aria-label="Lihat {entity.label}"
                title="Lihat data"
              >
                <Icon nama="arrow-right" class="size-4" />
              </a>
            </div>
          </div>
        {/each}
      </div>
    </section>
  {/each}
</div>
