<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const logoUrl = '/images/logo-hf.png';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
</script>

<template>
    <Head title="Daftar" />

    <div class="auth-page">

        <div class="auth-layout">

            <aside class="brand-panel">

                <div class="brand-panel-inner">

                    <div class="brand-logo">
                        <img
                            :src="logoUrl"
                            alt="HF Parfum"
                        >
                    </div>

                    <h1>Buat Akun HF Parfum</h1>

                    <p>
                        Daftar untuk mendapatkan pengalaman
                        berbelanja parfum yang lebih personal.
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
                        <h2>Daftar</h2>

                        <p>
                            Buat akun untuk mulai belanja
                            parfum favoritmu.
                        </p>
                    </div>

                    <form
                        class="auth-form"
                        @submit.prevent="submit"
                    >

                        <div class="form-group">

                            <label class="form-label">
                                Nama
                            </label>

                            <input
                                v-model="form.name"
                                type="text"
                                class="form-input"
                                placeholder="Nama lengkap"
                                autocomplete="name"
                            >

                            <div
                                v-if="form.errors.name"
                                class="error"
                            >
                                {{ form.errors.name }}
                            </div>

                        </div>

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
                                    placeholder="Buat password"
                                    autocomplete="new-password"
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

                        <div class="form-group">

                            <label class="form-label">
                                Konfirmasi Password
                            </label>

                            <div class="password-field">

                                <input
                                    v-model="form.password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    class="form-input"
                                    placeholder="Ulangi password"
                                    autocomplete="new-password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    :aria-label="
                                        showPasswordConfirmation
                                            ? 'Sembunyikan password'
                                            : 'Tampilkan password'
                                    "
                                    @click="togglePasswordConfirmationVisibility"
                                >
                                    <Eye
                                        v-if="!showPasswordConfirmation"
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

                        </div>

                        <button
                            type="submit"
                            class="btn-submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Membuat akun...' : 'Daftar' }}
                        </button>

                        <div class="social-section">

                            <div class="social-divider">
                                <span>Atau daftar dengan</span>
                            </div>

                            <div class="social-buttons">

                                <a
                                    href="/auth/google/redirect"
                                    class="social-btn"
                                >
                                    <svg
                                        class="social-icon"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M21.35 12.27c0-.79-.07-1.55-.22-2.27H12v4.3h5.23a4.47 4.47 0 0 1-1.94 2.94v2.44h3.14c1.84-1.69 2.92-4.18 2.92-7.41z"
                                            fill="#4285F4"
                                        />
                                        <path
                                            d="M12 21.75c2.63 0 4.84-.87 6.45-2.35l-3.14-2.44c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.75 9.75 0 0 0 12 21.75z"
                                            fill="#34A853"
                                        />
                                        <path
                                            d="M6.54 13.85a5.86 5.86 0 0 1 0-3.7V7.63H3.3a9.75 9.75 0 0 0 0 8.74l3.24-2.52z"
                                            fill="#FBBC05"
                                        />
                                        <path
                                            d="M12 6.12c1.43 0 2.72.49 3.73 1.45l2.8-2.8C16.84 3.23 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.38l3.24 2.52C7.31 7.84 9.46 6.12 12 6.12z"
                                            fill="#EA4335"
                                        />
                                    </svg>

                                    <span>Google</span>
                                </a>

                                <a
                                    href="https://www.facebook.com/login/"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="social-btn"
                                >
                                    <svg
                                        class="social-icon facebook-icon"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.09 4.39 23.08 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.7 4.53-4.7 1.31 0 2.68.24 2.68.24v2.96h-1.51c-1.49 0-1.96.93-1.96 1.88v2.28h3.34l-.53 3.49h-2.81V24C19.61 23.08 24 18.09 24 12.07z"
                                            fill="currentColor"
                                        />
                                    </svg>

                                    <span>Facebook</span>
                                </a>

                            </div>

                        </div>

                    </form>

                    <div class="auth-switch">
                        Sudah punya akun?

                        <Link href="/login">
                            Login
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

    font-size: 30px;
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
    max-width: 380px;
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
    margin-bottom: 26px;
}

.auth-title h2 {
    margin: 0 0 6px;

    font-size: 28px;
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
    margin-bottom: 16px;
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
    height: 44px;

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

.btn-submit {
    width: 100%;

    margin-top: 6px;

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


/* Social login */

.social-section {
    margin-top: 22px;
}

.social-divider {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 14px;

    color: var(--text-muted);

    font-size: 10px;
}

.social-divider::before,
.social-divider::after {
    content: "";

    flex: 1;

    height: 1px;

    background: var(--line);
}

.social-buttons {
    display: flex;
    gap: 10px;
}

.social-btn {
    flex: 1;

    height: 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    border: 1px solid var(--line);
    border-radius: 7px;

    background: #ffffff;

    color: var(--text);

    font-family: 'Inter', Arial, sans-serif;
    font-size: 10.5px;

    text-decoration: none;

    cursor: pointer;

    transition: background .2s ease, border-color .2s ease;
}

.social-btn:hover {
    background: var(--sage-soft);
    border-color: var(--sage);
}

.social-icon {
    width: 17px;
    height: 17px;

    flex-shrink: 0;
}

.facebook-icon {
    color: #1877F2;
}


/* Switch link */

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

.form-input:focus-visible,
.password-toggle:focus-visible,
.btn-submit:focus-visible,
.social-btn:focus-visible,
.auth-switch a:focus-visible {
    outline: 2px solid var(--sage);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .form-input,
    .password-toggle,
    .btn-submit,
    .social-btn {
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
        padding: 60px 24px 50px;
    }

}

@media (max-width: 420px) {

    .form-panel {
        padding: 50px 18px 36px;
    }

    .auth-title h2 {
        font-size: 24px;
    }

    .social-buttons {
        flex-direction: column;
    }

}

</style>