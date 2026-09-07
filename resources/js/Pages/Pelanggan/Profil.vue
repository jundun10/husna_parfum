<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    LogOut,
    ShoppingBag,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    authUser: {
        type: Object,
        required: true,
    },

    pesanans: {
        type: Array,
        default: () => [],
    },
});
const tabAktif = ref('Semua');
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
const pesananTampil = computed(() => {

    if (tabAktif.value === 'Semua') {
        return props.pesanans;
    }

    const mappingStatus = {
    Dikemas: ['menunggu', 'diproses'],
    Diantar: ['dikirim'],
    'Akan Diterima': ['diterima'],
    Selesai: ['selesai'],
    Dibatalkan: ['dibatalkan'],
};

    const statusYangDicari = mappingStatus[tabAktif.value] ?? [];

    return props.pesanans.filter(
        pesanan => statusYangDicari.includes(pesanan.status)
    );
});
const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Profil Saya" />

    <div class="profile-page">

        <header class="profile-header">

            <Link
                href="/Pelanggan"
                class="back-button"
            >
                <ArrowLeft :size="20" :stroke-width="1.7" />
            </Link>

            <div class="header-title">
                <h1>Profil Saya</h1>

            </div>

            <button
                type="button"
                class="logout-button"
                @click="logout"
                :disabled="logoutForm.processing"
            >
                <LogOut :size="15" :stroke-width="1.7" />

                <span>
                    {{
                        logoutForm.processing
                            ? 'Keluar...'
                            : 'Logout'
                    }}
                </span>
            </button>

        </header>

        <main class="profile-content">

            <section class="user-summary">

                <div class="user-avatar">
                    {{ props.authUser.name?.charAt(0).toUpperCase() }}
                </div>

                <div class="user-info">

                    <h2>
                        {{ props.authUser.name }}
                    </h2>

                    <p>
                        {{ props.authUser.email }}
                    </p>

                </div>

            </section>

            <div class="section-divider"></div>

            <section class="orders-section">

                <div class="orders-section-heading">
                    <h3>Riwayat Pesanan</h3>
                </div>

                <nav class="order-tabs">

                    <button
                        v-for="tab in [
                            'Semua',
                            'Dikemas',
                            'Diantar',
                            'Akan Diterima',
                            'Selesai',
                            'Dibatalkan'
                        ]"
                        :key="tab"
                        type="button"
                        class="order-tab"
                        :class="{ active: tabAktif === tab }"
                        @click="tabAktif = tab"
                    >
                        {{ tab }}
                    </button>

                </nav>

                <section
                    v-if="pesananTampil.length === 0"
                    class="empty-orders"
                >

                    <div class="empty-icon">
                        <ShoppingBag
                            :size="26"
                            :stroke-width="1.4"
                        />
                    </div>

                    <h2>
                        {{
                            tabAktif === 'Semua'
                                ? 'Belum ada pesanan'
                                : `Belum ada pesanan ${tabAktif.toLowerCase()}`
                        }}
                    </h2>

                    <p>
                        {{
                            tabAktif === 'Semua'
                                ? 'Pesanan kamu akan muncul di sini setelah melakukan checkout.'
                                : 'Belum ada pesanan dengan status tersebut.'
                        }}
                    </p>

                    <Link
                        v-if="tabAktif === 'Semua'"
                        href="/Pelanggan"
                        class="shop-button"
                    >
                        Mulai Belanja
                    </Link>

                </section>

                <section
                    v-else
                    class="orders-list"
                >

                    <article
                        v-for="pesanan in pesananTampil"
                        :key="pesanan.id"
                        class="customer-order-card"
                    >

                        <div class="customer-order-top">

                            <div>
                                <strong>
                                    {{
                                        new Date(
                                            pesanan.created_at
                                        ).toLocaleDateString('id-ID')
                                    }}
                                </strong>
                            </div>

                            <span
                                class="customer-status"
                                :class="`status-${pesanan.status}`"
                            >
                                {{ pesanan.status }}
                            </span>

                        </div>


                        <div class="customer-order-items">

                            <div
                                v-for="item in pesanan.items"
                                :key="item.id"
                                class="customer-order-item"
                            >

                                <div class="customer-product-image">

                                    <img
                                        v-if="item.parfum?.foto"
                                        :src="`/storage/${item.parfum.foto}`"
                                        :alt="item.parfum.nama"
                                    >

                                </div>

                                <div class="customer-product-info">

                                    <strong>
                                        {{ item.parfum?.nama }}
                                    </strong>

                                    <span>
                                        {{ item.jumlah }} produk ×
                                        {{
                                            formatRupiah(item.harga)
                                        }}
                                    </span>

                                </div>

                                <strong>
                                    {{
                                        formatRupiah(item.subtotal)
                                    }}
                                </strong>

                            </div>

                        </div>


                        <div class="customer-order-bottom">

                            <span>
                                Total Pesanan
                            </span>

                            <strong>
                                {{
                                    formatRupiah(
                                        pesanan.total_harga
                                    )
                                }}
                            </strong>

                        </div>

                    </article>

                </section>

            </section>

        </main>

    </div>
</template>

<style scoped>

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap');

* {
    box-sizing: border-box;
}

.profile-page {
    --forest: #23423a;
    --forest-deep: #1a332c;
    --sage: #7d9d8a;
    --sage-soft: #e9f0e8;
    --ivory: #faf9f4;
    --line: #e1e6dd;
    --text: #2c3a35;
    --text-muted: #75847b;

    min-height: 100vh;

    padding-bottom: 60px;

    background: var(--ivory);

    color: var(--text);

    font-family: 'Inter', Arial, sans-serif;
}

.profile-page h1,
.profile-page h2,
.profile-page h3 {
    font-family: 'Cormorant Garamond', Georgia, serif;
}

.profile-header {
    position: sticky;
    top: 0;
    z-index: 20;

    height: 68px;

    display: grid;
    grid-template-columns: 40px 1fr auto;

    align-items: center;
    gap: 14px;

    padding: 0 6%;

    background: rgba(250, 249, 244, .96);

    border-bottom: 1px solid var(--line);

    backdrop-filter: blur(8px);
}

.back-button {
    width: 36px;
    height: 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: transparent;

    color: var(--forest);

    cursor: pointer;
    text-decoration: none;

    transition: background .2s ease;
}

.back-button:hover {
    background: var(--sage-soft);
}

.header-title h1 {
    margin: 0;

    font-size: 20px;
    font-weight: 500;

    color: var(--forest-deep);
}

.logout-button {
    display: flex;
    align-items: center;
    gap: 7px;

    padding: 8px 14px;

    border: 1px solid var(--line);
    border-radius: 6px;

    background: #ffffff;

    color: var(--text-muted);

    font-family: 'Inter', Arial, sans-serif;
    font-size: 10.5px;
    font-weight: 500;

    cursor: pointer;
    transition: border-color .2s ease, color .2s ease;
}

.logout-button:hover {
    border-color: #d8b7b3;
    color: #b5675f;
}

.logout-button:disabled {
    opacity: .6;
    cursor: not-allowed;
}

.profile-content {
    width: min(720px, calc(100% - 40px));

    margin: 36px auto 0;
}

.user-summary {
    display: flex;
    align-items: center;
    gap: 16px;

    margin-bottom: 28px;
}

.user-avatar {
    width: 58px;
    height: 58px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--sage-soft);
    color: var(--forest);

    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 24px;
    font-weight: 600;
}

.user-info h2 {
    margin: 0 0 4px;

    font-size: 21px;
    font-weight: 500;

    color: var(--forest-deep);
}

.user-info p {
    margin: 0;

    color: var(--text-muted);

    font-size: 11px;
}

.section-divider {
    height: 1px;

    margin-bottom: 28px;

    background: var(--line);
}

.orders-section-heading {
    margin-bottom: 16px;
}

.orders-section-heading h3 {
    margin: 0;

    font-size: 19px;
    font-weight: 500;

    color: var(--forest-deep);
}

.order-tabs {
    display: flex;

    gap: 22px;

    margin-bottom: 24px;
    padding-bottom: 2px;

    overflow-x: auto;

    border-bottom: 1px solid var(--line);

    scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
    display: none;
}

.order-tab {
    position: relative;

    padding: 0 1px 11px;

    border: none;
    background: transparent;

    color: var(--text-muted);

    font-family: 'Inter', Arial, sans-serif;
    font-size: 12px;

    white-space: nowrap;

    cursor: pointer;
    transition: color .2s ease;
}

.order-tab:hover {
    color: var(--forest);
}

.order-tab::after {
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

.order-tab.active {
    color: var(--forest-deep);
    font-weight: 500;
}

.order-tab.active::after {
    transform: scaleX(1);
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.customer-order-card {
    background: #ffffff;

    border: 1px solid var(--line);
    border-radius: 8px;

    overflow: hidden;
}

.customer-order-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 14px 18px;

    border-bottom: 1px solid var(--line);
}

.customer-order-top strong {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 500;
}

.customer-status {
    padding: 5px 11px;

    border-radius: 4px;

    font-size: 9px;
    font-weight: 500;
    text-transform: capitalize;
}

.status-menunggu {
    background: #fbf3e4;
    color: #a8813f;
}

.status-diproses {
    background: #edf2fb;
    color: #6081b8;
}

.status-dikirim {
    background: #f1ecfb;
    color: #7c65ac;
}

.status-diterima {
    background: var(--sage-soft);
    color: var(--forest);
}

.status-selesai {
    background: #e9f3ea;
    color: #558b5e;
}

.status-dibatalkan {
    background: #fbeceb;
    color: #b5675f;
}

.customer-order-items {
    padding: 4px 18px;
}

.customer-order-item {
    display: flex;
    align-items: center;
    gap: 13px;

    padding: 12px 0;

    border-bottom: 1px solid var(--line);
}

.customer-order-item:last-child {
    border-bottom: none;
}

.customer-product-image {
    width: 52px;
    height: 52px;

    flex-shrink: 0;

    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: var(--sage-soft);
}

.customer-product-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.customer-product-info {
    flex: 1;

    min-width: 0;
}

.customer-product-info strong {
    display: block;

    margin-bottom: 4px;

    color: var(--text);

    font-size: 12px;
    font-weight: 500;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.customer-product-info span {
    color: var(--text-muted);

    font-size: 10px;
}

.customer-order-item > strong {
    flex-shrink: 0;

    color: var(--forest);

    font-size: 11px;
    font-weight: 600;
}

.customer-order-bottom {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    gap: 12px;

    padding: 13px 18px;

    background: var(--ivory);

    border-top: 1px solid var(--line);
}

.customer-order-bottom span {
    color: var(--text-muted);

    font-size: 9.5px;
}

.customer-order-bottom strong {
    color: var(--forest-deep);

    font-size: 14px;
    font-weight: 600;
}

.empty-orders {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    padding: 60px 25px;

    text-align: center;

    background: #ffffff;

    border: 1px solid var(--line);
    border-radius: 8px;
}

.empty-icon {
    width: 48px;
    height: 48px;

    margin-bottom: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--sage-soft);
    color: var(--forest);
}

.empty-orders h2 {
    margin: 0 0 7px;

    font-size: 19px;
    font-weight: 500;

    color: var(--forest-deep);
}

.empty-orders p {
    max-width: 320px;

    margin: 0 0 22px;

    color: var(--text-muted);

    font-size: 11px;
    line-height: 1.7;
}

.shop-button {
    padding: 11px 22px;

    border: 1px solid var(--forest);
    border-radius: 6px;

    background: var(--forest);

    color: #ffffff;

    text-decoration: none;

    font-size: 11px;
    font-weight: 500;

    transition: background .2s ease;
}

.shop-button:hover {
    background: var(--forest-deep);
}

.back-button:focus-visible,
.logout-button:focus-visible,
.order-tab:focus-visible,
.shop-button:focus-visible {
    outline: 2px solid var(--sage);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .back-button,
    .logout-button,
    .order-tab,
    .order-tab::after,
    .shop-button {
        transition: none !important;
    }
}

@media (max-width: 650px) {

    .profile-header {
        height: 60px;

        grid-template-columns: 34px 1fr auto;

        padding: 0 16px;
    }

    .header-title h1 {
        font-size: 17px;
    }

    .logout-button span {
        display: none;
    }

    .logout-button {
        padding: 8px;
    }

    .profile-content {
        width: calc(100% - 24px);

        margin-top: 26px;
    }

    .user-avatar {
        width: 50px;
        height: 50px;

        font-size: 20px;
    }

    .user-info h2 {
        font-size: 18px;
    }

    .order-tab {
        font-size: 11px;
    }

    .customer-order-top,
    .customer-order-bottom {
        padding: 12px 14px;
    }

    .customer-order-items {
        padding: 4px 14px;
    }

}

</style>