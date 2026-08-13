<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    email: {
        type: String,
        default: '',
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password');
};
</script>

<template>
    <Head title="Reset Password" />

    <div class="reset-page">
        <div class="reset-card">

            <div class="logo">
                <img
                    :src="'/images/logo.jpg'"
                    alt="Lamore Perfumes"
                >
            </div>

            <div class="header">
                <h1>Reset Password</h1>

                <p>
                    Buat password baru untuk akun Lamore Perfumes kamu.
                </p>
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

                    <span
                        v-if="form.errors.email"
                        class="error"
                    >
                        {{ form.errors.email }}
                    </span>
                </div>

                <div class="form-group">
                    <label for="password">Password Baru</label>

                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        placeholder="Masukkan password baru"
                        autocomplete="new-password"
                        required
                    >

                    <span
                        v-if="form.errors.password"
                        class="error"
                    >
                        {{ form.errors.password }}
                    </span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Konfirmasi Password
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        placeholder="Masukkan kembali password"
                        autocomplete="new-password"
                        required
                    >

                    <span
                        v-if="form.errors.password_confirmation"
                        class="error"
                    >
                        {{ form.errors.password_confirmation }}
                    </span>
                </div>

                <button
                    type="submit"
                    class="submit-button"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Reset Password' }}
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

.reset-page {
    min-height: 100vh;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 30px;

    background: #effafa;
}

.reset-card {
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
    margin-bottom: 18px;
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
</style>