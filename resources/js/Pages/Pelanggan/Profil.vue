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
                <ArrowLeft :size="21" />
            </Link>

            <div class="header-title">
                <h1>Profil Saya</h1>

                <span>
                    Kelola akun dan pesanan kamu
                </span>
            </div>

            <button
                type="button"
                class="logout-button"
                @click="logout"
                :disabled="logoutForm.processing"
            >
                <LogOut :size="16" />

                {{
                    logoutForm.processing
                        ? 'Keluar...'
                        : 'Logout'
                }}
            </button>

        </header>


        <section class="user-card">

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

       <main class="profile-content">

        <section
            v-if="pesananTampil.length === 0"
            class="empty-orders"
        >

            <div class="empty-icon">
            <ShoppingBag
                :size="30"
                :stroke-width="1.5"
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

    </main>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.profile-page {
    min-height: 100vh;

    padding: 25px 6% 60px;

    background:
        linear-gradient(
            135deg,
            #f7faf9 0%,
            #eef5f3 100%
        );

    color: #304c4b;
}


.profile-header {
    width: min(1200px, 100%);

    margin: 0 auto 25px;

    display: grid;

    grid-template-columns: 45px 1fr auto;

    align-items: center;

    gap: 15px;
}

.back-button {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: transparent;

    color: #477c79;

    cursor: pointer;

    text-decoration: none;
}

.back-button:hover {
    background: #edf4f2;
}

.header-title h1 {
    margin: 0 0 3px;

    font-family: Georgia, serif;

    font-size: 28px;

    font-weight: normal;
}

.header-title span {
    color: #92a3a1;

    font-size: 10px;
}

.logout-button {
    display: flex;
    align-items: center;

    gap: 7px;

    padding: 9px 13px;

    border: 1px solid #f0dddd;

    border-radius: 8px;

    background: #ffffff;

    color: #b77777;

    font-size: 10px;

    cursor: pointer;
}

.logout-button:hover {
    background: #fff8f8;
}


.user-card {
    width: min(1200px, 100%);

    margin: 0 auto 20px;

    padding: 20px;

    display: flex;

    align-items: center;

    gap: 15px;

    background: #ffffff;

    border: 1px solid #dce7e5;

    border-radius: 14px;
}

.user-avatar {
    width: 56px;
    height: 56px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #eaf7f7;

    color: #6f9d9d;

    font-family: Georgia, serif;

    font-size: 22px;
}

.user-info h2 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 19px;

    font-weight: normal;
}

.user-info p {
    margin: 0;

    color: #96a6a4;

    font-size: 10px;
}

.order-tabs {
    width: min(1200px, 100%);

    margin: 0 auto 20px;

    display: flex;

    overflow-x: auto;

    border: 1px solid #dce7e5;

    border-radius: 11px;

    background: rgba(255, 255, 255, .8);

    scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
    display: none;
}

.order-tab {
    flex: 1;

    min-width: 125px;

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

.order-tab:last-child {
    border-right: none;
}

.order-tab:hover {
    background: #edf5f3;
}

.order-tab.active {
    background: #416f6d;

    color: white;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.customer-order-card {
    background: #ffffff;

    border: 1px solid #dce7e5;
    border-radius: 14px;

    overflow: hidden;

    box-shadow:
        0 8px 25px
        rgba(70, 95, 90, .04);
}

.customer-order-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 16px 18px;

    border-bottom: 1px solid #edf2f1;
}

.customer-order-top strong {
    color: #61716f;

    font-size: 9px;

    font-weight: normal;
}

.customer-status {
    padding: 7px 11px;

    border-radius: 20px;

    font-size: 8px;

    text-transform: capitalize;
}

.status-menunggu {
    background: #fff7e8;
    color: #b58a45;
}

.status-diproses {
    background: #edf4ff;
    color: #6488bc;
}

.status-dikirim {
    background: #f3edff;
    color: #8068b1;
}

.status-selesai {
    background: #eaf7ec;
    color: #5c9865;
}

.status-dibatalkan {
    background: #fff0f0;
    color: #bc7777;
}

.customer-order-items {
    padding: 8px 18px;
}

.customer-order-item {
    display: flex;

    align-items: center;

    gap: 12px;

    padding: 10px 0;

    border-bottom: 1px solid #f0f4f3;
}

.customer-order-item:last-child {
    border-bottom: none;
}

.customer-product-image {
    width: 55px;
    height: 55px;

    flex-shrink: 0;

    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #f3f7f6;
}

.customer-product-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.customer-product-info {
    flex: 1;
}

.customer-product-info strong {
    display: block;

    margin-bottom: 4px;

    color: #526765;

    font-size: 10px;
}

.customer-product-info span {
    color: #97a6a4;

    font-size: 9px;
}

.customer-order-item > strong {
    color: #5d8986;

    font-size: 10px;
}

.customer-order-bottom {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    gap: 12px;

    padding: 14px 18px;

    background: #fafcfb;

    border-top: 1px solid #edf2f1;
}

.customer-order-bottom span {
    color: #97a6a4;

    font-size: 9px;
}

.customer-order-bottom strong {
    color: #477c79;

    font-size: 13px;
}

.profile-content {
    width: min(1200px, 100%);

    margin: 0 auto;
}

.empty-orders {
    min-height: 350px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    padding: 40px;

    text-align: center;

    background: #ffffff;

    border: 1px solid #dce7e5;

    border-radius: 14px;

    box-shadow:
        0 10px 30px
        rgba(70, 95, 90, .05);
}

.empty-icon {
    width: 55px;
    height: 55px;

    margin-bottom: 15px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 14px;

    background: #edf5f3;

    font-size: 24px;
}

.empty-orders h2 {
    margin: 0 0 7px;

    font-family: Georgia, serif;

    font-size: 20px;

    font-weight: normal;
}

.empty-orders p {
    max-width: 350px;

    margin: 0 0 20px;

    color: #99a8a6;

    font-size: 10px;

    line-height: 1.7;
}

.shop-button {
    padding: 10px 18px;

    border-radius: 8px;

    background: #477c79;

    color: white;

    text-decoration: none;

    font-size: 10px;
}


/* MOBILE */

@media (max-width: 650px) {

    .profile-page {
        padding: 18px 12px 40px;
    }

    .profile-header {
        grid-template-columns: 40px 1fr auto;

        gap: 8px;
    }

    .header-title h1 {
        font-size: 22px;
    }

    .logout-button {
        padding: 8px 10px;
    }

    .logout-button svg {
        display: none;
    }

    .user-card {
        padding: 15px;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
    }

    .order-tab {
        min-width: 110px;
    }
}
</style>