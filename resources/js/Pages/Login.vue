<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

const logoUrl = '/images/logo.jpg';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <Head title="Login" />

    <div class="auth-page">

        <div class="auth-box">

            <!-- LOGO -->
            <div class="auth-logo">
                <img
                    :src="logoUrl"
                    alt="Lamore Perfumes"
                >
            </div>

            <!-- TITLE -->
            <div class="auth-title">
                <h1>Log in</h1>
            </div>

            <form
                class="auth-form"
                @submit.prevent="submit"
            >

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
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                    >

                    <div
                        v-if="form.errors.password"
                        class="error"
                    >
                        {{ form.errors.password }}
                    </div>

                </div>


                <!-- FORGOT -->
                <div class="forgot">

                    <Link href="/forgot-password">
                        Lupa kata sandi?
                    </Link>

                </div>


                <!-- BUTTON -->
                <button
                    type="submit"
                    class="btn-submit"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Memproses...' : 'Log in' }}
                </button>

            </form>


            <!-- REGISTER -->
            <div class="auth-switch">

                Belum punya akun?

                <Link href="/register">
                    Daftar
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
    margin-bottom: 32px;
}

.auth-title h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 32px;

    font-weight: normal;

    color: #555;
}

.form-group {
    margin-bottom: 18px;
}

.form-label {
    display: block;

    margin-bottom: 7px;

    font-size: 11px;

    color: #777;
}

.form-input {
    width: 100%;
    height: 46px;

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

.forgot {
    text-align: right;

    margin-top: -5px;

    margin-bottom: 20px;
}

.forgot a {
    color: #777;

    font-size: 10px;

    text-decoration: none;
}

.forgot a:hover {
    text-decoration: underline;
}

.btn-submit {
    width: 100%;

    border: none;

    border-radius: 9px;

    background: #7fbd6c;

    color: white;

    padding: 13px;

    font-size: 12px;

    font-weight: 600;

    cursor: pointer;

    transition: .2s;
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

.auth-switch a:hover {
    text-decoration: underline;
}
</style>