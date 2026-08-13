<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Lupa Password" />

    <div class="forgot-page">
        <div class="forgot-card">

            <div class="logo">
                <img :src="'/images/logo.jpg'" alt="Lamore Perfumes">
            </div>

            <div class="header">
                <h1>Lupa Password?</h1>

                <p>
                    Masukkan email yang kamu gunakan untuk mendaftar.
                    Kami akan mengirimkan link untuk mengatur ulang password.
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
            <form @submit.prevent="submit">

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        placeholder="Masukkan email kamu"
                        autocomplete="email"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="submit-button"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset' }}
                </button>

            </form>

            <div class="back-login">
                <Link href="/login">
                    ← Kembali ke Login
                </Link>
            </div>

        </div>
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.forgot-page {
    min-height: 100vh;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 30px;

    background: #effafa;
}

.forgot-card {
    width: 100%;
    max-width: 430px;

    padding: 42px 40px;

    background: rgba(255, 255, 255, 0.95);

    border: 1px solid #dceeee;

    border-radius: 18px;

    box-shadow: 0 15px 45px rgba(120, 150, 150, 0.12);
}

.logo {
    display: flex;
    justify-content: center;

    margin-bottom: 20px;
}

.logo img {
    width: 72px;
    height: 72px;

    object-fit: contain;

    mix-blend-mode: multiply;
}

.header {
    text-align: center;

    margin-bottom: 30px;
}

.header h1 {
    margin: 0 0 12px;

    font-family: Georgia, serif;

    font-size: 27px;
    font-weight: normal;

    color: #777;
}

.header p {
    margin: 0;

    font-size: 11px;

    line-height: 1.8;

    color: #999;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-size: 11px;

    color: #777;
}

.form-group input {
    width: 100%;

    padding: 12px 14px;

    border: 1px solid #dceeee;

    border-radius: 8px;

    outline: none;

    background: #fafdfd;

    color: #666;

    font-size: 12px;

    transition: 0.2s;
}

.form-group input:focus {
    border-color: #a8d5d5;

    background: white;
}

.form-group input::placeholder {
    color: #bbb;
}

.error {
    display: block;

    margin-top: 6px;

    font-size: 10px;

    color: #c77777;
}

.success-message {
    margin-bottom: 20px;

    padding: 11px 13px;

    border-radius: 8px;

    background: #edf8f6;

    border: 1px solid #d5eee9;

    color: #6f9992;

    font-size: 11px;

    line-height: 1.6;
}

.submit-button {
    width: 100%;

    padding: 13px;

    border: none;

    border-radius: 8px;

    background: #7fbd6c;

    color: white;

    font-size: 11px;

    cursor: pointer;

    transition: 0.2s;
}

.submit-button:hover {
    background: #7eaeae;
}

.submit-button:disabled {
    opacity: 0.6;

    cursor: not-allowed;
}

.back-login {
    margin-top: 22px;

    text-align: center;
}

.back-login a {
    color: #888;

    font-size: 10px;

    text-decoration: none;
}

.back-login a:hover {
    color: #6f9d9d;
}

.error-message {
    margin-bottom: 20px;
    padding: 11px 13px;
    border-radius: 8px;
    background: #fff4f4;
    border: 1px solid #f0d6d6;
    color: #b87878;
    font-size: 11px;
    line-height: 1.6;
}
</style>