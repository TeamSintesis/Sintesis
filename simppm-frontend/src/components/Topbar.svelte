<script>
  import { push } from 'svelte-spa-router';
  import { findEntity } from '../lib/entities.meta.js';
  import { session, logout, PERAN_LABELS } from '../lib/auth.js';

  let resource = $state('');
  let entityMeta = $state(null);

  function updateEntityMeta() {
    resource = window.location.hash
      .replace(/^#\/?/, '')
      .split('/')[0];

    entityMeta = findEntity(resource);

    console.log('resource:', resource);
    console.log('entityMeta:', entityMeta.label);
  }

  updateEntityMeta();

  $effect(() => {
    const handleHashChange = () => {
      updateEntityMeta();
    };

    window.addEventListener('hashchange', handleHashChange);

    return () => {
      window.removeEventListener('hashchange', handleHashChange);
    };
  });

  let sedangKeluar = $state(false);
  let dropdownTerbuka = $state(false);

  function dapatkanInisial(nama) {
    if (!nama) return 'U';
    const kata = nama.trim().split(/\s+/);
    if (kata.length === 1) return kata[0][0].toUpperCase();
    return (kata[0][0] + kata[kata.length - 1][0]).toUpperCase();
  }

  function dapatkanSalam() {
    const jam = new Date().getHours();
    if (jam >= 3 && jam < 11) return 'Selamat pagi';
    if (jam >= 11 && jam < 15) return 'Selamat siang';
    if (jam >= 15 && jam < 18) return 'Selamat sore';
    return 'Selamat malam';
  }

  function toggleDropdown(e) {
    e.stopPropagation();
    dropdownTerbuka = !dropdownTerbuka;
  }

  function tutupDropdown(e) {
    if (dropdownTerbuka && !e.target.closest('.user-menu-container')) {
      dropdownTerbuka = false;
    }
  }
  
  async function keluar() {
    sedangKeluar = true;
    await logout();
    push('/login');
  }
</script>

<svelte:window onclick={tutupDropdown} />

<header class="topbar">
  <div class="topbar-left">
    {#if entityMeta !== null}
      <h1>{entityMeta.label}</h1>
    {/if}
  </div>

  {#if $session}
    <div class="topbar-right user-menu-container">
      <div class="greeting-box">
        <span class="greeting-text">{dapatkanSalam()},</span>
        <span class="user-display-name">{$session.user.name}</span>
      </div>

      <div class="avatar-wrapper">
        <button class="avatar-btn" onclick={toggleDropdown} aria-label="Menu pengguna">
          <div class="avatar">
            {dapatkanInisial($session.user.name)}
          </div>
        </button>

        {#if dropdownTerbuka}
          <div class="dropdown-menu">
            <div class="dropdown-header">
              <div class="dropdown-name">{$session.user.name}</div>
              <div class="dropdown-role">
                {PERAN_LABELS[$session.user.peran] ?? $session.user.peran}
              </div>
            </div>

            <div class="dropdown-divider"></div>

            <button class="logout-btn" onclick={keluar} disabled={sedangKeluar}>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              {sedangKeluar ? 'Keluar…' : 'Keluar'}
            </button>
          </div>
        {/if}
      </div>
    </div>
  {/if}
</header>

<style>
  .topbar {
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
    font-family: 'Inter', system-ui, sans-serif;
  }

  .topbar-left {
    display: flex;
    align-items: center;
    min-width: 0;
  }

  .topbar-left h1 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #334155;
    letter-spacing: 0.01em;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
  }

  .greeting-box {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #475569;
  }

  .greeting-text {
    font-weight: 400;
    color: #64748b;
  }

  .user-display-name {
    font-weight: 600;
    color: #0f172a;
  }

  .avatar-wrapper {
    position: relative;
  }

  .avatar-btn {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    outline: none;
    border-radius: 50%;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .avatar-btn:hover {
    transform: scale(1.04);
  }

  .avatar-btn:focus-visible {
    box-shadow: 0 0 0 3px #c7d2fe;
  }

  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #4338ca;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.05em;
    user-select: none;
  }

  .dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 220px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
    padding: 8px;
    z-index: 50;
    animation: fadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-6px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .dropdown-header {
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .dropdown-name {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
  }

  .dropdown-role {
    display: inline-block;
    align-self: flex-start;
    background: #eef2ff;
    color: #4338ca;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
  }

  .dropdown-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 6px 0;
  }

  .logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: transparent;
    border: none;
    border-radius: 8px;
    color: #ef4444;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.15s ease;
    text-align: left;
  }

  .logout-btn:hover:not(:disabled) {
    background: #fef2f2;
  }

  .logout-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .logout-btn svg {
    width: 16px;
    height: 16px;
  }

  @media (max-width: 640px) {
    .greeting-text {
      display: none;
    }
  }
</style>