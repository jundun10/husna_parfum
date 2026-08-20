<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ShoppingCart, UserRound } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    parfums: {
        type: Array,
        default: () => [],
    },

    authUser: {
        type: Object,
        default: null,
    },
});

const kategoriAktif = ref('Semua');

const kategori = [
    'Semua',
    'Pria',
    'Wanita',
    'Unisex',
    'Parfum Lain',
];

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};
const bukaProfil = () => {
    if (!props.authUser) {
        window.location.href = '/login?from=pelanggan';
        return;
    }

    router.visit('/Pelanggan/Profil');
};

const produkTampil = computed(() => {
    if (kategoriAktif.value === 'Semua') {
        return props.parfums;
    }

    return props.parfums.filter(
        parfum => parfum.kategori === kategoriAktif.value
    );
});

const parfumDipilih = ref(null);
const showPesanModal = ref(false);

const pesanParfum = (parfum) => {
    if (!props.authUser) {
        parfumDipilih.value = parfum;
        showPesanModal.value = true;
        return;
    }

    window.location.href = `/pelanggan/pesan/${parfum.id}`;
};

const tutupPesanModal = () => {
    showPesanModal.value = false;
    parfumDipilih.value = null;
};

const lanjutLoginPesan = () => {
    window.location.href = '/login?from=pelanggan';
};


const showLoginModal = ref(false);

const bukaKeranjang = () => {
    if (!props.authUser) {
        showLoginModal.value = true;
        return;
    }

    router.visit('/pelanggan/keranjang');
};

const tutupLoginModal = () => {
    showLoginModal.value = false;
};

const lanjutLogin = () => {
    window.location.href = '/login?from=keranjang';
};

const rekomendasi = computed(() => {
    return props.parfums.slice(0, 4);
});
const masukkanKeranjang = (parfum) => {
    if (!props.authUser) {
        showLoginModal.value = true;
        return;
    }

    router.post(
        `/pelanggan/keranjang/${parfum.id}`,
        {},
        {
            preserveScroll: true,
        }
    );
};
const populer = computed(() => {
    return [...props.parfums]
        .sort((a, b) => b.stok - a.stok)
        .slice(0, 4);
});
</script>


<template>

    <Head title="Koleksi Parfum" />

    <div class="customer-page">

        <header class="customer-header">

    <Link
        href="/"
        class="brand"
    >
        <h1>
            Lamore
            <span>Perfumes</span>
        </h1>
    </Link>

    <div class="header-actions">

        <button
            type="button"
            class="cart-button"
            @click="bukaKeranjang"
        >
            <ShoppingCart :size="19" />
        </button>

       <div class="profile-wrapper">

    <button
        type="button"
        class="profile-button"
        @click="bukaProfil"
        aria-label="Profil"
    >

        <span
            v-if="props.authUser"
            class="profile-avatar"
        >
            {{ props.authUser.name?.charAt(0).toUpperCase() }}
        </span>

        <UserRound
            v-else
            :size="19"
            :stroke-width="1.8"
        />

    </button>

</div>

    </div>

</header>

        <section class="collection-heading">

            <span class="section-label">
                KOLEKSI LAMORE
            </span>

            <h2>
                Koleksi Parfum
            </h2>

            <p>
                Temukan aroma yang sesuai dengan
                karakter dan kepribadianmu.
            </p>

        </section>

        <section class="category-section">

            <div class="category-list">

                <button
                    v-for="item in kategori"
                    :key="item"
                    type="button"
                    class="category-button"
                    :class="{
                        active: kategoriAktif === item
                    }"
                    @click="kategoriAktif = item"
                >
                    {{ item }}
                </button>

            </div>

        </section>

        <section class="collection-section">

            <div class="collection-top">

                <div>

                    <span class="section-label">
                        {{ kategoriAktif.toUpperCase() }}
                    </span>

                    <h3>
                        Pilihan untukmu
                    </h3>

                </div>

                <span class="product-count">
                    {{ produkTampil.length }} produk
                </span>

            </div>


            <div
                v-if="produkTampil.length"
                class="horizontal-products"
            >

                <article
                    v-for="parfum in produkTampil"
                    :key="parfum.id"
                    class="product-card horizontal-card"
                >

                    <div class="product-number">
                        {{ String(parfum.id).padStart(2, '0') }}
                    </div>

                    <div class="product-visual">

                    <img
                        v-if="parfum.foto"
                        :src="`/storage/${parfum.foto}`"
                        :alt="parfum.nama"
                    >

                    <span
                        v-else
                        class="photo-placeholder"
                    >
                        FOTO PARFUM
                    </span>

                    </div>


                    <div class="product-info">

                        <span class="product-label">
                            LAMORE PERFUMES
                        </span>

                        <h4>
                            {{ parfum.nama }}
                        </h4>

                        <strong class="product-price">
                            {{ formatRupiah(parfum.harga) }}
                        </strong>

                        <div class="product-stock">

                            <span>
                                Stok
                            </span>

                            <strong>
                                {{ parfum.stok }}
                            </strong>

                        </div>

                    </div>

                    <div class="product-actions">

                        <button
                            type="button"
                            class="cart-small"
                            title="Masukkan ke keranjang"
                            @click="masukkanKeranjang(parfum)"
                        >
                            <ShoppingCart :size="17" />
                        </button>

                        <button
                            type="button"
                            class="order-button"
                            :disabled="parfum.stok <= 0"
                            @click="pesanParfum(parfum)"
                        >
                            {{
                                parfum.stok > 0
                                    ? 'Pesan'
                                    : 'Stok Habis'
                            }}
                        </button>

                    </div>

                </article>

            </div>


            <div
                v-else
                class="empty-product"
            >
                Belum ada parfum tersedia.
            </div>

        </section>


        <section class="special-section">

    <div class="special-header">

        <div>
            <span class="section-label">
                PILIHAN LAMORE
            </span>

            <h3>
                Rekomendasi Untuk Kamu
            </h3>
        </div>
    </div>


    <div class="horizontal-products">

        <article
            v-for="parfum in rekomendasi"
            :key="`recommend-${parfum.id}`"
            class="product-card horizontal-card"
        >

            <div class="product-number">
                {{ String(parfum.id).padStart(2, '0') }}
            </div>


            <div class="product-visual">

    <img
        v-if="parfum.foto"
        :src="`/storage/${parfum.foto}`"
        :alt="parfum.nama"
    >

        <span
            v-else
            class="photo-placeholder"
        >
            FOTO PARFUM
        </span>

        </div>


            <div class="product-info">

                <span class="product-label">
                    LAMORE PERFUMES
                </span>

                <h4>
                    {{ parfum.nama }}
                </h4>

                <strong class="product-price">
                    {{ formatRupiah(parfum.harga) }}
                </strong>

                <div class="product-stock">

                    <span>
                        Stok
                    </span>

                    <strong>
                        {{ parfum.stok }}
                    </strong>

                </div>

            </div>

            <div class="product-actions">

                <button
                    type="button"
                    class="cart-small"
                    @click="bukaKeranjang"
                >
                    <ShoppingCart :size="17" />
                </button>

                <button
                    type="button"
                    class="order-button"
                    :disabled="parfum.stok <= 0"
                    @click="pesanParfum(parfum)"
                >
                    {{
                        parfum.stok > 0
                            ? 'Pesan'
                            : 'Stok Habis'
                    }}
                </button>

            </div>

        </article>

    </div>

</section>

<section class="special-section">

    <div class="special-header">

        <div>
            <span class="section-label">
                FAVORIT PELANGGAN
            </span>

            <h3>
                Parfum Paling Laris
            </h3>
        </div>

        <span class="scroll-hint">
            Geser untuk melihat lainnya →
        </span>

    </div>


    <div class="horizontal-products">

        <article
            v-for="parfum in populer"
            :key="`popular-${parfum.id}`"
            class="product-card horizontal-card"
        >

            <div class="product-number">
                {{ String(parfum.id).padStart(2, '0') }}
            </div>


            <div class="product-visual">

            <img
                v-if="parfum.foto"
                :src="`/storage/${parfum.foto}`"
                :alt="parfum.nama"
            >

            <span v-else class="photo-placeholder">
                FOTO PARFUM
            </span>

            </div>

            <div class="product-info">

                <span class="product-label">
                    LAMORE PERFUMES
                </span>

                <h4>
                    {{ parfum.nama }}
                </h4>

                <strong class="product-price">
                    {{ formatRupiah(parfum.harga) }}
                </strong>

                <div class="product-stock">

                    <span>
                        Stok
                    </span>

                    <strong>
                        {{ parfum.stok }}
                    </strong>

                </div>

            </div>

            <div class="product-actions">

                <button
                    type="button"
                    class="cart-small"
                    @click="bukaKeranjang"
                >
                    <ShoppingCart :size="17" />
                </button>

                <button
                    type="button"
                    class="order-button"
                    :disabled="parfum.stok <= 0"
                    @click="pesanParfum(parfum)"
                >
                    {{
                        parfum.stok > 0
                            ? 'Pesan'
                            : 'Stok Habis'
                    }}
                </button>

            </div>

        </article>

    </div>

</section>

    </div>
    <div
    v-if="showLoginModal"
    class="login-modal-overlay"
    @click.self="tutupLoginModal"
>
    <div class="login-modal">

        <div class="login-modal-icon">
            🛒
        </div>

        <h3>Login Terlebih Dahulu</h3>

        <p>
            Silakan login terlebih dahulu untuk melihat
            dan mengelola keranjang belanja kamu.
        </p>

        <div class="login-modal-actions">
            <button
                type="button"
                class="modal-cancel"
                @click="tutupLoginModal"
            >
                Batal
            </button>

            <button
                type="button"
                class="modal-login"
                @click="lanjutLogin"
            >
                Login
            </button>
        </div>

    </div>
</div>
<div
    v-if="showPesanModal"
    class="login-modal-overlay"
    @click.self="tutupPesanModal"
>
    <div class="login-modal">

        <div class="login-modal-icon">
            🛍️
        </div>

        <h3>Login Terlebih Dahulu</h3>

        <p>
            Silakan login terlebih dahulu untuk memesan
            parfum yang kamu pilih.
        </p>

        <div class="login-modal-actions">
            <button
                type="button"
                class="modal-cancel"
                @click="tutupPesanModal"
            >
                Batal
            </button>

            <button
                type="button"
                class="modal-login"
                @click="lanjutLoginPesan"
            >
                Login
            </button>
        </div>

    </div>
</div>

</template>


<style scoped>

* {
    box-sizing: border-box;
}

.customer-page {
    min-height: 100vh;

    padding: 30px 6% 70px;

    background:
        linear-gradient(
            135deg,
            #f7faf9 0%,
            #eef5f3 100%
        );

    color: #304c4b;
}


.customer-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    max-width: 1300px;

    margin: 0 auto 55px;
}

.brand {
    text-decoration: none;
}

.brand h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 25px;

    font-weight: normal;

    letter-spacing: 1px;

    color: #304c4b;
}

.brand h1 span {
    display: block;

    margin-top: -2px;

    font-family: Arial, sans-serif;

    font-size: 8px;

    letter-spacing: 4px;

    text-align: center;

    color: #668b89;
}

.cart-button {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: none;
    background: transparent;

    color: #477c7a;

    cursor: pointer;
    transition: .2s;
}

.cart-button:hover {
    color: #6f9d9d;
    background: transparent;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 18px;
}

.profile-wrapper {
    position: relative;
}

.profile-button {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid #d1dfdc;
    border-radius: 50%;

    background: #ffffff;
    color: #477c7a;

    cursor: pointer;
    transition: .2s;
}

.profile-button:hover {
    background: #edf4f2;
}

.profile-avatar {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #eaf7f7;
    color: #6f9d9d;

    font-family: Georgia, serif;
    font-size: 15px;
}

.profile-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;

    width: 230px;

    padding: 14px;

    background: #ffffff;

    border: 1px solid #dce7e5;
    border-radius: 12px;

    box-shadow: 0 15px 35px rgba(80, 110, 100, .12);

    z-index: 100;
}

.profile-info strong {
    display: block;

    margin-bottom: 5px;

    color: #445858;
    font-size: 12px;
}

.profile-info span {
    display: block;

    color: #99a8a6;
    font-size: 9px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.profile-divider {
    height: 1px;

    margin: 12px 0;

    background: #edf2f1;
}

.profile-logout {
    width: 100%;

    display: flex;
    align-items: center;

    gap: 8px;

    padding: 9px 10px;

    border: 1px solid #f0dddd;
    border-radius: 8px;

    background: #ffffff;
    color: #b77777;

    font-size: 10px;
    cursor: pointer;
}

.profile-logout:hover {
    background: #fff8f8;
}

.profile-login {
    display: block;

    padding: 9px 10px;

    border-radius: 8px;

    background: #7fbd6c;

    color: #ffffff;

    text-align: center;

    text-decoration: none;

    font-size: 10px;
}

.profile-login:hover {
    background: #6daa5c;
}
.collection-heading {
    max-width: 1300px;

    margin: 0 auto 30px;

    text-align: center;
}

.section-label {
    display: block;

    font-size: 8px;

    letter-spacing: 3px;

    color: #7c9c99;
}

.collection-heading h2 {
    margin: 10px 0 7px;

    font-family: Georgia, serif;

    font-size: 40px;

    font-weight: normal;

    letter-spacing: 1px;

    color: #304c4b;
}

.collection-heading p {
    margin: 0;

    font-size: 12px;

    color: #839997;
}

.category-section {
    max-width: 1300px;
    margin: 0 auto 25px;

    overflow-x: auto;

    scrollbar-width: none;
}

.category-section::-webkit-scrollbar {
    display: none;
}

.category-list {
    display: flex;

    width: max-content;
    min-width: 100%;

    border: 1px solid #dce7e5;
    border-radius: 11px;

    overflow: hidden;

    background: rgba(255, 255, 255, .75);
}

.category-button {
    flex: 0 0 auto;

    min-width: 115px;

    padding: 13px 18px;

    border: none;
    border-right: 1px solid #e2ebe9;

    background: transparent;

    color: #6e8583;

    font-size: 10px;

    white-space: nowrap;

    cursor: pointer;

    transition: .2s;
}

.category-button:last-child {
    border-right: none;
}

.category-button:hover {
    background: #edf5f3;
}

.category-button.active {
    background: #416f6d;

    color: white;
}

.collection-section {
    max-width: 1300px;

    margin: 0 auto 45px;
}

.collection-top {
    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    margin-bottom: 18px;
}

.collection-top h3 {
    margin: 6px 0 0;

    font-family: Georgia, serif;

    font-size: 25px;

    font-weight: normal;
}

.product-count {
    font-size: 10px;

    color: #8a9e9c;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(5, 190px);
    gap: 12px;
}

.product-card {
    position: relative;

    padding: 10px;

    border: 1px solid #dce7e5;

    border-radius: 14px;

    background: #ffffff;

    transition: .25s;

    overflow: hidden;
}

.product-card:hover {
    transform: none;
    box-shadow: none;
}

.product-number {
    position: absolute;

    top: 13px;
    right: 15px;

    font-family: Georgia, serif;

    font-size: 10px;

    color: #b4c5c3;
}

.product-visual {
    height: 145px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 10px;

    background: #f5f8f7;
}

.product-visual img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
    object-position: center;

    border-radius: 10px;
}

.bottle-shape {
    display: flex;

    flex-direction: column;

    align-items: center;
}

.bottle-cap {
    width: 30px;
    height: 27px;

    border-radius: 4px 4px 2px 2px;

    background: #263d3c;
}

.bottle-body {
    width: 72px;
    height: 82px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    border-radius: 9px 9px 12px 12px;

    border: 1px solid #aabdb9;

    background:
        linear-gradient(
            145deg,
            #dfeae7,
            #ffffff
        );

    color: #507673;

    font-family: Georgia, serif;

    font-size: 9px;

    letter-spacing: 1px;
}

.bottle-body small {
    margin-top: 4px;

    font-family: Arial, sans-serif;

    font-size: 4px;

    letter-spacing: 2px;
}


/* =====================================================
   PRODUCT INFO
===================================================== */

.product-info {
    padding: 13px 3px 0;
}

.product-label {
    font-size: 7px;

    letter-spacing: 2px;

    color: #8da3a1;
}

.product-info h4 {
    margin: 7px 0;

    font-family: Georgia, serif;

    font-size: 17px;

    font-weight: normal;

    color: #304c4b;
}

.product-price {
    font-size: 12px;

    color: #4a807d;
}

.product-stock {
    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 10px;

    padding-top: 9px;

    border-top: 1px solid #edf2f1;

    font-size: 9px;

    color: #91a3a1;
}

.product-stock strong {
    color: #536e6c;

    font-size: 10px;
}


/* =====================================================
   ACTION
===================================================== */

.product-actions {
    display: flex;

    gap: 7px;

    margin-top: 12px;
}

.cart-small {
    width: 38px;
    height: 34px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: 1px solid #cbdcd9;

    border-radius: 7px;

    background: white;

    color: #477c79;

    cursor: pointer;
}

.order-button {
    flex: 1;

    height: 34px;

    border: none;

    border-radius: 7px;

    background: #477c79;

    color: white;

    font-size: 10px;

    cursor: pointer;
}

.order-button:disabled {
    background: #c7d3d1;

    cursor: not-allowed;
}


/* =====================================================
   SPECIAL SECTIONS
===================================================== */

.special-section {
    max-width: 1300px;

    margin: 0 auto 25px;

    padding: 24px;

    border: 1px solid #dce7e5;

    border-radius: 15px;

    background: rgba(255,255,255,.82);
}

.special-header {
    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    margin-bottom: 18px;
}

.special-header h3 {
    margin: 6px 0 0;

    font-family: Georgia, serif;

    font-size: 22px;

    font-weight: normal;
}

.see-all {
    border: none;

    background: none;

    color: #568381;

    font-size: 10px;

    cursor: pointer;
}


/* =====================================================
   SMALL PRODUCTS
===================================================== */

.small-product-grid {
    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 15px;
}

.small-product {
    display: flex;

    align-items: center;

    gap: 14px;

    padding: 10px;

    border-radius: 10px;

    background: #f8faf9;
}

.small-bottle {
    width: 60px;
    height: 75px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: flex-end;

    flex-shrink: 0;
}

.small-cap {
    width: 17px;
    height: 15px;

    border-radius: 3px;

    background: #2c4140;
}

.small-body {
    width: 40px;
    height: 45px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 5px 5px 8px 8px;

    border: 1px solid #aebfbc;

    background: white;

    font-family: Georgia, serif;

    font-size: 13px;

    color: #527774;
}

.small-bottle.popular .small-body {
    background: #edf4f2;
}

.small-product h4 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 14px;

    font-weight: normal;

    color: #3c5654;
}

.small-product p {
    margin: 0;

    font-size: 10px;

    color: #5d8986;
}


/* =====================================================
   EMPTY
===================================================== */

.empty-product {
    padding: 60px;

    text-align: center;

    border: 1px solid #dce7e5;

    border-radius: 14px;

    background: white;

    color: #899c9a;

    font-size: 11px;
}
/* =====================================================
   HORIZONTAL PRODUCT SECTIONS
===================================================== */

.special-section {
    max-width: 1300px;

    margin: 0 auto 45px;
}

.special-header {
    display: flex;

    align-items: flex-end;
    justify-content: space-between;

    margin-bottom: 18px;
}

.special-header h3 {
    margin: 6px 0 0;

    font-family: Georgia, serif;

    font-size: 25px;

    font-weight: normal;

    color: #304c4b;
}

.scroll-hint {
    font-size: 9px;

    color: #91a3a1;
}


/* PRODUK HORIZONTAL */

.horizontal-products {
    display: flex;

    gap: 15px;

    overflow-x: auto;

    padding: 5px 3px 15px;

    scroll-behavior: smooth;

    scrollbar-width: thin;

    scrollbar-color: #b9ceca transparent;
}


/* HILANGKAN SCROLLBAR BAWAAN WEBKIT */

.horizontal-products::-webkit-scrollbar {
    height: 5px;
}

.horizontal-products::-webkit-scrollbar-track {
    background: transparent;
}

.horizontal-products::-webkit-scrollbar-thumb {
    background: #b9ceca;

    border-radius: 10px;
}
.horizontal-card {
    flex: 0 0 220px;
    width: 220px;
    min-width: 220px;
}
.photo-placeholder {
    font-size: 8px;

    letter-spacing: 2px;

    color: #9aadaa;
}
.login-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 20px;

    background: rgba(40, 55, 45, 0.35);
    backdrop-filter: blur(4px);
}

.login-modal {
    width: 100%;
    max-width: 360px;

    padding: 30px 28px;

    background: #ffffff;
    border-radius: 16px;

    text-align: center;

    box-shadow: 0 20px 60px rgba(60, 90, 65, 0.15);

    animation: modalIn .2s ease;
}

.login-modal-icon {
    width: 52px;
    height: 52px;

    margin: 0 auto 16px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #edf7ec;

    font-size: 22px;
}

.login-modal h3 {
    margin: 0 0 10px;

    font-family: Georgia, serif;

    font-size: 22px;
    font-weight: normal;

    color: #555;
}

.login-modal p {
    margin: 0 auto 25px;

    max-width: 280px;

    font-size: 11px;
    line-height: 1.7;

    color: #888;
}

.login-modal-actions {
    display: flex;
    gap: 10px;
}

.login-modal-actions button {
    flex: 1;

    padding: 11px;

    border-radius: 8px;

    font-size: 11px;

    cursor: pointer;

    transition: .2s;
}

.modal-cancel {
    border: 1px solid #dce8d8;

    background: #ffffff;

    color: #777;
}

.modal-cancel:hover {
    background: #f7faf7;
}

.modal-login {
    border: none;

    background: #7fbd6c;

    color: #ffffff;
}

.modal-login:hover {
    background: #6daa5c;
}

@keyframes modalIn {
    from {
        opacity: 0;
        transform: translateY(8px) scale(.98);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 1050px) {

    .product-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

    .small-product-grid {
        grid-template-columns:
            repeat(2, 1fr);
    }

}

@media (max-width: 750px) {

    .customer-page {
        padding: 25px 18px 50px;
    }

    .product-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .collection-heading h2 {
        font-size: 32px;
    }

}

@media (max-width: 520px) {

    .customer-header {
        margin-bottom: 35px;
    }

    .brand h1 {
        font-size: 21px;
    }

    .cart-button span {
        display: none;
    }

    .product-grid {
        grid-template-columns: 1fr;
    }

    .small-product-grid {
        grid-template-columns: 1fr;
    }

    .special-section {
        padding: 18px;
    }

}

</style>