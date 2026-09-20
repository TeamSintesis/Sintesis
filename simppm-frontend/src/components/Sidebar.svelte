<script>
  import active from 'svelte-spa-router/active';
  import { entitiesByPhase, PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';

  const grouped = entitiesByPhase();

  let isOpen = true;

  function toggleSidebar() {
    isOpen = !isOpen;
  }

  // regexparam menganggap "/x/*" hanya cocok bila ada garis miring setelah
  // "x" (tidak cocok untuk path persis "/x"), jadi pola aktif dibuat manual
  // agar menu tetap tersorot baik di "/dosen" maupun "/dosen/1/ubah".
  function polaAktif(resourceType) {
    return new RegExp(`^/${resourceType}(/.*)?$`);
  }
</script>

<nav class="sidebar" class:closed={!isOpen}>
  <div class="brand-row">
    <a class="brand" href="#/" use:active={{ path: '/', className: 'is-active' }}>
      <span class="brand-mark">Σ</span>
      {#if isOpen}<span>SIMPPM</span>{/if}
    </a>
    <button class="toggle-btn" on:click={toggleSidebar} aria-label="Toggle sidebar">
      {#if isOpen}
        <!-- ikon panah kiri (tutup) -->
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6" />
        </svg>
      {:else}
        <!-- ikon panah kanan (buka) -->
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6" />
        </svg>
      {/if}
    </button>
  </div>

  <div class="menu-scroll">
    {#each PHASE_ORDER as phase}
      <div class="phase-group">
        {#if isOpen}
          <div class="phase-title">{PHASE_LABELS[phase]}</div>
        {/if}
        {#each grouped[phase] as entity}
          <a
            class="menu-item"
            href={`#/${entity.resourceType}`}
            title={entity.label}
            use:active={{ path: polaAktif(entity.resourceType), className: 'is-active' }}
          >
            <span class="menu-label">{entity.label}</span>
          </a>
        {/each}
      </div>
    {/each}
  </div>
</nav>

<style>
  .sidebar {
    width: 240px;
    flex-shrink: 0;
    background: #ffffff;
    color: #475569;
    height: 100vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    border-right: 1px solid #e5e7eb;
    transition: width 0.2s ease;
  }

  .sidebar.closed {
    width: 68px;
  }

  .brand-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 14px;
    border-bottom: 1px solid #f1f5f9;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: 0.01em;
    color: #0f172a;
    overflow: hidden;
    white-space: nowrap;
  }
  .brand:hover { text-decoration: none; }

  .brand-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    border-radius: 8px;
    background: #0f172a;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
  }

  .toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease;
  }
  .toggle-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }

  .menu-scroll {
    padding: 8px 12px 24px;
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .phase-group {
    margin-bottom: 4px;
  }

  .phase-title {
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    padding: 16px 10px 6px;
    font-weight: 600;
    white-space: nowrap;
  }

  .menu-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    margin: 1px 0;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    color: #475569;
    transition: background-color 0.15s ease, color 0.15s ease;
    overflow: hidden;
    white-space: nowrap;
  }

  .menu-label {
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .menu-item:hover {
    background: #f1f5f9;
    text-decoration: none;
    color: #0f172a;
  }

  :global(.menu-item.is-active) {
    background: #eef2ff;
    color: #3b3f8f;
    font-weight: 600;
  }

  /* scrollbar tipis biar makin rapi */
  .menu-scroll::-webkit-scrollbar {
    width: 5px;
  }
  .menu-scroll::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 3px;
  }
</style>