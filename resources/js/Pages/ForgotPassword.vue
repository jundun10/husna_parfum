<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Mail } from 'lucide-vue-next';

const page = usePage();
const logoUrl = '/images/logo-hf.png';
const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Lupa Password" />

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

                    <h1>Lupa Password?</h1>

                    <p>
                        Jangan khawatir. Masukkan email akunmu
                        dan kami akan membantu kamu mengatur
                        ulang password.
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
                        <h2>Reset Password</h2>

                        <p>
                            Masukkan email yang kamu gunakan untuk
                            mendaftar. Kami akan mengirimkan link
                            untuk mengatur ulang password.
                        </p>
                    </div>

                    <div
                        v-if="page.props.flash?.status"
                        class="success-message"
                    >
                        {{ page.props.flash.status }}
                    </div>

                    <div
                        v-if="form.errors.email"
                        class="error-message"
                    >
                        {{ form.errors.email }}
                    </div>

                    <form
                        class="auth-form"
                        @submit.prevent="submit"
                    >

                        <div class="form-group">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                            </label>

                            <div class="input-with-icon">

                                <Mail
                                    :size="16"
                                    :stroke-width="1.7"
                                    class="input-icon"
                                />

                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="form-input"
                                    placeholder="Masukkan email kamu"
                                    autocomplete="email"
                                    required
                                >

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn-submit"
                            :disabled="form.processing"
                        >
                            {{
                                form.processing
                                    ? 'Mengirim...'
                                    : 'Kirim Link Reset'
                            }}
                        </button>

                    </form>

                    <div class="back-login">
                        <Link href="/login">
                            ← Kembali ke Login
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
    margin-bottom: 22px;
}

.auth-title h2 {
    margin: 0 0 8px;

    font-size: 28px;
    font-weight: 500;

    color: var(--forest-deep);
}

.auth-title p {
    margin: 0;

    color: var(--text-muted);

    font-size: 12px;
    line-height: 1.75;
}


/* Messages */

.success-message {
    margin-bottom: 18px;

    padding: 12px 14px;

    border-radius: 7px;

    background: var(--sage-soft);
    border: 1px solid #cfe1d5;

    color: var(--forest);

    font-size: 11.5px;
    line-height: 1.6;
}

.error-message {
    margin-bottom: 18px;

    padding: 12px 14px;

    border-radius: 7px;

    background: #fbeceb;
    border: 1px solid #f0d6d6;

    color: #b5675f;

    font-size: 11.5px;
    line-height: 1.6;
}


/* Form */

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;

    margin-bottom: 7px;

    color: var(--text-muted);

    font-size: 11px;
    font-weight: 500;
}

.input-with-icon {
    position: relative;

    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 14px;

    color: var(--text-muted);

    pointer-events: none;
}

.form-input {
    width: 100%;
    height: 46px;

    padding: 0 14px 0 40px;

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


/* Back to login */

.back-login {
    margin-top: 22px;

    text-align: center;
}

.back-login a {
    color: var(--text-muted);

    font-size: 11.5px;

    text-decoration: none;

    transition: color .2s ease;
}

.back-login a:hover {
    color: var(--forest);
}


/* Focus visibility */

.form-input:focus-visible,
.btn-submit:focus-visible,
.back-login a:focus-visible {
    outline: 2px solid var(--sage);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .form-input,
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
        font-size: 24px;
    }

}

</style>