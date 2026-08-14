<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    parfums: {
        type: Array,
        default: () => [],
    },
});

const showModal = ref(false);

const form = useForm({
    nama: '',
    harga: '',
    stok: '',
    foto: null,
});

const openModal = () => {
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    if (form.processing) return;

    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.post('/admin/stok', {
        forceFormData: true,

        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const handleFoto = (event) => {
    form.foto = event.target.files[0];
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Kelola Stok" />

    <div class="stok-page">

        <header class="page-header">

            <div class="header-left">

                <Link
                    href="/admin/dashboard"
                    class="back-button"
                >
                    ←
                </Link>

                <div>
                    <h1>Kelola Stok</h1>

                    <p>
                        Kelola persediaan parfum Lamore Perfumes.
                    </p>
                </div>

            </div>

            <button
                type="button"
                class="add-button"
                @click="openModal"
            >
                <span>+</span>
                Tambah Stok
            </button>

        </header>

        <section
            v-if="parfums.length === 0"
            class="empty-card"
        >

            <div class="empty-icon">
                ♡
            </div>

            <h2>Belum Ada Stok Parfum</h2>

            <p>
                Belum ada parfum yang tersimpan.
                Tambahkan produk pertama untuk mulai mengelola stok.
            </p>

            <button
                type="button"
                class="empty-button"
                @click="openModal"
            >
                + Tambah Stok Parfum
            </button>

        </section>

        <section
            v-else
            class="product-grid"
        >

            <article
                v-for="parfum in parfums"
                :key="parfum.id"
                class="product-card"
            >

                <div class="product-image">

                    <img
                        v-if="parfum.foto"
                        :src="`/storage/${parfum.foto}`"
                        :alt="parfum.nama"
                    >

                    <div
                        v-else
                        class="no-image"
                    >
                        ♡
                    </div>

                </div>

                <div class="product-info">

                    <h2>
                        {{ parfum.nama }}
                    </h2>

                    <p class="price">
                        {{ formatRupiah(parfum.harga) }}
                    </p>

                    <div class="stock-row">

                        <span>Stok</span>

                        <strong>
                            {{ parfum.stok }}
                        </strong>

                    </div>

                </div>

            </article>

        </section>

        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="closeModal"
        >

            <div class="modal">

                <div class="modal-header">

                    <div>
                        <h2>Tambah Stok Parfum</h2>

                        <p>
                            Masukkan informasi produk parfum.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        @click="closeModal"
                    >
                        ×
                    </button>

                </div>

                <form
                    class="stok-form"
                    @submit.prevent="submit"
                >

                    <div class="form-group">

                        <label>
                            Foto Produk
                        </label>

                        <label class="upload-box">

                            <span class="upload-icon">
                                +
                            </span>

                            <span v-if="!form.foto">
                                Pilih foto produk
                            </span>

                            <span v-else>
                                {{ form.foto.name }}
                            </span>

                            <input
                                type="file"
                                accept="image/*"
                                @change="handleFoto"
                            >

                        </label>

                        <small>
                            JPG, PNG atau WEBP. Maksimal 2 MB.
                        </small>

                        <span
                            v-if="form.errors.foto"
                            class="error"
                        >
                            {{ form.errors.foto }}
                        </span>

                    </div>

                    <div class="form-group">

                        <label for="nama">
                            Nama Parfum
                        </label>

                        <input
                            id="nama"
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Dior Sauvage"
                        >

                        <span
                            v-if="form.errors.nama"
                            class="error"
                        >
                            {{ form.errors.nama }}
                        </span>

                    </div>

                    <div class="form-group">

                        <label for="harga">
                            Harga
                        </label>

                        <div class="price-input">

                            <span>
                                Rp
                            </span>

                            <input
                                id="harga"
                                v-model="form.harga"
                                type="number"
                                min="0"
                                placeholder="Masukkan harga"
                            >

                        </div>

                        <span
                            v-if="form.errors.harga"
                            class="error"
                        >
                            {{ form.errors.harga }}
                        </span>

                    </div>

                    <div class="form-group">

                        <label for="stok">
                            Jumlah Stok
                        </label>

                        <input
                            id="stok"
                            v-model="form.stok"
                            type="number"
                            min="0"
                            placeholder="Contoh: 10"
                        >

                        <span
                            v-if="form.errors.stok"
                            class="error"
                        >
                            {{ form.errors.stok }}
                        </span>

                    </div>

                    <div class="modal-actions">

                        <button
                            type="button"
                            class="cancel-button"
                            @click="closeModal"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="save-button"
                            :disabled="form.processing"
                        >
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Stok'
                            }}
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.stok-page {
    min-height: 100vh;
    padding: 35px;
    background: #edf3f2;
    color: #526363;
}


.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 30px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.back-button {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #d5e3e1;
    border-radius: 10px;

    background: #ffffff;

    color: #5c8585;

    font-size: 20px;

    text-decoration: none;

    transition: 0.2s;
}

.back-button:hover {
    background: #eaf3f2;
}

.page-header h1 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 29px;
    font-weight: normal;

    color: #344747;
}

.page-header p {
    margin: 0;

    font-size: 11px;

    color: #879797;
}


.add-button,
.empty-button {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    padding: 12px 18px;

    border: none;
    border-radius: 9px;

    background: #477878;

    color: #ffffff;

    font-size: 11px;

    cursor: pointer;

    transition: 0.2s;
}

.add-button:hover,
.empty-button:hover {
    background: #386666;
}

.add-button span {
    font-size: 18px;
}


.empty-card {
    min-height: 500px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    padding: 40px;

    background: #fbfdfc;

    border: 1px solid #d7e3e1;

    border-radius: 16px;

    text-align: center;

    box-shadow: 0 8px 25px rgba(50, 90, 90, 0.05);
}

.empty-icon {
    width: 75px;
    height: 75px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 20px;

    border-radius: 50%;

    background: #eaf3f2;

    color: #6f9d9d;

    font-size: 30px;
}

.empty-card h2 {
    margin: 0 0 9px;

    font-family: Georgia, serif;

    font-size: 22px;
    font-weight: normal;

    color: #344747;
}

.empty-card p {
    max-width: 420px;

    margin: 0 0 22px;

    font-size: 11px;

    line-height: 1.8;

    color: #8b9999;
}

.product-grid {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;
}

.product-card {
    overflow: hidden;

    background: #fbfdfc;

    border: 1px solid #d7e3e1;

    border-radius: 14px;

    box-shadow: 0 8px 25px rgba(50, 90, 90, 0.05);
}

.product-image {
    height: 220px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f2f7f6;
}

.product-image img {
    width: 100%;
    height: 100%;

    object-fit: contain;
}

.no-image {
    color: #a3b5b5;

    font-size: 35px;
}

.product-info {
    padding: 18px;
}

.product-info h2 {
    margin: 0 0 8px;

    font-family: Georgia, serif;

    font-size: 17px;
    font-weight: normal;

    color: #344747;
}

.price {
    margin: 0 0 15px;

    color: #477878;

    font-size: 13px;
    font-weight: 600;
}

.stock-row {
    display: flex;
    justify-content: space-between;

    padding-top: 12px;

    border-top: 1px solid #e2ebea;

    font-size: 10px;

    color: #899999;
}

.stock-row strong {
    color: #477878;
}

.modal-overlay {
    position: fixed;

    inset: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 25px;

    background: rgba(24, 49, 49, 0.45);

    z-index: 2000;
}

.modal {
    width: 100%;
    max-width: 480px;
    max-height: 90vh;

    overflow-y: auto;

    padding: 28px;

    background: #fbfdfc;

    border: 1px solid #d7e3e1;

    border-radius: 17px;

    box-shadow: 0 25px 70px rgba(20, 50, 50, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    margin-bottom: 25px;
}

.modal-header h2 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 21px;
    font-weight: normal;

    color: #344747;
}

.modal-header p {
    margin: 0;

    font-size: 10px;

    color: #899999;
}

.modal-close {
    width: 32px;
    height: 32px;

    border: none;
    border-radius: 8px;

    background: transparent;

    color: #899999;

    font-size: 24px;

    cursor: pointer;
}

.modal-close:hover {
    background: #edf3f2;
}
.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-size: 10px;

    color: #657575;
}

.form-group input {
    width: 100%;

    padding: 12px 13px;

    border: 1px solid #d4e2e0;

    border-radius: 8px;

    outline: none;

    background: #ffffff;

    color: #526363;

    font-size: 11px;

    transition: 0.2s;
}

.form-group input:focus {
    border-color: #7eabab;

    box-shadow: 0 0 0 3px rgba(126, 171, 171, 0.12);
}

.form-group input::placeholder {
    color: #b0bbbb;
}

.upload-box {
    min-height: 80px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 9px;

    padding: 15px;

    border: 1px dashed #abc4c2;

    border-radius: 9px;

    background: #f6faf9;

    color: #779292;

    font-size: 10px;

    cursor: pointer;
}

.upload-box:hover {
    background: #edf5f4;
}

.upload-box input {
    display: none;
}

.upload-icon {
    font-size: 20px;
}

.form-group small {
    display: block;

    margin-top: 6px;

    font-size: 8px;

    color: #a0adad;
}

.price-input {
    display: flex;
    align-items: center;

    border: 1px solid #d4e2e0;

    border-radius: 8px;

    background: #ffffff;

    overflow: hidden;
}

.price-input span {
    padding-left: 13px;

    color: #819090;

    font-size: 11px;
}

.price-input input {
    border: none;

    border-radius: 0;

    box-shadow: none;
}

.error {
    display: block;

    margin-top: 5px;

    color: #c87878;

    font-size: 9px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;

    gap: 10px;

    margin-top: 25px;
}

.cancel-button,
.save-button {
    padding: 11px 18px;

    border-radius: 8px;

    font-size: 10px;

    cursor: pointer;

    transition: 0.2s;
}

.cancel-button {
    border: 1px solid #d5e2e0;

    background: #ffffff;

    color: #788888;
}

.cancel-button:hover {
    background: #f0f5f4;
}

.save-button {
    border: none;

    background: #477878;

    color: white;
}

.save-button:hover {
    background: #386666;
}

.save-button:disabled {
    opacity: 0.6;

    cursor: not-allowed;
}

@media (max-width: 1000px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 750px) {
    .stok-page {
        padding: 22px 15px;
    }

    .page-header {
        align-items: flex-start;
        gap: 15px;
    }

    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 500px) {
    .page-header {
        flex-direction: column;
    }

    .add-button {
        width: 100%;
    }

    .product-grid {
        grid-template-columns: 1fr;
    }

    .modal {
        padding: 22px;
    }
}
</style>