<script>
  import active from 'svelte-spa-router/active';
  import { entitiesByPhase, PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';

  const grouped = entitiesByPhase();

  // regexparam menganggap "/x/*" hanya cocok bila ada garis miring setelah
  // "x" (tidak cocok untuk path persis "/x"), jadi pola aktif dibuat manual
  // agar menu tetap tersorot baik di "/dosen" maupun "/dosen/1/ubah".
  function polaAktif(resourceType) {
    return new RegExp(`^/${resourceType}(/.*)?$`);
  }
</script>

<nav class="sidebar">
  <a class="brand" href="#/" use:active={{ path: '/', className: 'is-active' }}>
    <span class="brand-mark">Σ</span>
    <span>SIMPPM</span>
  </a>

  <div class="menu-scroll">
    {#each PHASE_ORDER as phase}
      <div class="phase-group">
        <div class="phase-title">{PHASE_LABELS[phase]}</div>
        {#each grouped[phase] as entity}
          <a
            class="menu-item"
            href={`#/${entity.resourceType}`}
            use:active={{ path: polaAktif(entity.resourceType), className: 'is-active' }}
          >
            {entity.label}
          </a>
        {/each}
      </div>
    {/each}
  </div>
</nav>

<style>
  .sidebar {
    width: 248px;
    flex-shrink: 0;
    background: #16233a;
    color: #cbd5e1;
    height: 100vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
  }
  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 20px;
    font-weight: 700;
    font-size: 16px;
    color: #fff;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }
  .brand:hover { text-decoration: none; }
  .brand-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #1f5aa6;
    font-size: 14px;
  }
  .menu-scroll { padding: 10px 0 20px; flex: 1; overflow-y: auto; }
  .phase-group { margin-bottom: 6px; }
  .phase-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #7d8ba1;
    padding: 14px 20px 6px;
    font-weight: 700;
  }
  .menu-item {
    display: block;
    padding: 7px 20px;
    font-size: 14px;
    color: #cbd5e1;
    border-left: 3px solid transparent;
  }
  .menu-item:hover { background: rgba(255, 255, 255, 0.05); text-decoration: none; color: #fff; }
  :global(.menu-item.is-active) {
    background: rgba(31, 90, 166, 0.25);
    border-left-color: #4f8fd8;
    color: #fff;
    font-weight: 600;
  }
</style>
