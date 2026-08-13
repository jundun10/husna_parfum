<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

const logoUrl = '/images/logo.jpg';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <Head title="Daftar" />

    <div class="auth-page">

        <div class="auth-box">

           
            <div class="auth-logo">
                <img
                    :src="logoUrl"
                    alt="Lamore Perfumes"
                >
            </div>


            <!-- TITLE -->
            <div class="auth-title">
                <h1>Daftar</h1>
            </div>


            <form
                class="auth-form"
                @submit.prevent="submit"
            >

                <!-- NAME -->
                <div class="form-group">

                    <label class="form-label">
                        Nama
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        placeholder="Masukkan nama"
                        autocomplete="name"
                    >

                    <div
                        v-if="form.errors.name"
                        class="error"
                    >
                        {{ form.errors.name }}
                    </div>

                </div>


                <!-- EMAIL -->
                <div class="form-group">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        class="form-input"
                        placeholder="Masukkan email"
                        autocomplete="email"
                    >

                    <div
                        v-if="form.errors.email"
                        class="error"
                    >
                        {{ form.errors.email }}
                    </div>

                </div>


                <!-- PASSWORD -->
                <div class="form-group">

                    <label class="form-label">
                        Password
                    </label>

                    <input
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        placeholder="Buat password"
                        autocomplete="new-password"
                    >

                    <div
                        v-if="form.errors.password"
                        class="error"
                    >
                        {{ form.errors.password }}
                    </div>

                </div>


                <!-- CONFIRM PASSWORD -->
                <div class="form-group">

                    <label class="form-label">
                        Konfirmasi Password
                    </label>

                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-input"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                    >

                </div>


                <!-- BUTTON -->
                <button
                    type="submit"
                    class="btn-submit"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Membuat akun...' : 'Daftar' }}
                </button>
                <div class="social-section">

    <div class="social-divider">
        <span>amasuk dengan</span>
    </div>

    <div class="social-buttons">

        <!-- GOOGLE -->
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


        <!-- FACEBOOK -->
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


            <!-- LOGIN -->
            <div class="auth-switch">

                Sudah punya akun?

                <Link href="/login">
                    Login
                </Link>

            </div>

        </div>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.auth-page {
    min-height: 100vh;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #ffffff;

    padding: 40px 20px;
}

.auth-box {
    width: 100%;
    max-width: 360px;
}

.auth-logo {
    width: 120px;
    height: 120px;

    margin: 0 auto 15px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-logo img {
    width: 110px;
    height: 110px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.auth-title {
    text-align: center;

    margin-bottom: 30px;
}

.auth-title h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 32px;

    font-weight: normal;

    color: #555;
}

.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;

    margin-bottom: 7px;

    font-size: 11px;

    color: #777;
}

.form-input {
    width: 100%;
    height: 44px;

    padding: 0 14px;

    border: 1px solid #dce8d8;

    border-radius: 9px;

    background: #f7f8f7;

    color: #555;

    font-size: 12px;

    outline: none;
}

.form-input:focus {
    background: #ffffff;

    border-color: #a8c89d;

    box-shadow: 0 0 0 3px rgba(168, 200, 157, .15);
}

.error {
    margin-top: 5px;

    color: #dc6b6b;

    font-size: 10px;
}

.btn-submit {
    width: 100%;

    margin-top: 8px;

    border: none;

    border-radius: 9px;

    background: #7fbd6c;

    color: white;

    padding: 13px;

    font-size: 12px;

    font-weight: 600;

    cursor: pointer;
}

.btn-submit:hover {
    background: #6daa5c;
}

.btn-submit:disabled {
    opacity: .6;

    cursor: not-allowed;
}

.auth-switch {
    text-align: center;

    margin-top: 15px;

    font-size: 10px;

    color: #777;
}

.auth-switch a {
    color: #6daa5c;

    text-decoration: none;

    margin-left: 4px;
}
.social-section {
    margin-top: 25px;
}

.social-divider {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 14px;

    color: #aaa;

    font-size: 9px;
}

.social-divider::before,
.social-divider::after {
    content: "";

    flex: 1;

    height: 1px;

    background: #e5e5e5;
}

.social-buttons {
    display: flex;
    gap: 10px;
}

.social-btn {
    flex: 1;

    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    border: 1px solid #e2e5e2;
    border-radius: 8px;

    background: #ffffff;

    color: #666;

    font-size: 10px;

    text-decoration: none;

    cursor: pointer;

    transition: .2s;
}

.social-btn:hover {
    background: #f8faf8;
    border-color: #cfdacf;
}

.social-icon {
    width: 17px;
    height: 17px;

    flex-shrink: 0;
}

.facebook-icon {
    color: #1877F2;
}
</style>