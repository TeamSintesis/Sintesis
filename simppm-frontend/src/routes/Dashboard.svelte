<script>
  // Halaman ringkasan setelah login: menampilkan jumlah data per entitas,
  // dikelompokkan per fase siklus PPM, dengan tautan cepat ke setiap daftar.
  import { onMount } from 'svelte';
  import { entitiesByPhase, PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';
  import { listResource, ApiError } from '../lib/api.js';
  import { session, PERAN_LABELS } from '../lib/auth.js';

  const grouped = entitiesByPhase();

  /** @type {Record<string, number|null>} */
  let totals = $state({});
  let memuat = $state(true);

  onMount(async () => {
    const semuaEntitas = PHASE_ORDER.flatMap((p) => grouped[p]);
    await Promise.all(
      semuaEntitas.map(async (entity) => {
        try {
          const payload = await listResource(entity.resourceType, { page: 1, pageSize: 1 });
          totals[entity.resourceType] = payload?.meta?.page?.total ?? 0;
        } catch (err) {
          // 403 dari kebijakan RBAC dianggap wajar (mis. mahasiswa tidak
          // boleh melihat daftar kontrak) -- tampilkan sebagai '-' saja.
          totals[entity.resourceType] = err instanceof ApiError ? null : null;
        }
      })
    );
    memuat = false;
  });
</script>

<div class="dashboard">
  <div class="welcome card">
    <h1 style="font-size:22px;">Selamat datang, {$session?.user?.name ?? ''}</h1>
    <p class="muted" style="margin:4px 0 0;">
      Anda masuk sebagai <strong>{PERAN_LABELS[$session?.user?.peran] ?? $session?.user?.peran}</strong>.
      Pilih menu di sebelah kiri untuk mengelola data pada tiap fase siklus PPM
      (Masukan → Proses → Luaran/Capaian → Dampak).
    </p>
  </div>

  {#each PHASE_ORDER as phase}
    <section class="phase-section">
      <h2 style="font-size:15px;text-transform:uppercase;letter-spacing:0.04em;color:var(--color-text-muted);">
        {PHASE_LABELS[phase]}
      </h2>
      <div class="entity-grid">
        {#each grouped[phase] as entity}
          <a class="entity-card card" href={`#/${entity.resourceType}`}>
            <div class="entity-name">{entity.label}</div>
            <div class="entity-total">
              {#if memuat}
                <span class="spinner" style="width:14px;height:14px;"></span>
              {:else if totals[entity.resourceType] === null}
                <span class="muted">-</span>
              {:else}
                {totals[entity.resourceType]}
              {/if}
            </div>
          </a>
        {/each}
      </div>
    </section>
  {/each}
</div>

<style>
  .dashboard { display: flex; flex-direction: column; gap: 28px; }
  .welcome { padding: 20px 24px; }
  .phase-section h2 { margin-bottom: 12px; }
  .entity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
  }
  .entity-card {
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    color: var(--color-text);
  }
  .entity-card:hover { border-color: var(--color-primary); text-decoration: none; }
  .entity-name { font-size: 13px; font-weight: 600; }
  .entity-total { font-size: 22px; font-weight: 700; color: var(--color-primary); }
</style>
