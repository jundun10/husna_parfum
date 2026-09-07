<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ShoppingCart, UserRound } from 'lucide-vue-next';
import { ref, computed, nextTick } from 'vue';

const props = defineProps({
    parfums: {
        type: Array,
        default: () => [],
    },

    authUser: {
        type: Object,
        default: null,
    },

    cartCount: {
        type: Number,
        default: 0,
    },
});

const kategoriAktif = ref('Semua');

const kategori = [
    'Semua',
    'Pria',
    'Wanita',
    'Unisex',
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

    bukaUkuranModal(parfum, 'pesan');
};

const tutupPesanModal = () => {
    showPesanModal.value = false;
    parfumDipilih.value = null;
};

const lanjutLoginPesan = () => {
    window.location.href = '/login?from=pelanggan';
};


const showLoginModal = ref(false);
const showUkuranModal = ref(false);
const parfumUkuranDipilih = ref(null);
const modeUkuranModal = ref('keranjang');
const cartButtonRef = ref(null);
const cartAnimating = ref(false);
const gambarProdukUntukAnimasi = ref(null);
const ukuranTersedia = [
    1,
    5,
    10,
    15,
    20,
    25,
    50,
];

const ukuranMlDipilih = ref(10);
const jumlahBotolDipilih = ref(1);
const hargaUkuranDipilih = computed(() => {
    const hargaPerMl = Number(
        parfumUkuranDipilih.value?.harga_per_ml || 0
    );

    const ukuran = Number(ukuranMlDipilih.value || 0);

    return hargaPerMl * ukuran;
});
const bukaUkuranModal = (parfum, mode = 'keranjang') => {
    if (!props.authUser) {
        showLoginModal.value = true;
        return;
    }

    parfumUkuranDipilih.value = parfum;
    ukuranMlDipilih.value = 10;
    jumlahBotolDipilih.value = 1;
    modeUkuranModal.value = mode;
    showUkuranModal.value = true;
};

const tutupUkuranModal = () => {
    showUkuranModal.value = false;
    parfumUkuranDipilih.value = null;
};

const tambahBotol = () => {
    if (
        parfumUkuranDipilih.value &&
        jumlahBotolDipilih.value <
            parfumUkuranDipilih.value.stok
    ) {
        jumlahBotolDipilih.value++;
    }
};

const kurangiBotol = () => {
    if (jumlahBotolDipilih.value > 1) {
        jumlahBotolDipilih.value--;
    }
};
const animasiKeKeranjang = () => {
    const source = gambarProdukUntukAnimasi.value;
    const target = cartButtonRef.value;

    if (!source || !target) {
        return;
    }

    const sourceRect = source.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();

    const clone = source.cloneNode(true);

    clone.className = 'flying-product-image';

    clone.style.left = `${sourceRect.left}px`;
    clone.style.top = `${sourceRect.top}px`;
    clone.style.width = `${sourceRect.width}px`;
    clone.style.height = `${sourceRect.height}px`;

    document.body.appendChild(clone);

    requestAnimationFrame(() => {
        clone.style.left =
            `${sourceRect.left + sourceRect.width / 2 - 35}px`;

        clone.style.top =
            `${sourceRect.top + sourceRect.height / 2 - 35}px`;

        clone.style.width = '70px';
        clone.style.height = '70px';

        clone.style.transform = 'scale(.85)';
    });
    setTimeout(() => {
        clone.style.left =
            `${targetRect.left + targetRect.width / 2 - 10}px`;

        clone.style.top =
            `${targetRect.top + targetRect.height / 2 - 10}px`;

        clone.style.width = '20px';
        clone.style.height = '20px';
        clone.style.opacity = '0';
        clone.style.borderRadius = '50%';
        clone.style.transform = 'scale(.5)';
    }, 550);

    setTimeout(() => {
        clone.remove();

        cartAnimating.value = true;

        setTimeout(() => {
            cartAnimating.value = false;
        }, 450);
    }, 1800);
};

const konfirmasiKeranjang = () => {
    if (!parfumUkuranDipilih.value) {
        return;
    }

    router.post(
        `/pelanggan/keranjang/${parfumUkuranDipilih.value.id}`,
        {
            ukuran_ml: ukuranMlDipilih.value,
            jumlah: jumlahBotolDipilih.value,
        },
        {
            preserveScroll: true,

            onSuccess: async () => {
            tutupUkuranModal();

            await nextTick();

            setTimeout(() => {
                animasiKeKeranjang();
            }, 100);
            },
        }
    );
};

const pesanSekarang = () => {
    if (!parfumUkuranDipilih.value) {
        return;
    }

    router.post(
        `/pelanggan/pesan-sekarang/${parfumUkuranDipilih.value.id}`,
        {
            ukuran_ml: ukuranMlDipilih.value,
            jumlah: jumlahBotolDipilih.value,
        },
        {
            preserveScroll: true,

            onSuccess: () => {
                tutupUkuranModal();
            },
        }
    );
};

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
const masukkanKeranjang = (parfum, event) => {
    if (event?.currentTarget) {
        gambarProdukUntukAnimasi.value =
            event.currentTarget
                .closest('.product-card')
                ?.querySelector('.product-visual img');
    }

    bukaUkuranModal(parfum, 'keranjang');
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

        <header class="site-header">

            <div class="header-inner">

                <Link
                    href="/"
                    class="brand"
                >
                    <span class="brand-mark">HF</span>
                    <span class="brand-word">Parfum</span>
                </Link>

                <div class="header-actions">

                    <button
                        ref="cartButtonRef"
                        type="button"
                        class="cart-button"
                        :class="{ 'cart-bounce': cartAnimating }"
                        @click="bukaKeranjang"
                        aria-label="Keranjang"
                    >
                        <ShoppingCart :size="18" :stroke-width="1.6" />

                        <span
                            v-if="props.cartCount > 0"
                            class="cart-badge"
                        >
                            {{ props.cartCount }}
                        </span>
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
                                :size="17"
                                :stroke-width="1.6"
                            />

                        </button>

                    </div>

                </div>

            </div>

        </header>

        <section class="intro">

            <h2>Aroma yang menjadi identitas kamu.</h2>

            <p>
                Koleksi parfum pilihan dengan bahan berkualitas,
                diracik untuk menemani setiap momen — dari yang
                santai hingga yang paling berkesan.
            </p>

        </section>

        <nav class="category-nav" aria-label="Kategori parfum">

            <div class="category-list">

                <button
                    v-for="item in kategori"
                    :key="item"
                    type="button"
                    class="category-tab"
                    :class="{ active: kategoriAktif === item }"
                    @click="kategoriAktif = item"
                >
                    {{ item }}
                </button>

            </div>

        </nav>

        <section class="collection-section">

            <div class="collection-top">

                <h3>
                    {{
                        kategoriAktif === 'Semua'
                            ? 'Semua Koleksi'
                            : `Parfum ${kategoriAktif}`
                    }}
                </h3>

                <span class="product-count">
                    {{ produkTampil.length }} produk
                </span>

            </div>


            <div
                v-if="produkTampil.length"
                class="product-grid"
            >

                <article
                    v-for="parfum in produkTampil"
                    :key="parfum.id"
                    class="product-card"
                >

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
                            Foto belum tersedia
                        </span>

                        <span
                            v-if="parfum.kategori"
                            class="product-tag"
                        >
                            {{ parfum.kategori }}
                        </span>

                        <button
                            type="button"
                            class="quick-cart"
                            title="Masukkan ke keranjang"
                            @click="masukkanKeranjang(parfum, $event)"
                        >
                            <ShoppingCart :size="15" :stroke-width="1.7" />
                        </button>

                    </div>


                    <div class="product-info">

                        <h4>
                            {{ parfum.nama }}
                        </h4>

                        <div class="product-meta">

                            <strong class="product-price">
                                {{ formatRupiah(parfum.harga) }}
                            </strong>

                            <span class="product-stock">
                                Stok {{ parfum.stok }}
                            </span>

                        </div>

                        <button
                            type="button"
                            class="order-button"
                            :disabled="parfum.stok <= 0"
                            @click="pesanParfum(parfum)"
                        >
                            {{
                                parfum.stok > 0
                                    ? 'Pesan Sekarang'
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
                Belum ada parfum tersedia untuk kategori ini.
            </div>

        </section>


        <section class="shelf-section">

            <div class="shelf-header">

                <h3>Rekomendasi Untuk Kamu</h3>

                <span class="scroll-hint">Geser untuk lihat lainnya</span>

            </div>


            <div class="horizontal-products">

                <article
                    v-for="parfum in rekomendasi"
                    :key="`recommend-${parfum.id}`"
                    class="product-card horizontal-card"
                >

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
                            Foto belum tersedia
                        </span>

                        <span
                            v-if="parfum.kategori"
                            class="product-tag"
                        >
                            {{ parfum.kategori }}
                        </span>

                        <button
                            type="button"
                            class="quick-cart"
                            title="Masukkan ke keranjang"
                            @click="masukkanKeranjang(parfum)"
                        >
                            <ShoppingCart :size="15" :stroke-width="1.7" />
                        </button>

                    </div>

                    <div class="product-info">

                        <h4>
                            {{ parfum.nama }}
                        </h4>

                        <div class="product-meta">

                            <strong class="product-price">
                                {{ formatRupiah(parfum.harga) }}
                            </strong>

                            <span class="product-stock">
                                Stok {{ parfum.stok }}
                            </span>

                        </div>

                        <button
                            type="button"
                            class="order-button"
                            :disabled="parfum.stok <= 0"
                            @click="pesanParfum(parfum)"
                        >
                            {{
                                parfum.stok > 0
                                    ? 'Pesan Sekarang'
                                    : 'Stok Habis'
                            }}
                        </button>

                    </div>

                </article>

            </div>

        </section>

        <section class="shelf-section">

            <div class="shelf-header">

                <h3>Parfum Paling Diminati</h3>

                <span class="scroll-hint">Geser untuk lihat lainnya</span>

            </div>


            <div class="horizontal-products">

                <article
                    v-for="parfum in populer"
                    :key="`popular-${parfum.id}`"
                    class="product-card horizontal-card"
                >

                    <div class="product-visual">

                        <img
                            v-if="parfum.foto"
                            :src="`/storage/${parfum.foto}`"
                            :alt="parfum.nama"
                        >

                        <span v-else class="photo-placeholder">
                            Foto belum tersedia
                        </span>

                        <span
                            v-if="parfum.kategori"
                            class="product-tag"
                        >
                            {{ parfum.kategori }}
                        </span>

                        <button
                            type="button"
                            class="quick-cart"
                            title="Masukkan ke keranjang"
                            @click="masukkanKeranjang(parfum)"
                        >
                            <ShoppingCart :size="15" :stroke-width="1.7" />
                        </button>

                    </div>

                    <div class="product-info">

                        <h4>
                            {{ parfum.nama }}
                        </h4>

                        <div class="product-meta">

                            <strong class="product-price">
                                {{ formatRupiah(parfum.harga) }}
                            </strong>

                            <span class="product-stock">
                                Stok {{ parfum.stok }}
                            </span>

                        </div>

                        <button
                            type="button"
                            class="order-button"
                            :disabled="parfum.stok <= 0"
                            @click="pesanParfum(parfum)"
                        >
                            {{
                                parfum.stok > 0
                                    ? 'Pesan Sekarang'
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
                <ShoppingCart :size="20" :stroke-width="1.6" />
            </div>

            <h3>Masuk Terlebih Dahulu</h3>

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
                <UserRound :size="20" :stroke-width="1.6" />
            </div>

            <h3>Masuk Terlebih Dahulu</h3>

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

    <div
        v-if="showUkuranModal"
        class="size-modal-overlay"
        @click.self="tutupUkuranModal"
    >
        <div class="size-modal">

            <button
                type="button"
                class="size-modal-close"
                @click="tutupUkuranModal"
                aria-label="Tutup"
            >
                ×
            </button>


            <div class="size-modal-product">

                <div class="size-modal-image">

                    <img
                        v-if="parfumUkuranDipilih?.foto"
                        :src="`/storage/${parfumUkuranDipilih.foto}`"
                        :alt="parfumUkuranDipilih.nama"
                    >

                    <span v-else>
                        Foto
                    </span>

                </div>


                <div class="size-modal-info">

                    <span class="size-modal-name">
                        {{ parfumUkuranDipilih?.nama }}
                    </span>

                    <h3>
                        {{ formatRupiah(hargaUkuranDipilih) }}
                    </h3>

                    <p>
                        Stok tersedia:
                        {{ parfumUkuranDipilih?.stok ?? 0 }}
                        botol
                    </p>

                </div>

            </div>


            <div class="size-section">

                <h4>
                    Pilih Ukuran
                </h4>

                <div class="size-list">

                    <button
                        v-for="ml in ukuranTersedia"
                        :key="ml"
                        type="button"
                        class="size-button"
                        :class="{
                            active: ukuranMlDipilih === ml
                        }"
                        @click="ukuranMlDipilih = ml"
                    >
                        {{ ml }} ml
                    </button>

                </div>

            </div>


            <div class="quantity-section">

                <h4>
                    Jumlah Botol
                </h4>

                <div class="modal-quantity">

                    <button
                        type="button"
                        @click="kurangiBotol"
                        :disabled="jumlahBotolDipilih <= 1"
                        aria-label="Kurangi jumlah"
                    >
                        −
                    </button>

                    <span>
                        {{ jumlahBotolDipilih }}
                    </span>

                    <button
                        type="button"
                        @click="tambahBotol"
                        :disabled="
                            jumlahBotolDipilih >=
                            (parfumUkuranDipilih?.stok ?? 0)
                        "
                        aria-label="Tambah jumlah"
                    >
                        +
                    </button>

                </div>

            </div>


            <button
                type="button"
                class="size-modal-submit"
                @click="
                    modeUkuranModal === 'pesan'
                        ? pesanSekarang()
                        : konfirmasiKeranjang()
                "
            >
                {{
                    modeUkuranModal === 'pesan'
                        ? 'Pesan Sekarang'
                        : 'Masukkan ke Keranjang'
                }}
            </button>

        </div>
    </div>


</template>


<style scoped>

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap');

* {
    box-sizing: border-box;
}

.customer-page {
    --forest: #23423a;
    --forest-deep: #1a332c;
    --sage: #7d9d8a;
    --sage-soft: #e9f0e8;
    --ivory: #faf9f4;
    --line: #e1e6dd;
    --text: #2c3a35;
    --text-muted: #75847b;

    min-height: 100vh;

    padding: 40px 6% 80px;

    background: var(--ivory);

    color: var(--text);

    font-family: 'Inter', Arial, sans-serif;
}

.customer-page h1,
.customer-page h2,
.customer-page h3,
.customer-page h4 {
    font-family: 'Cormorant Garamond', Georgia, serif;
}

.site-header {
    max-width: 1200px;

    margin: 0 auto 50px;
}

.header-inner {
    display: flex;

    align-items: center;
    justify-content: space-between;

    padding-bottom: 18px;

    border-bottom: 1px solid var(--line);
}

.brand {
    display: flex;
    align-items: baseline;

    gap: 6px;

    text-decoration: none;
    color: var(--forest);
}

.brand-mark {
    font-size: 24px;
    font-weight: 600;

    letter-spacing: .5px;
}

.brand-word {
    font-size: 14px;

    color: var(--sage);

    letter-spacing: .5px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 14px;
}

.cart-button {
    position: relative;

    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: none;
    background: transparent;

    color: var(--forest);

    cursor: pointer;
    transition: color .2s ease;
}

.cart-button:hover {
    color: var(--sage);
}

.cart-badge {
    position: absolute;
    top: -2px;
    right: -4px;

    min-width: 16px;
    height: 16px;

    padding: 0 4px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--forest);
    color: #ffffff;

    font-size: 9px;
    font-weight: 600;
    line-height: 1;
}

.cart-bounce {
    animation: cartBounce .45s ease;
}

@keyframes cartBounce {
    0% { transform: scale(1); }
    35% { transform: scale(1.2) rotate(-6deg); }
    65% { transform: scale(.94) rotate(4deg); }
    100% { transform: scale(1); }
}

.flying-product-image {
    position: fixed;
    z-index: 99999;

    object-fit: cover;
    pointer-events: none;

    border-radius: 50%;

    box-shadow: 0 8px 22px rgba(35, 66, 58, .18);

    transition:
        left 1.2s cubic-bezier(.22, .61, .36, 1),
        top 1.2s cubic-bezier(.22, .61, .36, 1),
        width 1.2s ease,
        height 1.2s ease,
        opacity 1.2s ease,
        transform 1.2s ease;
}

.profile-button {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid var(--line);
    border-radius: 50%;

    background: #ffffff;
    color: var(--forest);

    cursor: pointer;
    transition: border-color .2s ease, background .2s ease;
}

.profile-button:hover {
    border-color: var(--sage);
    background: var(--sage-soft);
}

.profile-avatar {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--sage-soft);
    color: var(--forest);

    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 15px;
    font-weight: 600;
}

.intro {
    max-width: 620px;

    margin: 0 auto 42px;
}

.intro h2 {
    margin: 0 0 12px;

    font-size: 34px;
    font-weight: 500;
    line-height: 1.25;

    color: var(--forest-deep);
}

.intro p {
    margin: 0;

    max-width: 46ch;

    font-size: 13.5px;
    line-height: 1.75;

    color: var(--text-muted);
}

.category-nav {
    max-width: 1200px;

    margin: 0 auto 40px;

    overflow-x: auto;

    scrollbar-width: none;
}

.category-nav::-webkit-scrollbar {
    display: none;
}

.category-list {
    display: flex;

    gap: 26px;

    width: max-content;

    border-bottom: 1px solid var(--line);
}

.category-tab {
    position: relative;

    padding: 0 2px 12px;

    border: none;
    background: transparent;

    color: var(--text-muted);

    font-size: 13px;
    font-family: 'Inter', Arial, sans-serif;

    white-space: nowrap;

    cursor: pointer;
    transition: color .2s ease;
}

.category-tab:hover {
    color: var(--forest);
}

.category-tab::after {
    content: '';

    position: absolute;
    left: 0;
    bottom: -1px;

    width: 100%;
    height: 2px;

    background: var(--forest);

    transform: scaleX(0);
    transform-origin: left;

    transition: transform .25s ease;
}

.category-tab.active {
    color: var(--forest-deep);
    font-weight: 500;
}

.category-tab.active::after {
    transform: scaleX(1);
}

.collection-section {
    max-width: 1200px;

    margin: 0 auto 60px;
}

.collection-top {
    display: flex;

    align-items: baseline;
    justify-content: space-between;

    margin-bottom: 22px;
}

.collection-top h3 {
    margin: 0;

    font-size: 24px;
    font-weight: 500;

    color: var(--forest-deep);
}

.product-count {
    font-size: 11px;

    color: var(--text-muted);
}

.product-grid {
    display: grid;

    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));

    gap: 24px 20px;
}

.product-card {
    display: flex;
    flex-direction: column;
}

.product-visual {
    position: relative;

    height: 230px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 6px;

    background: var(--sage-soft);
}

.product-visual img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
    object-position: center;

    transition: transform .5s ease;
}

.product-card:hover .product-visual img {
    transform: scale(1.045);
}

.photo-placeholder {
    font-size: 11px;

    color: var(--text-muted);
}

.product-tag {
    position: absolute;
    left: 10px;
    top: 10px;

    padding: 4px 9px;

    border-radius: 3px;

    background: rgba(250, 249, 244, .92);

    color: var(--forest);

    font-size: 9.5px;
    letter-spacing: .3px;
}

.quick-cart {
    position: absolute;
    right: 10px;
    bottom: 10px;

    width: 32px;
    height: 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: rgba(250, 249, 244, .92);
    color: var(--forest);

    cursor: pointer;

    opacity: 0;
    transform: translateY(4px);

    transition: opacity .2s ease, transform .2s ease, background .2s ease;
}

.product-card:hover .quick-cart {
    opacity: 1;
    transform: translateY(0);
}

.quick-cart:hover {
    background: var(--forest);
    color: #ffffff;
}

.product-info {
    padding-top: 14px;
}

.product-info h4 {
    margin: 0 0 8px;

    font-size: 17px;
    font-weight: 500;
    line-height: 1.3;

    color: var(--text);
}

.product-meta {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 12px;
}

.product-price {
    font-family: 'Inter', Arial, sans-serif;
    font-size: 13px;
    font-weight: 600;

    color: var(--forest);
}

.product-stock {
    font-size: 10.5px;

    color: var(--text-muted);
}

.order-button {
    width: 100%;
    height: 38px;

    border: 1px solid var(--forest);
    border-radius: 5px;

    background: transparent;
    color: var(--forest);

    font-family: 'Inter', Arial, sans-serif;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: .2px;

    cursor: pointer;

    transition: background .2s ease, color .2s ease;
}

.order-button:hover:not(:disabled) {
    background: var(--forest);
    color: #ffffff;
}

.order-button:disabled {
    border-color: var(--line);

    color: var(--text-muted);

    cursor: not-allowed;
}

.empty-product {
    padding: 60px;

    text-align: center;

    border: 1px solid var(--line);
    border-radius: 8px;

    background: #ffffff;

    color: var(--text-muted);

    font-size: 12px;
}

.shelf-section {
    max-width: 1200px;

    margin: 0 auto 50px;
}

.shelf-header {
    display: flex;

    align-items: baseline;
    justify-content: space-between;

    margin-bottom: 20px;
}

.shelf-header h3 {
    margin: 0;

    font-size: 22px;
    font-weight: 500;

    color: var(--forest-deep);
}

.scroll-hint {
    font-size: 10.5px;

    color: var(--text-muted);
}

.horizontal-products {
    display: flex;

    gap: 20px;

    overflow-x: auto;

    padding: 2px 2px 14px;

    scroll-behavior: smooth;

    scrollbar-width: thin;
    scrollbar-color: var(--sage) transparent;
}

.horizontal-products::-webkit-scrollbar {
    height: 4px;
}

.horizontal-products::-webkit-scrollbar-track {
    background: transparent;
}

.horizontal-products::-webkit-scrollbar-thumb {
    background: var(--sage);
    border-radius: 10px;
}

.horizontal-card {
    flex: 0 0 210px;
    width: 210px;
    min-width: 210px;
}

.login-modal-overlay {
    --forest: #23423a;
    --forest-deep: #1a332c;
    --sage: #7d9d8a;
    --sage-soft: #e9f0e8;
    --ivory: #faf9f4;
    --line: #e1e6dd;
    --text: #2c3a35;
    --text-muted: #75847b;

    position: fixed;
    inset: 0;
    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 20px;

    background: rgba(26, 51, 44, .38);

    font-family: 'Inter', Arial, sans-serif;
}

.login-modal-overlay h3 {
    font-family: 'Cormorant Garamond', Georgia, serif;
}

.login-modal {
    width: 100%;
    max-width: 340px;

    padding: 32px 28px;

    background: #ffffff;
    border-radius: 10px;

    text-align: center;

    animation: modalIn .18s ease;
}

.login-modal-icon {
    width: 46px;
    height: 46px;

    margin: 0 auto 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--sage-soft);
    color: var(--forest);
}

.login-modal h3 {
    margin: 0 0 10px;

    font-size: 21px;
    font-weight: 500;

    color: var(--forest-deep);
}

.login-modal p {
    margin: 0 auto 24px;

    max-width: 260px;

    font-size: 11.5px;
    line-height: 1.7;

    color: var(--text-muted);
}

.login-modal-actions {
    display: flex;
    gap: 10px;
}

.login-modal-actions button {
    flex: 1;

    padding: 11px;

    border-radius: 5px;

    font-family: 'Inter', Arial, sans-serif;
    font-size: 11px;
    font-weight: 500;

    cursor: pointer;

    transition: background .2s ease, border-color .2s ease;
}

.modal-cancel {
    border: 1px solid var(--line);
    background: #ffffff;
    color: var(--text-muted);
}

.modal-cancel:hover {
    background: var(--ivory);
}

.modal-login {
    border: 1px solid var(--forest);
    background: var(--forest);
    color: #ffffff;
}

.modal-login:hover {
    background: var(--forest-deep);
    border-color: var(--forest-deep);
}

.size-modal-overlay {
    --forest: #23423a;
    --forest-deep: #1a332c;
    --sage: #7d9d8a;
    --sage-soft: #e9f0e8;
    --ivory: #faf9f4;
    --line: #e1e6dd;
    --text: #2c3a35;
    --text-muted: #75847b;

    position: fixed;
    inset: 0;
    z-index: 10000;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 20px;

    background: rgba(26, 51, 44, .42);

    font-family: 'Inter', Arial, sans-serif;
}

.size-modal-overlay h3,
.size-modal-overlay h4 {
    font-family: 'Cormorant Garamond', Georgia, serif;
}

.size-modal {
    position: relative;

    width: min(500px, 100%);
    max-height: 90vh;

    overflow-y: auto;

    padding: 28px;

    background: #ffffff;
    border-radius: 10px;

    animation: sizeModalIn .2s ease;
}

.size-modal-close {
    position: absolute;

    top: 14px;
    right: 14px;

    width: 30px;
    height: 30px;

    border: none;
    border-radius: 50%;

    background: var(--ivory);
    color: var(--text-muted);

    font-size: 20px;
    line-height: 1;

    cursor: pointer;

    transition: background .2s ease, color .2s ease;
}

.size-modal-close:hover {
    background: var(--sage-soft);
    color: var(--forest);
}

.size-modal-product {
    display: flex;
    align-items: center;

    gap: 16px;

    margin-bottom: 26px;
    padding-bottom: 22px;
    padding-right: 30px;

    border-bottom: 1px solid var(--line);
}

.size-modal-image {
    width: 86px;
    height: 86px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 6px;

    background: var(--sage-soft);
}

.size-modal-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.size-modal-image span {
    color: var(--text-muted);
    font-size: 9px;
}

.size-modal-name {
    display: block;

    margin-bottom: 5px;

    font-size: 13px;

    color: var(--text);
}

.size-modal-info h3 {
    margin: 0 0 6px;

    font-family: 'Inter', Arial, sans-serif;
    font-size: 20px;
    font-weight: 600;

    color: var(--forest);
}

.size-modal-info p {
    margin: 0;

    color: var(--text-muted);
    font-size: 10.5px;
}

.size-section {
    margin-bottom: 22px;
}

.size-section h4,
.quantity-section h4 {
    margin: 0 0 12px;

    font-family: 'Inter', Arial, sans-serif;
    color: var(--text);

    font-size: 12px;
    font-weight: 600;
}

.size-list {
    display: grid;

    grid-template-columns: repeat(6, minmax(0, 1fr));

    gap: 8px;

    max-height: 190px;

    overflow-y: auto;

    padding-right: 4px;
}

.size-button {
    min-height: 36px;

    border: 1px solid var(--line);
    border-radius: 5px;

    background: #ffffff;

    color: var(--text-muted);

    font-family: 'Inter', Arial, sans-serif;
    font-size: 10px;

    cursor: pointer;

    transition: border-color .15s ease, background .15s ease, color .15s ease;
}

.size-button:hover {
    border-color: var(--sage);
}

.size-button.active {
    border-color: var(--forest);
    background: var(--forest);
    color: #ffffff;
    font-weight: 600;
}

.quantity-section {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin-bottom: 22px;
    padding: 16px 0;

    border-bottom: 1px solid var(--line);
}

.modal-quantity {
    display: flex;
    align-items: center;

    overflow: hidden;

    border: 1px solid var(--line);
    border-radius: 6px;
}

.modal-quantity button {
    width: 36px;
    height: 34px;

    border: none;

    background: transparent;
    color: var(--forest);

    font-size: 17px;

    cursor: pointer;

    transition: background .15s ease;
}

.modal-quantity button:hover:not(:disabled) {
    background: var(--sage-soft);
}

.modal-quantity button:disabled {
    color: var(--line);
    cursor: not-allowed;
}

.modal-quantity span {
    width: 38px;

    text-align: center;

    font-family: 'Inter', Arial, sans-serif;
    color: var(--text);

    font-size: 11px;
    font-weight: 600;
}

.size-modal-submit {
    width: 100%;
    height: 46px;

    border: none;
    border-radius: 6px;

    background: var(--forest);
    color: #ffffff;

    font-family: 'Inter', Arial, sans-serif;
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: .2px;

    cursor: pointer;

    transition: background .2s ease;
}

.size-modal-submit:hover {
    background: var(--forest-deep);
}

@keyframes sizeModalIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes modalIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.category-tab:focus-visible,
.order-button:focus-visible,
.quick-cart:focus-visible,
.cart-button:focus-visible,
.profile-button:focus-visible,
.size-button:focus-visible,
.modal-login:focus-visible,
.modal-cancel:focus-visible,
.size-modal-submit:focus-visible {
    outline: 2px solid var(--sage);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .product-visual img,
    .quick-cart,
    .cart-bounce,
    .login-modal,
    .size-modal {
        animation: none !important;
        transition: none !important;
    }
}


@media (max-width: 1050px) {

    .product-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

}

@media (max-width: 750px) {

    .customer-page {
        padding: 28px 20px 60px;
    }

    .intro h2 {
        font-size: 28px;
    }

    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 14px;
    }

}

@media (max-width: 520px) {

    .site-header {
        margin-bottom: 34px;
    }

    .brand-mark {
        font-size: 20px;
    }

    .intro h2 {
        font-size: 24px;
    }

    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 10px;
    }

    .product-visual {
        height: 170px;
    }

    .horizontal-card {
        flex: 0 0 165px;
        width: 165px;
        min-width: 165px;
    }

    .size-modal {
        padding: 22px;
    }

    .size-modal-product {
        gap: 12px;
    }

    .size-modal-image {
        width: 74px;
        height: 74px;
    }

    .size-list {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

}

</style>