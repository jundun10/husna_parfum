<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Eye,
    EyeOff,
    User,
    Lock,
    BellRing,
} from 'lucide-vue-next';

// PROPOSED props — confirm actual shape with your backend before wiring up.
const props = defineProps({
    authUser: {
        type: Object,
        default: null,
    },

    pengaturanNotifikasi: {
        type: Object,
        default: () => ({
            notifikasi_pesanan_aktif: true,
        }),
    },
});

const logoUrl = '/images/logo-hf.png';

const sidebarOpen = ref(false);
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};

// ---------- Success / error feedback (same pattern as Kelola Stok) ----------
const showSuccess = ref(false);
const successMessage = ref('');

const showError = ref(false);
const errorMessage = ref('');

const showSuccessNotification = (message) => {
    successMessage.value = message;
    showSuccess.value = true;

    setTimeout(() => {
        showSuccess.value = false;
    }, 2000);
};

const showErrorNotification = (message) => {
    errorMessage.value = message;
    showError.value = true;

    setTimeout(() => {
        showError.value = false;
    }, 2500);
};

// ---------- Profil Akun ----------
// PROPOSED route — replace with your real endpoint.
const profilForm = useForm({
    name: props.authUser?.name ?? '',
    email: props.authUser?.email ?? '',
});

const submitProfil = () => {
    profilForm.put('/admin/pengaturan/profil', {
        preserveScroll: true,

        onSuccess: () => {
            showSuccessNotification('Profil berhasil diperbarui.');
        },

        onError: (errors) => {
            const firstError = Object.values(errors)[0];

            showErrorNotification(
                firstError || 'Profil gagal diperbarui.'
            );
        },
    });
};

// ---------- Keamanan ----------
// PROPOSED route — replace with your real endpoint.
const passwordForm = useForm({
    password_lama: '',
    password_baru: '',
    password_baru_confirmation: '',
});

const showPasswordLama = ref(false);
const showPasswordBaru = ref(false);
const showPasswordConfirm = ref(false);

const submitPassword = () => {
    passwordForm.put('/admin/pengaturan/password', {
        preserveScroll: true,

        onSuccess: () => {
            passwordForm.reset();

            showSuccessNotification('Password berhasil diubah.');
        },

        onError: (errors) => {
            const firstError = Object.values(errors)[0];

            showErrorNotification(
                firstError || 'Password gagal diubah.'
            );
        },
    });
};

// ---------- Notifikasi Pesanan Baru ----------
// PROPOSED route + column — needs a boolean column on users table.
const notifikasiForm = useForm({
    notifikasi_pesanan_aktif:
        props.pengaturanNotifikasi?.notifikasi_pesanan_aktif ?? true,
});

const submitNotifikasi = () => {
    notifikasiForm.put('/admin/pengaturan/notifikasi', {
        preserveScroll: true,

        onSuccess: () => {
            showSuccessNotification('Preferensi notifikasi disimpan.');
        },

        onError: () => {
            showErrorNotification('Preferensi notifikasi gagal disimpan.');
        },
    });
};
</script>

<template>
    <Head title="Pengaturan Admin" />

    <div class="admin-page">

        <div
            v-if="sidebarOpen"
            class="overlay"
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="sidebar"
            :class="{ 'sidebar-open': sidebarOpen }"
        >

            <div class="sidebar-header">

                <div class="brand">
                    <img
                        :src="logoUrl"
                        alt="HF Parfum"
                    >

                    <div>
                        <h2>HF Parfum</h2>
                        <span>Admin Panel</span>
                    </div>
                </div>

                <button
                    type="button"
                    class="close-button"
                    @click="sidebarOpen = false"
                >
                    ×
                </button>

            </div>

            <div class="admin-info">

                <div class="admin-avatar">
                    {{ props.authUser?.name?.charAt(0).toUpperCase() }}
                </div>

                <div class="admin-details">

                    <strong>
                        {{ props.authUser?.name }}
                    </strong>

                    <span>
                        Admin
                    </span>

                    <small>
                        {{ props.authUser?.email }}
                    </small>

                </div>

            </div>

            <nav class="sidebar-menu">

                <Link
                    href="/admin/dashboard"
                    class="menu-item"
                >
                    <span>Dashboard</span>
                </Link>

                <Link
                    href="/admin/pesanan"
                    class="menu-item"
                >
                    <span>Pesanan</span>
                </Link>

                <Link
                    href="/admin/stok"
                    class="menu-item"
                >
                    <span>Kelola Stok</span>
                </Link>

                <Link
                    href="/admin/pengaturan"
                    class="menu-item active"
                >
                    <span>Pengaturan</span>
                </Link>

            </nav>

            <div class="sidebar-footer">
                <button
                    type="button"
                    class="logout-button"
                    @click="logout"
                    :disabled="logoutForm.processing"
                >
                    <span>
                        {{ logoutForm.processing ? 'Keluar...' : 'Logout' }}
                    </span>
                </button>
            </div>

        </aside>

        <main class="main-content">

            <header class="topbar">

                <div class="topbar-left">

                    <button
                        type="button"
                        class="toggle-button"
                        @click="sidebarOpen = true"
                        aria-label="Buka sidebar"
                    >
                        ☰
                    </button>

                    <div class="topbar-heading">
                        <span class="topbar-eyebrow">HF Parfum</span>
                        <h1>Pengaturan</h1>
                        <p>Kelola akun dan preferensi Anda sebagai Admin.</p>
                    </div>

                </div>

            </header>

            <div class="settings-layout">

                <!-- Profil Akun -->
                <section class="settings-card">

                    <div class="settings-header">

                        <div class="settings-icon">
                            <User :size="17" />
                        </div>

                        <div>
                            <h2>Profil Akun</h2>
                            <p>Nama dan email yang digunakan untuk masuk ke sistem.</p>
                        </div>

                    </div>

                    <form
                        class="settings-form"
                        @submit.prevent="submitProfil"
                    >

                        <div class="form-row">

                            <div class="form-group">
                                <label for="profil-nama">Nama</label>

                                <input
                                    id="profil-nama"
                                    v-model="profilForm.name"
                                    type="text"
                                    placeholder="Nama lengkap"
                                >

                                <span
                                    v-if="profilForm.errors.name"
                                    class="error"
                                >
                                    {{ profilForm.errors.name }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="profil-email">Email</label>

                                <input
                                    id="profil-email"
                                    v-model="profilForm.email"
                                    type="email"
                                    placeholder="Alamat email"
                                >

                                <span
                                    v-if="profilForm.errors.email"
                                    class="error"
                                >
                                    {{ profilForm.errors.email }}
                                </span>
                            </div>

                        </div>

                        <div class="settings-actions">
                            <button
                                type="submit"
                                class="save-button"
                                :disabled="profilForm.processing"
                            >
                                {{
                                    profilForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Perubahan'
                                }}
                            </button>
                        </div>

                    </form>

                </section>

                <!-- Keamanan -->
                <section class="settings-card">

                    <div class="settings-header">

                        <div class="settings-icon">
                            <Lock :size="17" />
                        </div>

                        <div>
                            <h2>Keamanan</h2>
                            <p>Perbarui password akun Anda secara berkala.</p>
                        </div>

                    </div>

                    <form
                        class="settings-form"
                        @submit.prevent="submitPassword"
                    >

                        <div class="form-group">
                            <label for="password-lama">Password Lama</label>

                            <div class="password-input">

                                <input
                                    id="password-lama"
                                    v-model="passwordForm.password_lama"
                                    :type="showPasswordLama ? 'text' : 'password'"
                                    placeholder="Masukkan password lama"
                                >

                                <button
                                    type="button"
                                    class="toggle-password"
                                    @click="showPasswordLama = !showPasswordLama"
                                    :aria-label="
                                        showPasswordLama
                                            ? 'Sembunyikan password'
                                            : 'Tampilkan password'
                                    "
                                >
                                    <EyeOff v-if="showPasswordLama" :size="16" />
                                    <Eye v-else :size="16" />
                                </button>

                            </div>

                            <span
                                v-if="passwordForm.errors.password_lama"
                                class="error"
                            >
                                {{ passwordForm.errors.password_lama }}
                            </span>
                        </div>

                        <div class="form-row">

                            <div class="form-group">
                                <label for="password-baru">Password Baru</label>

                                <div class="password-input">

                                    <input
                                        id="password-baru"
                                        v-model="passwordForm.password_baru"
                                        :type="showPasswordBaru ? 'text' : 'password'"
                                        placeholder="Masukkan password baru"
                                    >

                                    <button
                                        type="button"
                                        class="toggle-password"
                                        @click="showPasswordBaru = !showPasswordBaru"
                                        :aria-label="
                                            showPasswordBaru
                                                ? 'Sembunyikan password'
                                                : 'Tampilkan password'
                                        "
                                    >
                                        <EyeOff v-if="showPasswordBaru" :size="16" />
                                        <Eye v-else :size="16" />
                                    </button>

                                </div>

                                <span
                                    v-if="passwordForm.errors.password_baru"
                                    class="error"
                                >
                                    {{ passwordForm.errors.password_baru }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm">
                                    Konfirmasi Password Baru
                                </label>

                                <div class="password-input">

                                    <input
                                        id="password-confirm"
                                        v-model="passwordForm.password_baru_confirmation"
                                        :type="showPasswordConfirm ? 'text' : 'password'"
                                        placeholder="Ulangi password baru"
                                    >

                                    <button
                                        type="button"
                                        class="toggle-password"
                                        @click="
                                            showPasswordConfirm = !showPasswordConfirm
                                        "
                                        :aria-label="
                                            showPasswordConfirm
                                                ? 'Sembunyikan password'
                                                : 'Tampilkan password'
                                        "
                                    >
                                        <EyeOff v-if="showPasswordConfirm" :size="16" />
                                        <Eye v-else :size="16" />
                                    </button>

                                </div>

                                <span
                                    v-if="passwordForm.errors.password_baru_confirmation"
                                    class="error"
                                >
                                    {{ passwordForm.errors.password_baru_confirmation }}
                                </span>
                            </div>

                        </div>

                        <div class="settings-actions">
                            <button
                                type="submit"
                                class="save-button"
                                :disabled="passwordForm.processing"
                            >
                                {{
                                    passwordForm.processing
                                        ? 'Memproses...'
                                        : 'Ubah Password'
                                }}
                            </button>
                        </div>

                    </form>

                </section>

                <!-- Notifikasi Pesanan Baru -->
                <section class="settings-card">

                    <div class="settings-header">

                        <div class="settings-icon">
                            <BellRing :size="17" />
                        </div>

                        <div>
                            <h2>Notifikasi Pesanan Baru</h2>
                            <p>
                                Atur apakah Anda ingin menerima notifikasi
                                setiap ada pesanan pelanggan yang masuk.
                            </p>
                        </div>

                    </div>

                    <form
                        class="settings-form"
                        @submit.prevent="submitNotifikasi"
                    >

                        <label class="toggle-row">

                            <div class="toggle-text">
                                <strong>Notifikasi pesanan baru</strong>
                                <span>
                                    Saat aktif, notifikasi akan muncul di
                                    Dashboard dan halaman Pesanan setiap ada
                                    pesanan baru.
                                </span>
                            </div>

                            <span
                                class="toggle-switch"
                                :class="{
                                    'toggle-switch--on':
                                        notifikasiForm.notifikasi_pesanan_aktif
                                }"
                            >
                                <input
                                    type="checkbox"
                                    v-model="notifikasiForm.notifikasi_pesanan_aktif"
                                >

                                <span class="toggle-knob"></span>
                            </span>

                        </label>

                        <span
                            v-if="notifikasiForm.errors.notifikasi_pesanan_aktif"
                            class="error"
                        >
                            {{ notifikasiForm.errors.notifikasi_pesanan_aktif }}
                        </span>

                        <div class="settings-actions">
                            <button
                                type="submit"
                                class="save-button"
                                :disabled="notifikasiForm.processing"
                            >
                                {{
                                    notifikasiForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Preferensi'
                                }}
                            </button>
                        </div>

                    </form>

                </section>

            </div>

        </main>

        <Transition name="success">

            <div
                v-if="showSuccess"
                class="feedback-overlay"
            >

                <div class="feedback-modal">

                    <div class="feedback-icon">

                        <svg
                            viewBox="0 0 52 52"
                            class="checkmark"
                        >

                            <circle
                                cx="26"
                                cy="26"
                                r="24"
                                fill="none"
                                class="check-circle"
                            />

                            <path
                                d="M14 27 L22 35 L39 18"
                                fill="none"
                                class="check-line"
                            />

                        </svg>

                    </div>

                    <h3>
                        Berhasil!
                    </h3>

                    <p>
                        {{ successMessage }}
                    </p>

                </div>

            </div>

        </Transition>

        <Transition name="error">

            <div
                v-if="showError"
                class="feedback-overlay"
            >

                <div class="feedback-modal feedback-modal--error">

                    <div class="feedback-icon">

                        <svg
                            viewBox="0 0 52 52"
                            class="checkmark"
                        >

                            <circle
                                cx="26"
                                cy="26"
                                r="24"
                                fill="none"
                                class="error-circle"
                            />

                            <path
                                d="M17 17 L35 35 M35 17 L17 35"
                                fill="none"
                                class="error-line"
                            />

                        </svg>

                    </div>

                    <h3>
                        Gagal!
                    </h3>

                    <p>
                        {{ errorMessage }}
                    </p>

                </div>

            </div>

        </Transition>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.admin-page {
    --hf-green: #3f6e69;
    --hf-green-dark: #2f5652;
    --hf-sage: #cddbd6;
    --hf-sage-soft: #eef4f2;
    --hf-bg: #f5f8f7;
    --hf-ink: #2c3f3d;
    --hf-ink-soft: #5f7472;
    --hf-ink-faint: #97a6a4;
    --hf-border: #e3ece9;
    --hf-red: #b5504b;
    --hf-red-bg: #fbeeee;

    min-height: 100vh;

    background: var(--hf-bg);
    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* ---------- Sidebar ---------- */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;

    width: 270px;
    height: 100vh;

    padding: 24px 16px;

    display: flex;
    flex-direction: column;

    background: #ffffff;

    border-right: 1px solid var(--hf-border);

    transform: translateX(-100%);
    transition: transform .25s ease;

    z-index: 1000;

    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar.sidebar-open {
    transform: translateX(0);

    box-shadow: 10px 0 34px rgba(47, 86, 82, .12);
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 30px;
}

.brand {
    display: flex;
    align-items: center;

    gap: 11px;
}

.brand img {
    width: 40px;
    height: 40px;

    object-fit: contain;

    border-radius: 8px;

    mix-blend-mode: multiply;
}

.brand h2 {
    margin: 0 0 2px;

    font-family: Georgia, serif;

    font-size: 16px;
    font-weight: normal;

    color: var(--hf-ink);
}

.brand span {
    font-size: 9px;

    color: var(--hf-ink-faint);
    letter-spacing: .06em;
    text-transform: uppercase;
}

.close-button {
    width: 34px;
    height: 34px;

    border: none;
    border-radius: 8px;

    background: transparent;

    color: var(--hf-ink-faint);

    font-size: 22px;
    line-height: 1;

    cursor: pointer;

    transition: background .15s ease;
}

.close-button:hover {
    background: var(--hf-sage-soft);
}

.admin-info {
    display: flex;
    align-items: center;

    width: 100%;

    margin: 4px 0 24px;
    padding: 13px;

    border-radius: 10px;
    background: var(--hf-bg);
}

.admin-avatar {
    width: 42px;
    height: 42px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-right: 12px;

    border-radius: 50%;

    background: var(--hf-sage-soft);

    color: var(--hf-green);

    font-family: Georgia, serif;

    font-size: 18px;
}

.admin-details {
    min-width: 0;
}

.admin-details strong {
    display: block;

    margin-bottom: 3px;

    color: var(--hf-ink);

    font-size: 12.5px;
    font-weight: 600;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.admin-details span {
    display: block;

    margin-bottom: 3px;

    color: var(--hf-green);

    font-size: 10px;
    font-weight: 600;
}

.admin-details small {
    display: block;

    overflow: hidden;

    color: var(--hf-ink-faint);

    font-size: 9px;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;

    gap: 3px;
}

.menu-item {
    display: flex;
    align-items: center;

    gap: 12px;

    padding: 12px 13px;

    border-radius: 8px;

    color: var(--hf-ink-soft);

    text-decoration: none;

    font-size: 12px;
    font-weight: 500;

    transition: background .15s ease, color .15s ease;
}

.menu-item:hover {
    background: var(--hf-sage-soft);

    color: var(--hf-green);
}

.menu-item.active {
    background: var(--hf-sage-soft);

    color: var(--hf-green-dark);

    font-weight: 700;
}

.sidebar-footer {
    margin-top: 22px;
    padding-top: 14px;
    border-top: 1px solid var(--hf-border);
}

.logout-button {
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 10px;

    padding: 12px 14px;

    border: 1px solid var(--hf-red-bg);
    border-radius: 9px;

    background: var(--hf-red-bg);

    color: var(--hf-red);

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.logout-button:hover {
    background: #f6e2e1;
}

.logout-button:disabled {
    opacity: .6;
    cursor: not-allowed;
}

.overlay {
    position: fixed;
    inset: 0;

    background: rgba(30, 45, 43, .32);

    z-index: 999;
}

/* ---------- Main ---------- */

.main-content {
    min-height: 100vh;

    padding: 30px 4%;
}

.topbar {
    display: flex;
    align-items: center;

    margin-bottom: 26px;
}

.topbar-left {
    display: flex;
    align-items: center;

    gap: 16px;
}

.toggle-button {
    width: 42px;
    height: 42px;
    flex-shrink: 0;

    border: 1px solid var(--hf-border);
    border-radius: 10px;

    background: #ffffff;

    color: var(--hf-green);

    font-size: 17px;

    cursor: pointer;

    transition: background .15s ease;
}

.toggle-button:hover {
    background: var(--hf-sage-soft);
}

.topbar-heading {
    display: flex;
    flex-direction: column;
}

.topbar-eyebrow {
    font-size: 10px;
    letter-spacing: .1em;
    text-transform: uppercase;

    color: var(--hf-ink-faint);
    margin-bottom: 2px;
}

.topbar h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 25px;
    font-weight: normal;

    color: var(--hf-ink);
}

.topbar p {
    margin: 3px 0 0;

    font-size: 11px;

    color: var(--hf-ink-faint);
}

/* ---------- Settings layout ---------- */

.settings-layout {
    max-width: 640px;

    display: flex;
    flex-direction: column;
    gap: 16px;
}

.settings-card {
    padding: 26px;

    background: #ffffff;

    border: 1px solid var(--hf-border);
    border-radius: 14px;
}

.settings-header {
    display: flex;
    align-items: flex-start;

    gap: 13px;

    margin-bottom: 22px;
}

.settings-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: var(--hf-sage-soft);
    color: var(--hf-green);
}

.settings-header h2 {
    margin: 0 0 3px;

    font-family: Georgia, serif;

    font-size: 16.5px;
    font-weight: normal;

    color: var(--hf-ink);
}

.settings-header p {
    margin: 0;

    color: var(--hf-ink-faint);

    font-size: 10.5px;
    line-height: 1.5;
}

.settings-form {
    display: flex;
    flex-direction: column;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.form-group {
    margin-bottom: 17px;
}

.form-group label {
    display: block;

    margin-bottom: 7px;

    color: var(--hf-ink-soft);

    font-size: 10.5px;
    font-weight: 600;
}

.form-group input {
    width: 100%;

    padding: 12px 13px;

    border: 1px solid var(--hf-border);
    border-radius: 8px;

    outline: none;

    background: var(--hf-bg);
    color: var(--hf-ink);

    font-size: 11.5px;
    font-family: inherit;

    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
}

.form-group input::placeholder {
    color: var(--hf-ink-faint);
}

.form-group input:focus {
    border-color: var(--hf-green);
    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(63, 110, 105, .10);
}

.password-input {
    display: flex;
    align-items: center;

    border: 1px solid var(--hf-border);
    border-radius: 8px;

    background: var(--hf-bg);

    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
}

.password-input:focus-within {
    border-color: var(--hf-green);
    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(63, 110, 105, .10);
}

.password-input input {
    border: none;
    background: transparent;
    box-shadow: none;
}

.password-input input:focus {
    box-shadow: none;
    background: transparent;
}

.toggle-password {
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    width: 38px;
    height: 100%;

    border: none;
    background: transparent;

    color: var(--hf-ink-faint);

    cursor: pointer;

    transition: color .15s ease;
}

.toggle-password:hover {
    color: var(--hf-green);
}

/* ---------- Notification toggle row ---------- */

.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 16px;

    padding: 14px;

    margin-bottom: 6px;

    border: 1px solid var(--hf-border);
    border-radius: 10px;

    background: var(--hf-bg);

    cursor: pointer;
}

.toggle-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.toggle-text strong {
    color: var(--hf-ink);
    font-size: 12px;
    font-weight: 600;
}

.toggle-text span {
    color: var(--hf-ink-faint);
    font-size: 10px;
    line-height: 1.5;
}

.toggle-switch {
    position: relative;
    flex-shrink: 0;

    width: 42px;
    height: 24px;

    border-radius: 999px;

    background: var(--hf-border);

    transition: background .15s ease;
}

.toggle-switch--on {
    background: var(--hf-green);
}

.toggle-switch input {
    position: absolute;
    inset: 0;

    margin: 0;
    opacity: 0;
    cursor: pointer;
}

.toggle-knob {
    position: absolute;

    top: 3px;
    left: 3px;

    width: 18px;
    height: 18px;

    border-radius: 50%;

    background: #ffffff;

    box-shadow: 0 1px 3px rgba(0, 0, 0, .2);

    transition: transform .15s ease;
}

.toggle-switch--on .toggle-knob {
    transform: translateX(18px);
}

.error {
    display: block;

    margin-top: 6px;

    color: var(--hf-red);

    font-size: 10px;
}

.settings-actions {
    display: flex;
    justify-content: flex-end;

    margin-top: 4px;
}

.save-button {
    padding: 11px 20px;

    border: none;
    border-radius: 9px;

    background: var(--hf-green);
    color: #ffffff;

    font-size: 11.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.save-button:hover:not(:disabled) {
    background: var(--hf-green-dark);
}

.save-button:disabled {
    background: #cbd7d5;
    cursor: not-allowed;
}

/* ---------- Feedback overlay ---------- */

.feedback-overlay {
    position: fixed;
    inset: 0;

    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(30, 45, 43, .28);

    backdrop-filter: blur(3px);
}

.feedback-modal {
    width: 300px;

    padding: 32px 26px;

    background: #ffffff;

    border-radius: 16px;

    text-align: center;

    box-shadow: 0 20px 50px rgba(0, 0, 0, .15);
}

.feedback-icon {
    width: 68px;
    height: 68px;

    margin: 0 auto 16px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.checkmark {
    width: 68px;
    height: 68px;
}

.check-circle {
    stroke: var(--hf-green);

    stroke-width: 2;

    stroke-dasharray: 151;
    stroke-dashoffset: 151;

    animation: circleDraw .5s ease forwards;
}

.check-line {
    stroke: var(--hf-green);

    stroke-width: 3.5;

    stroke-linecap: round;
    stroke-linejoin: round;

    stroke-dasharray: 40;
    stroke-dashoffset: 40;

    animation: checkDraw .45s ease .45s forwards;
}

.error-circle {
    stroke: var(--hf-red);
    stroke-width: 2;

    stroke-dasharray: 151;
    stroke-dashoffset: 151;

    animation: circleError .5s ease forwards;
}

.error-line {
    stroke: var(--hf-red);
    stroke-width: 3.5;

    stroke-linecap: round;

    stroke-dasharray: 50;
    stroke-dashoffset: 50;

    animation: errorDraw .4s ease .4s forwards;
}

.feedback-modal h3 {
    margin: 0 0 6px;

    font-family: Georgia, serif;

    font-size: 20px;
    font-weight: normal;

    color: var(--hf-ink);
}

.feedback-modal--error h3 {
    color: var(--hf-red);
}

.feedback-modal p {
    margin: 0;

    font-size: 12px;

    color: var(--hf-ink-faint);
}

@keyframes circleDraw {
    from { stroke-dashoffset: 151; }
    to { stroke-dashoffset: 0; }
}

@keyframes checkDraw {
    from { stroke-dashoffset: 40; }
    to { stroke-dashoffset: 0; }
}

@keyframes circleError {
    from { stroke-dashoffset: 151; }
    to { stroke-dashoffset: 0; }
}

@keyframes errorDraw {
    from { stroke-dashoffset: 50; }
    to { stroke-dashoffset: 0; }
}

.success-enter-active,
.error-enter-active {
    animation: feedbackIn .3s ease;
}

.success-leave-active,
.error-leave-active {
    animation: feedbackOut .25s ease;
}

@keyframes feedbackIn {
    from { opacity: 0; transform: scale(.9); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes feedbackOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(.92); }
}

@media (max-width: 700px) {

    .main-content {
        padding: 20px 15px;
    }

    .settings-card {
        padding: 18px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .toggle-row {
        align-items: flex-start;
    }

}
</style>