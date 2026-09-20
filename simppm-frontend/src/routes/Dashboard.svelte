<script>
  import { onMount } from 'svelte';
  import { entitiesByPhase, PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';
  import { listResource, ApiError } from '../lib/api.js';
  import { session, PERAN_LABELS } from '../lib/auth.js';

  const grouped = entitiesByPhase();

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
          totals[entity.resourceType] = err instanceof ApiError ? null : null;
        }
      })
    );
    memuat = false;
  });
</script>

<div class="dashboard-wrapper">
  <!-- <div class="header-section">
    <h1>Selamat datang, <span>{$session?.user?.name ?? 'Pengguna'}</span></h1>
    <p>
      Anda masuk sebagai <span class="role-tag">{PERAN_LABELS[$session?.user?.peran] ?? $session?.user?.peran}</span>.
      Silakan kelola data siklus PPM Anda.
    </p>
  </div> -->

  <div class="content-section">
    {#each PHASE_ORDER as phase}
      <div class="phase-block">
        <h2 class="phase-title">{PHASE_LABELS[phase]}</h2>
        
        <div class="card-grid">
          {#each grouped[phase] as entity}
            <a class="data-card" href={`#/${entity.resourceType}`}>
              <div class="card-info">
                <span class="card-label">{entity.label}</span>
                <div class="card-value">
                  {#if memuat}
                    <span class="loader"></span>
                  {:else if totals[entity.resourceType] === null}
                    <span class="dash">—</span>
                  {:else}
                    {totals[entity.resourceType]}
                  {/if}
                </div>
              </div>
              <div class="card-arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </div>
            </a>
          {/each}
        </div>
      </div>
    {/each}
  </div>
</div>

<style>
  .dashboard-wrapper {
    display: flex;
    flex-direction: column;
    gap: 32px;
    padding: 8px;
    max-width: 1280px;
    margin: 0 auto;
    font-family: 'Inter', system-ui, sans-serif;
    color: #0f172a;
  }

  .header-section {
    background: linear-gradient(to right, #ffffff, #f8fafc);
    padding: 28px 32px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
  }

  .header-section h1 {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 8px 0;
    letter-spacing: -0.01em;
  }

  .header-section h1 span {
    color: #4338ca;
  }

  .header-section p {
    margin: 0;
    font-size: 15px;
    color: #64748b;
  }

  .role-tag {
    background: #eef2ff;
    color: #4338ca;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    margin: 0 4px;
  }

  .content-section {
    display: flex;
    flex-direction: column;
    gap: 36px;
  }

  .phase-title {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    margin: 0 0 16px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid #f1f5f9;
  }

  .card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
  }

  .data-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
  }

  .data-card:hover {
    border-color: #a5b4fc;
    box-shadow: 0 10px 15px -3px rgba(67, 56, 202, 0.08);
    transform: translateY(-2px);
  }

  .card-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .card-label {
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
  }

  .data-card:hover .card-label {
    color: #4338ca;
  }

  .card-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
  }

  .card-arrow {
    width: 24px;
    height: 24px;
    color: #cbd5e1;
    transition: transform 0.2s ease, color 0.2s ease;
  }

  .data-card:hover .card-arrow {
    color: #4338ca;
    transform: translateX(4px);
  }

  .dash {
    color: #cbd5e1;
    font-size: 24px;
  }

  .loader {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f1f5f9;
    border-top-color: #4338ca;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  @media (max-width: 640px) {
    .card-grid {
      grid-template-columns: 1fr;
    }
  }
</style>