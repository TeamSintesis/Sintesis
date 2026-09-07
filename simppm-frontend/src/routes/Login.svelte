<script>
  // Halaman login -- satu-satunya rute publik. Memanggil auth.js#login()
  // yang menghubungi endpoint auth backend (bukan JSON:API resource biasa).
  import { push } from 'svelte-spa-router';
  import { login } from '../lib/auth.js';

  let email = $state('');
  let password = $state('');
  let sedangMasuk = $state(false);
  let pesanError = $state('');

  async function tanganiSubmit(event) {
    event.preventDefault();
    pesanError = '';
    sedangMasuk = true;
    try {
      await login(email, password);
      push('/');
    } catch (err) {
      pesanError = err.message || 'Gagal masuk. Periksa kembali email dan kata sandi.';
    } finally {
      sedangMasuk = false;
    }
  }
</script>

<div class="login-page">
  <form class="card login-card" onsubmit={tanganiSubmit}>
    <div class="login-brand">
      <span class="brand-mark">Σ</span>
      <div>
        <h1 style="font-size:18px;">SIMPPM</h1>
        <p class="muted" style="font-size:13px;margin:0;">Sistem Informasi Pengelolaan Penelitian &amp; PkM</p>
      </div>
    </div>

    {#if pesanError}
      <div class="alert alert-danger" role="alert">{pesanError}</div>
    {/if}

    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" bind:value={email} required autocomplete="username" placeholder="nama@simppm.test" />
    </div>

    <div class="field">
      <label for="password">Kata sandi</label>
      <input id="password" type="password" bind:value={password} required autocomplete="current-password" placeholder="••••••••" />
    </div>

    <button class="btn btn-primary" type="submit" disabled={sedangMasuk} style="width:100%;justify-content:center;">
      {sedangMasuk ? 'Memproses…' : 'Masuk'}
    </button>

    <p class="hint" style="margin-top:18px;font-size:12px;">
      Akun demo: admin.lppm@simppm.test, dosen@simppm.test, mahasiswa@simppm.test, dll. — kata sandi <code>password</code> untuk semuanya.
    </p>
  </form>
</div>

<style>
  .login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #16233a 0%, #1f5aa6 100%);
    padding: 24px;
  }
  .login-card {
    width: 100%;
    max-width: 380px;
    padding: 32px;
  }
  .login-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
  }
  .brand-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #1f5aa6;
    color: #fff;
    font-size: 18px;
    flex-shrink: 0;
  }
  .hint { color: var(--color-text-muted); line-height: 1.5; }
</style>
