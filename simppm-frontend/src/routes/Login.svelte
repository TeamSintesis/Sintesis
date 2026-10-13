<script>
  // Halaman login -- satu-satunya rute publik. Memanggil auth.js#login()
  // yang menghubungi endpoint auth backend (bukan JSON:API resource biasa).
  import { push } from 'svelte-spa-router';
  import { login, PERAN_LABELS } from '../lib/auth.js';
  import { PHASE_LABELS, PHASE_ORDER } from '../lib/entities.meta.js';
  import { FASE_UI } from '../lib/ui.js';
  import { tema, gantiTema, TEMA_GELAP } from '../lib/theme.js';
  import Icon from '../components/Icon.svelte';

  let email = $state('');
  let password = $state('');
  let tampilkanSandi = $state(false);
  let sedangMasuk = $state(false);
  let pesanError = $state('');

  // Akun demo hasil seeder backend (lihat README) -- hanya untuk lingkungan
  // pengembangan/demo; klik untuk mengisi form secara otomatis.
  const AKUN_DEMO = [
    ['admin_lppm', 'admin.lppm@simppm.test'],
    ['ketua_lppm', 'ketua.lppm@simppm.test'],
    ['dosen', 'dosen@simppm.test'],
    ['mahasiswa', 'mahasiswa@simppm.test'],
    ['reviewer', 'reviewer@simppm.test'],
    ['prodi_gkm', 'prodi.gkm@simppm.test'],
    ['pimpinan', 'pimpinan@simppm.test'],
    ['mitra_eksternal', 'mitra@simppm.test'],
  ];

  function pakaiAkunDemo(alamat) {
    email = alamat;
    password = 'password';
    pesanError = '';
  }

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

<div class="grid min-h-dvh lg:grid-cols-[1.1fr_1fr]">
  <!-- Panel identitas (layar besar) -->
  <section class="relative hidden overflow-hidden bg-neutral p-12 text-neutral-content lg:flex lg:flex-col">
    <div class="pointer-events-none absolute -top-32 -right-32 size-[28rem] rounded-full bg-primary/40 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -left-24 size-[26rem] rounded-full bg-secondary/30 blur-3xl"></div>

    <div class="relative flex items-center gap-3">
      <span class="grid size-11 place-items-center rounded-xl bg-primary text-primary-content">
        <Icon nama="graduation-cap" class="size-6" />
      </span>
      <span class="text-xl font-extrabold tracking-tight">SIMPPM</span>
    </div>

    <div class="relative my-auto max-w-lg py-12">
      <h1 class="text-4xl leading-tight font-extrabold tracking-tight xl:text-5xl">
        Kelola siklus penelitian &amp; pengabdian dalam satu sistem.
      </h1>
      <p class="mt-5 text-lg text-neutral-content/70">
        Sistem Informasi Pengelolaan Penelitian dan Pengabdian kepada Masyarakat — dari perencanaan hingga
        pembuktian dampak, selaras dengan kerangka akreditasi BAN-PT.
      </p>

      <ol class="mt-10 grid grid-cols-2 gap-3">
        {#each PHASE_ORDER as phase, i}
          <li class="rounded-box border border-neutral-content/10 bg-neutral-content/5 p-4 backdrop-blur">
            <div class="flex items-center gap-2">
              <span class="grid size-8 place-items-center rounded-lg {FASE_UI[phase].latar} {FASE_UI[phase].teks}">
                <Icon nama={FASE_UI[phase].ikon} class="size-4" />
              </span>
              <span class="text-xs font-semibold text-neutral-content/50">Fase {i + 1}</span>
            </div>
            <div class="mt-3 font-semibold">{PHASE_LABELS[phase]}</div>
          </li>
        {/each}
      </ol>
    </div>

    <p class="relative text-sm text-neutral-content/50">Lembaga Penelitian dan Pengabdian kepada Masyarakat</p>
  </section>

  <!-- Form masuk -->
  <section class="relative flex items-center justify-center bg-base-100 px-5 py-10 sm:px-10">
    <button
      class="btn btn-ghost btn-square btn-sm absolute top-4 right-4"
      onclick={gantiTema}
      aria-label={$tema === TEMA_GELAP ? 'Gunakan tema terang' : 'Gunakan tema gelap'}
    >
      <Icon nama={$tema === TEMA_GELAP ? 'sun' : 'moon'} />
    </button>

    <div class="w-full max-w-sm">
      <div class="mb-8 flex items-center gap-3 lg:hidden">
        <span class="grid size-11 place-items-center rounded-xl bg-primary text-primary-content">
          <Icon nama="graduation-cap" class="size-6" />
        </span>
        <div class="leading-tight">
          <div class="text-xl font-extrabold tracking-tight">SIMPPM</div>
          <div class="text-xs text-base-content/60">Pengelolaan Penelitian &amp; PkM</div>
        </div>
      </div>

      <h2 class="text-2xl font-bold tracking-tight">Masuk ke akun Anda</h2>
      <p class="mt-1 text-sm text-base-content/60">Gunakan email dan kata sandi yang terdaftar.</p>

      {#if pesanError}
        <div class="alert alert-error alert-soft mt-6" role="alert">
          <Icon nama="alert-circle" />
          <span class="text-sm">{pesanError}</span>
        </div>
      {/if}

      <form class="mt-6 space-y-4" onsubmit={tanganiSubmit}>
        <div>
          <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
          <label class="input w-full">
            <Icon nama="mail" class="size-4 opacity-50" />
            <input
              id="email"
              type="email"
              bind:value={email}
              required
              autocomplete="username"
              placeholder="nama@simppm.test"
            />
          </label>
        </div>

        <div>
          <label for="password" class="mb-1.5 block text-sm font-medium">Kata sandi</label>
          <label class="input w-full">
            <Icon nama="lock" class="size-4 opacity-50" />
            <input
              id="password"
              type={tampilkanSandi ? 'text' : 'password'}
              bind:value={password}
              required
              autocomplete="current-password"
              placeholder="••••••••"
            />
            <button
              type="button"
              class="btn btn-ghost btn-xs btn-square"
              onclick={() => (tampilkanSandi = !tampilkanSandi)}
              aria-label={tampilkanSandi ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
            >
              <Icon nama={tampilkanSandi ? 'eye-off' : 'eye'} class="size-4" />
            </button>
          </label>
        </div>

        <button class="btn btn-primary btn-block mt-2" type="submit" disabled={sedangMasuk}>
          {#if sedangMasuk}
            <span class="loading loading-spinner loading-sm"></span> Memproses…
          {:else}
            Masuk <Icon nama="arrow-right" class="size-4" />
          {/if}
        </button>
      </form>

      <div class="divider my-8 text-xs text-base-content/50">Akun demo</div>

      <div class="flex flex-wrap gap-2">
        {#each AKUN_DEMO as [peran, alamat]}
          <button
            type="button"
            class="btn btn-xs {email === alamat ? 'btn-primary' : 'btn-soft'}"
            onclick={() => pakaiAkunDemo(alamat)}
            title={alamat}
          >
            {PERAN_LABELS[peran]}
          </button>
        {/each}
      </div>
      <p class="mt-3 text-xs text-base-content/50">
        Klik peran untuk mengisi form. Semua akun demo memakai kata sandi <code class="font-semibold">password</code>.
      </p>
    </div>
  </section>
</div>
