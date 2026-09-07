<script>
  import { push } from 'svelte-spa-router';
  import { session, logout, PERAN_LABELS } from '../lib/auth.js';

  let sedangKeluar = $state(false);

  async function keluar() {
    sedangKeluar = true;
    await logout();
    push('/login');
  }
</script>

<header class="topbar">
  <div></div>
  {#if $session}
    <div class="user-box">
      <div class="user-info">
        <div class="user-name">{$session.user.name}</div>
        <div class="user-role badge">{PERAN_LABELS[$session.user.peran] ?? $session.user.peran}</div>
      </div>
      <button class="btn btn-small" onclick={keluar} disabled={sedangKeluar}>
        {sedangKeluar ? 'Keluar…' : 'Keluar'}
      </button>
    </div>
  {/if}
</header>

<style>
  .topbar {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    background: var(--color-surface);
    border-bottom: 1px solid var(--color-border);
  }
  .user-box { display: flex; align-items: center; gap: 14px; }
  .user-info { text-align: right; }
  .user-name { font-size: 14px; font-weight: 600; }
  .user-role { margin-top: 2px; }
</style>
