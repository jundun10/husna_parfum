<script setup>
import { useForm, Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const logoUrl = '/images/logo-hf.png';
const page = usePage();
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <Head title="Login" />

    <div class="auth-page">

        <Link
            :href="page.props.backUrl || '/'"
            class="back-button"
            aria-label="Kembali"
        >
            <ArrowLeft :size="20" :stroke-width="1.8" />
        </Link>

        <div class="auth-layout">

            <aside class="brand-panel">

                <div class="brand-panel-inner">

                    <div class="brand-logo">
                        <img
                            :src="logoUrl"
                            alt="HF Parfum"
                        >
                    </div>

                    <h1>Selamat Datang di HF Parfum</h1>

                    <p>
                        Temukan aroma favoritmu dan nikmati
                        pengalaman berbelanja parfum yang
                        lebih personal.
                    </p>

                </div>

            </aside>

            <div class="form-panel">

                <div class="auth-box">

                    <div class="auth-logo-mobile">
                        <img
                            :src="logoUrl"
                            alt="HF Parfum"
                        >
                    </div>

                    <div class="auth-title">
                        <h2>Login</h2>

                        <p>
                            Masuk untuk melanjutkan belanja
                            parfum favoritmu.
                        </p>
                    </div>

                    <form
                        class="auth-form"
                        @submit.prevent="submit"
                    >
                        <div class="form-group">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                v-model="form.email"
                                type="email"
                                class="form-input"
                                placeholder="nama@email.com"
                                autocomplete="email"
                            >

                            <div
                                v-if="form.errors.email"
                                class="error"
                            >
                                {{ form.errors.email }}
                            </div>

                        </div>

                        <div class="form-group">

                            <label class="form-label">
                                Password
                            </label>

                            <div class="password-field">

                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="form-input"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    :aria-label="
                                        showPassword
                                            ? 'Sembunyikan password'
                                            : 'Tampilkan password'
                                    "
                                    @click="togglePasswordVisibility"
                                >
                                    <Eye
                                        v-if="!showPassword"
                                        :size="17"
                                        :stroke-width="1.7"
                                    />

                                    <EyeOff
                                        v-else
                                        :size="17"
                                        :stroke-width="1.7"
                                    />
                                </button>

                            </div>

                            <div
                                v-if="form.errors.password"
                                class="error"
                            >
                                {{ form.errors.password }}
                            </div>

                        </div>

                        <div class="form-row">

                            <label class="remember-me">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                >

                                <span>Ingat saya</span>
                            </label>

                            <Link
                                href="/forgot-password"
                                class="forgot-link"
                            >
                                Lupa Password?
                            </Link>

                        </div>

                        <button
                            type="submit"
                            class="btn-submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Memproses...' : 'Masuk' }}
                        </button>

                    </form>

                    <div class="auth-switch">
                        Belum punya akun?

                        <Link href="/register">
                            Daftar
                        </Link>
                    </div>

                </div>

            </div>

        </div>

    </div>
</template>

<style scoped>

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap');

* {
    box-sizing: border-box;
}

.auth-page {
    --forest: #23423a;
    --forest-deep: #1a332c;
    --sage: #7d9d8a;
    --sage-soft: #e9f0e8;
    --ivory: #faf9f4;
    --line: #e1e6dd;
    --text: #2c3a35;
    --text-muted: #75847b;

    position: relative;

    min-height: 100vh;

    background: #ffffff;

    font-family: 'Inter', Arial, sans-serif;

    color: var(--text);

    overflow-x: hidden;
}

.auth-page h1,
.auth-page h2 {
    font-family: 'Cormorant Garamond', Georgia, serif;
}


/* Back button */

.back-button {
    position: fixed;

    top: 22px;
    left: 22px;

    z-index: 30;

    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(255, 255, 255, .85);
    border: 1px solid var(--line);
    border-radius: 50%;

    color: var(--forest);

    text-decoration: none;

    cursor: pointer;

    backdrop-filter: blur(6px);

    transition: background .2s ease, transform .2s ease;
}

.back-button:hover {
    background: var(--sage-soft);
    transform: translateX(-2px);
}


/* Layout */

.auth-layout {
    display: grid;
    grid-template-columns: 1.05fr 1fr;

    min-height: 100vh;
}


/* Brand panel (left, desktop) */

.brand-panel {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 60px;

    background: var(--forest);

    overflow: hidden;
}

.brand-panel::before {
    content: '';

    position: absolute;

    width: 480px;
    height: 480px;

    border-radius: 50%;

    border: 1px solid rgba(255, 255, 255, .08);

    top: -140px;
    right: -160px;
}

.brand-panel::after {
    content: '';

    position: absolute;

    width: 320px;
    height: 320px;

    border-radius: 50%;

    border: 1px solid rgba(255, 255, 255, .08);

    bottom: -120px;
    left: -100px;
}

.brand-panel-inner {
    position: relative;
    z-index: 1;

    max-width: 380px;

    text-align: center;
}

.brand-logo {
    width: 96px;
    height: 96px;

    margin: 0 auto 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: rgba(255, 255, 255, .95);
}

.brand-logo img {
    width: 66px;
    height: 66px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.brand-panel-inner h1 {
    margin: 0 0 16px;

    font-size: 32px;
    font-weight: 500;
    line-height: 1.3;

    color: #ffffff;
}

.brand-panel-inner p {
    margin: 0;

    color: rgba(255, 255, 255, .75);

    font-size: 13px;
    line-height: 1.8;
}


/* Form panel (right) */

.form-panel {
    display: flex;
    align-items: center;
    justify-content: center;

    padding: 40px 30px;

    background: var(--ivory);
}

.auth-box {
    width: 100%;
    max-width: 360px;
}

.auth-logo-mobile {
    display: none;

    width: 74px;
    height: 74px;

    margin: 0 auto 18px;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #ffffff;
    border: 1px solid var(--line);
}

.auth-logo-mobile img {
    width: 50px;
    height: 50px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.auth-title {
    margin-bottom: 30px;
}

.auth-title h2 {
    margin: 0 0 6px;

    font-size: 30px;
    font-weight: 500;

    color: var(--forest-deep);
}

.auth-title p {
    margin: 0;

    color: var(--text-muted);

    font-size: 12px;
    line-height: 1.6;
}


/* Form */

.form-group {
    margin-bottom: 18px;
}

.form-label {
    display: block;

    margin-bottom: 7px;

    color: var(--text-muted);

    font-size: 11px;
    font-weight: 500;
}

.form-input {
    width: 100%;
    height: 46px;

    padding: 0 14px;

    border: 1px solid var(--line);
    border-radius: 7px;

    background: #ffffff;

    color: var(--text);

    font-family: 'Inter', Arial, sans-serif;
    font-size: 12.5px;

    outline: none;

    transition: border-color .2s ease, box-shadow .2s ease;
}

.form-input::placeholder {
    color: #b7c2bc;
}

.form-input:focus {
    border-color: var(--sage);
    box-shadow: 0 0 0 3px rgba(125, 157, 138, .15);
}

.password-field {
    position: relative;
}

.password-field .form-input {
    padding-right: 42px;
}

.password-toggle {
    position: absolute;
    top: 50%;
    right: 12px;

    transform: translateY(-50%);

    display: flex;
    align-items: center;
    justify-content: center;

    width: 26px;
    height: 26px;

    border: none;
    border-radius: 50%;

    background: transparent;
    color: var(--text-muted);

    cursor: pointer;

    transition: color .2s ease, background .2s ease;
}

.password-toggle:hover {
    color: var(--forest);
    background: var(--sage-soft);
}

.error {
    margin-top: 6px;

    color: #b5675f;

    font-size: 10.5px;
}

.form-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin: -4px 0 22px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 7px;

    color: var(--text-muted);

    font-size: 11px;

    cursor: pointer;
}

.remember-me input {
    width: 14px;
    height: 14px;

    margin: 0;

    accent-color: var(--forest);

    cursor: pointer;
}

.forgot-link {
    color: var(--text-muted);

    font-size: 11px;

    text-decoration: none;

    transition: color .2s ease;
}

.forgot-link:hover {
    color: var(--forest);
    text-decoration: underline;
}

.btn-submit {
    width: 100%;

    height: 48px;

    border: none;
    border-radius: 7px;

    background: var(--forest);

    color: #ffffff;

    font-family: 'Inter', Arial, sans-serif;
    font-size: 12.5px;
    font-weight: 600;
    letter-spacing: .2px;

    cursor: pointer;

    transition: background .2s ease, transform .15s ease;
}

.btn-submit:hover:not(:disabled) {
    background: var(--forest-deep);
}

.btn-submit:active:not(:disabled) {
    transform: scale(.99);
}

.btn-submit:disabled {
    background: #b7c4bf;

    cursor: not-allowed;
}

.auth-switch {
    margin-top: 22px;

    text-align: center;

    color: var(--text-muted);

    font-size: 11.5px;
}

.auth-switch a {
    margin-left: 4px;

    color: var(--forest);

    font-weight: 500;

    text-decoration: none;
}

.auth-switch a:hover {
    text-decoration: underline;
}


/* Focus visibility */

.back-button:focus-visible,
.form-input:focus-visible,
.password-toggle:focus-visible,
.btn-submit:focus-visible,
.forgot-link:focus-visible,
.auth-switch a:focus-visible {
    outline: 2px solid var(--sage);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .back-button,
    .form-input,
    .password-toggle,
    .btn-submit {
        transition: none !important;
    }
}


/* Responsive */

@media (max-width: 980px) {

    .auth-layout {
        grid-template-columns: 1fr;
    }

    .brand-panel {
        display: none;
    }

    .auth-logo-mobile {
        display: flex;
    }

    .form-panel {
        padding: 70px 24px 50px;
    }

}

@media (max-width: 420px) {

    .form-panel {
        padding: 60px 18px 40px;
    }

    .auth-title h2 {
        font-size: 26px;
    }

    .back-button {
        top: 16px;
        left: 16px;
    }

}

</style>