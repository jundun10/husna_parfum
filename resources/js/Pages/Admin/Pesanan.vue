<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ShoppingBag,
    ChevronDown,
    ChevronUp,
    Package,
    MapPin,
    User,
    Mail
} from 'lucide-vue-next';

const props = defineProps({
    pesanans: {
        type: Array,
        default: () => [],
    },
    authUser: {
        type: Object,
        default: null,
    },
});

const logoUrl = '/images/logo.jpg';

const sidebarOpen = ref(false);
const openOrders = ref([]);

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const toggleOrder = (id) => {
    if (openOrders.value.includes(id)) {
        openOrders.value = openOrders.value.filter(
            orderId => orderId !== id
        );
    } else {
        openOrders.value.push(id);
    }
};

const isOpen = (id) => {
    return openOrders.value.includes(id);
};

const statusForm = (pesanan) => {
    return useForm({
        status: pesanan.status,
    });
};

const updateStatus = (pesanan) => {
    const form = statusForm(pesanan);

    form.put(`/admin/pesanan/${pesanan.id}/status`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Pesanan Admin" />

    <div class="admin-page">

        <!-- OVERLAY -->
        <div
            v-if="sidebarOpen"
            class="overlay"
            @click="sidebarOpen = false"
        ></div>


        <!-- SIDEBAR -->
        <aside
            class="sidebar"
            :class="{ 'sidebar-open': sidebarOpen }"
        >

            <div class="sidebar-header">

                <div class="brand">
                    <img
                        :src="logoUrl"
                        alt="Lamore Perfumes"
                    >

                    <div>
                        <h2>Lamore</h2>
                    </div>
                </div>

                <button
                    type="button"
                    class="close-button"
                    @click="sidebarOpen = false"
                >
                    ×
                </button>

            </div>


            <!-- ADMIN INFO -->
            <div class="admin-info">

                <div class="admin-avatar">
                    {{ props.authUser?.name?.charAt(0).toUpperCase() }}
                </div>

                <div class="admin-details">

                    <strong>
                        {{ props.authUser?.name }}
                    </strong>

                    <span>
                        Admin
                    </span>

                    <small>
                        {{ props.authUser?.email }}
                    </small>

                </div>

            </div>


            <!-- MENU -->
            <nav class="sidebar-menu">

                <Link
                    href="/admin/dashboard"
                    class="menu-item"
                >
                    <span>Dashboard</span>
                </Link>

                <Link
                    href="/admin/pesanan"
                    class="menu-item active"
                >
                    <span>Pesanan</span>
                </Link>

                <Link
                    href="/admin/stok"
                    class="menu-item"
                >
                    <span>Kelola Stok</span>
                </Link>

                <Link
                    href="/admin/laporan"
                    class="menu-item"
                >
                    <span>Laporan</span>
                </Link>

                <Link
                    href="/admin/pengaturan"
                    class="menu-item"
                >
                    <span>Pengaturan</span>
                </Link>

            </nav>


            <!-- LOGOUT -->
            <div class="sidebar-footer">

                <button
                    type="button"
                    class="logout-button"
                    @click="logout"
                    :disabled="logoutForm.processing"
                >
                    <span>
                        {{
                            logoutForm.processing
                                ? 'Keluar...'
                                : 'Logout'
                        }}
                    </span>
                </button>

            </div>

        </aside>


        <!-- MAIN -->
        <main class="main-content">

            <!-- TOPBAR -->
            <header class="topbar">

                <div class="topbar-left">

                    <button
                        type="button"
                        class="toggle-button"
                        @click="sidebarOpen = true"
                        aria-label="Buka sidebar"
                    >
                        ☰
                    </button>

                    <div>
                        <h1>Pesanan</h1>

                        <p>
                            Kelola pesanan pelanggan Lamore Perfumes.
                        </p>
                    </div>

                </div>

            </header>


            <!-- PESANAN -->
            <section class="order-section">

                <div class="section-heading">

                    <div>
                        <h2>Daftar Pesanan</h2>

                        <p>
                            Semua pesanan yang masuk dari pelanggan.
                        </p>
                    </div>

                    <div class="order-count">
                        {{ props.pesanans.length }} Pesanan
                    </div>

                </div>


                <!-- EMPTY -->
                <div
                    v-if="props.pesanans.length === 0"
                    class="empty-orders"
                >
                    <div class="empty-orders-icon">
                        <ShoppingBag :size="30" />
                    </div>

                    <h3>
                        Belum ada pesanan
                    </h3>

                    <p>
                        Pesanan pelanggan akan muncul di sini
                        setelah checkout.
                    </p>
                </div>


                <!-- LIST -->
                <div
                    v-else
                    class="order-list"
                >

                    <article
                        v-for="pesanan in props.pesanans"
                        :key="pesanan.id"
                        class="order-card"
                    >

                        <!-- ORDER HEADER -->
                        <div class="order-top">

                            <div class="order-number">

                                <div class="order-icon">
                                    <ShoppingBag :size="18" />
                                </div>

                                <div>
                                    <strong>
                                        Pesanan #{{ pesanan.id }}
                                    </strong>

                                    <span>
                                        {{
                                            new Date(
                                                pesanan.created_at
                                            ).toLocaleString('id-ID')
                                        }}
                                    </span>
                                </div>

                            </div>


                            <span
                                class="status-badge"
                                :class="`status-${pesanan.status}`"
                            >
                                {{ pesanan.status }}
                            </span>

                        </div>


                        <!-- CUSTOMER -->
                        <div class="customer-row">

                            <div class="customer-info">

                                <User :size="16" />

                                <div>
                                    <strong>
                                        {{ pesanan.user?.name }}
                                    </strong>

                                    <span>
                                        {{ pesanan.user?.email }}
                                    </span>
                                </div>

                            </div>


                            <div class="customer-total">
                                <span>Total</span>

                                <strong>
                                    {{
                                        formatRupiah(
                                            pesanan.total_harga
                                        )
                                    }}
                                </strong>
                            </div>

                        </div>


                        <!-- ACTION -->
                        <div class="order-actions">

                            <button
                                type="button"
                                class="detail-button"
                                @click="toggleOrder(pesanan.id)"
                            >
                                {{
                                    isOpen(pesanan.id)
                                        ? 'Tutup detail'
                                        : 'Lihat detail'
                                }}

                                <ChevronUp
                                    v-if="isOpen(pesanan.id)"
                                    :size="15"
                                />

                                <ChevronDown
                                    v-else
                                    :size="15"
                                />
                            </button>


                            <!-- STATUS -->
                            <select
                                class="status-select"
                                :value="pesanan.status"
                                @change="
                                    (event) => {
                                        const form = statusForm(pesanan);

                                        form.status = event.target.value;

                                        form.put(
                                            `/admin/pesanan/${pesanan.id}/status`,
                                            {
                                                preserveScroll: true,
                                            }
                                        );
                                    }
                                "
                            >
                                <option value="menunggu">
                                    Menunggu
                                </option>

                                <option value="diproses">
                                    Diproses
                                </option>

                                <option value="dikirim">
                                    Dikirim
                                </option>

                                <option value="diterima">
                                    Diterima
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                                <option value="dibatalkan">
                                    Dibatalkan
                                </option>
                            </select>

                        </div>


                        <!-- DETAIL -->
                        <div
                            v-if="isOpen(pesanan.id)"
                            class="order-detail"
                        >

                            <div class="detail-grid">

                                <!-- ALAMAT -->
                                <div class="detail-box">

                                    <div class="detail-title">
                                        <MapPin :size="16" />

                                        <span>
                                            Alamat Pengiriman
                                        </span>
                                    </div>

                                    <strong>
                                        {{ pesanan.alamat?.nama_penerima }}
                                    </strong>

                                    <span>
                                        {{ pesanan.alamat?.no_hp }}
                                    </span>

                                    <p>
                                        {{ pesanan.alamat?.alamat_lengkap }},
                                        {{ pesanan.alamat?.desa_nama }},
                                        {{ pesanan.alamat?.kecamatan_nama }},
                                        {{ pesanan.alamat?.kabupaten_nama }},
                                        {{ pesanan.alamat?.provinsi_nama }},
                                        {{ pesanan.alamat?.kode_pos }}
                                    </p>

                                    <a
                                        v-if="pesanan.alamat?.maps_url"
                                        :href="pesanan.alamat.maps_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="maps-button"
                                    >
                                        <MapPin :size="14" />
                                        Buka di Maps
                                    </a>
                                </div>

                                <div class="detail-box">

                                    <div class="detail-title">
                                        <Mail :size="16" />

                                        <span>
                                            Data Pelanggan
                                        </span>
                                    </div>

                                    <strong>
                                        {{ pesanan.user?.name }}
                                    </strong>

                                    <span>
                                        {{ pesanan.user?.email }}
                                    </span>

                                </div>

                            </div>


                            <div class="products-title">
                                <Package :size="17" />

                                <span>
                                    Produk Dipesan
                                </span>
                            </div>

                            <div class="product-list">

                                <div
                                    v-for="item in pesanan.items"
                                    :key="item.id"
                                    class="product-row"
                                >

                                    <div class="product-image">

                                        <img
                                            v-if="item.parfum?.foto"
                                            :src="`/storage/${item.parfum.foto}`"
                                            :alt="item.parfum.nama"
                                        >

                                        <Package
                                            v-else
                                            :size="20"
                                        />

                                    </div>

                                    <div class="product-info">

                                        <strong>
                                            {{ item.parfum?.nama }}
                                        </strong>

                                        <span>
                                            {{
                                                formatRupiah(item.harga)
                                            }}
                                            ×
                                            {{ item.jumlah }}
                                        </span>

                                    </div>

                                    <strong class="product-subtotal">
                                        {{
                                            formatRupiah(
                                                item.subtotal
                                            )
                                        }}
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </article>

                </div>

            </section>

        </main>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.admin-page {
    min-height: 100vh;

    background: #edf3f2;

    color: #526363;
}


/* SIDEBAR */

.sidebar {
    position: fixed;

    top: 0;
    left: 0;

    width: 275px;
    height: 100vh;

    padding: 25px 18px;

    display: flex;
    flex-direction: column;

    background: #ffffff;

    border-right: 1px solid #e2eeee;

    transform: translateX(-100%);

    transition: transform .3s ease;

    z-index: 1000;

    box-shadow: 8px 0 30px rgba(100, 130, 130, .08);

    overflow-y: auto;
}

.sidebar.sidebar-open {
    transform: translateX(0);
}

.sidebar-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 38px;
}

.brand {
    display: flex;

    align-items: center;

    gap: 11px;
}

.brand img {
    width: 43px;
    height: 43px;

    object-fit: contain;

    border-radius: 8px;

    mix-blend-mode: multiply;
}

.brand h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 18px;

    font-weight: normal;

    color: #6f9d9d;
}

.close-button {
    width: 34px;
    height: 34px;

    border: none;

    border-radius: 8px;

    background: transparent;

    color: #999;

    font-size: 25px;

    cursor: pointer;
}

.close-button:hover {
    background: #f3f9f9;
}


.admin-info {
    display: flex;

    align-items: center;

    width: 100%;

    margin: 5px 0 28px;

    padding: 0 8px;
}

.admin-avatar {
    width: 48px;
    height: 48px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-right: 13px;

    border-radius: 50%;

    background: #eaf7f7;

    color: #6f9d9d;

    font-family: Georgia, serif;

    font-size: 21px;
}

.admin-details {
    min-width: 0;
}

.admin-details strong {
    display: block;

    margin-bottom: 4px;

    color: #344747;

    font-size: 14px;

    font-weight: 600;
}

.admin-details span {
    display: block;

    margin-bottom: 4px;

    color: #6f9d9d;

    font-size: 11px;
}

.admin-details small {
    display: block;

    max-width: 190px;

    overflow: hidden;

    color: #9aa8a8;

    font-size: 9px;

    text-overflow: ellipsis;

    white-space: nowrap;
}

.sidebar-menu {
    display: flex;

    flex-direction: column;

    gap: 6px;
}

.menu-item {
    display: flex;

    align-items: center;

    gap: 13px;

    padding: 13px 14px;

    border-radius: 9px;

    color: #777;

    text-decoration: none;

    font-size: 12px;

    transition: all .2s ease;
}

.menu-item:hover {
    background: #f2f9f9;

    color: #6f9d9d;
}

.menu-item.active {
    background: #eaf7f7;

    color: #6f9d9d;

    font-weight: 500;
}

.sidebar-footer {
    margin-top: 25px;

    padding-top: 15px;
}

.logout-button {
    width: 100%;

    display: flex;

    align-items: center;

    padding: 13px 14px;

    border: 1px solid #f0dddd;

    border-radius: 9px;

    background: #ffffff;

    color: #b77777;

    font-size: 12px;

    cursor: pointer;
}

.logout-button:hover {
    background: #fff8f8;
}

.overlay {
    position: fixed;

    inset: 0;

    background: rgba(0, 0, 0, .22);

    z-index: 999;
}

.main-content {
    min-height: 100vh;

    padding: 30px 35px;
}

.topbar {
    display: flex;

    align-items: center;

    margin-bottom: 35px;
}

.topbar-left {
    display: flex;

    align-items: center;

    gap: 17px;
}

.toggle-button {
    width: 45px;
    height: 45px;

    border: 1px solid #dceeee;

    border-radius: 10px;

    background: #ffffff;

    color: #6f9d9d;

    font-size: 20px;

    cursor: pointer;

    box-shadow: 0 5px 18px rgba(100, 130, 130, .04);
}

.toggle-button:hover {
    background: #effafa;
}

.topbar h1 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 29px;

    font-weight: normal;

    color: #666;
}

.topbar p {
    margin: 0;

    font-size: 11px;

    color: #999;
}

.order-section {
    max-width: 1200px;

    margin: 0 auto;
}

.section-heading {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 20px;
}

.section-heading h2 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 21px;

    font-weight: normal;

    color: #666;
}

.section-heading p {
    margin: 0;

    font-size: 10px;

    color: #aaa;
}

.order-count {
    padding: 9px 13px;

    border-radius: 8px;

    background: #eaf7f7;

    color: #6f9d9d;

    font-size: 10px;
}


.empty-orders {
    min-height: 350px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    padding: 40px;

    background: #ffffff;

    border: 1px solid #e2eeee;

    border-radius: 14px;

    text-align: center;

    box-shadow: 0 8px 25px rgba(100, 130, 130, .045);
}

.empty-orders-icon {
    width: 58px;
    height: 58px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 14px;

    border-radius: 14px;

    background: #effafa;

    color: #6f9d9d;
}

.empty-orders h3 {
    margin: 0 0 7px;

    font-family: Georgia, serif;

    font-size: 20px;

    font-weight: normal;

    color: #666;
}

.empty-orders p {
    margin: 0;

    font-size: 10px;

    color: #aaa;
}


.order-list {
    display: flex;

    flex-direction: column;

    gap: 12px;
}

.order-card {
    background: #ffffff;

    border: 1px solid #e2eeee;

    border-radius: 14px;

    overflow: hidden;

    box-shadow: 0 8px 25px rgba(100, 130, 130, .045);
}

.order-top {
    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 18px 20px;

    border-bottom: 1px solid #edf2f2;
}

.order-number {
    display: flex;

    align-items: center;

    gap: 12px;
}

.order-icon {
    width: 40px;
    height: 40px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #effafa;

    color: #6f9d9d;
}

.order-number strong {
    display: block;

    margin-bottom: 4px;

    font-size: 12px;

    color: #4e6060;
}

.order-number span {
    font-size: 9px;

    color: #aaa;
}
.status-badge {
    padding: 7px 11px;

    border-radius: 20px;

    font-size: 9px;

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

.customer-row {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding: 16px 20px;
}

.customer-info {
    display: flex;

    align-items: center;

    gap: 10px;

    color: #6f9d9d;
}

.customer-info strong {
    display: block;

    margin-bottom: 3px;

    color: #5d6c6b;

    font-size: 11px;
}

.customer-info span {
    display: block;

    color: #aaa;

    font-size: 9px;
}

.customer-total {
    display: flex;

    align-items: flex-end;

    flex-direction: column;

    gap: 4px;
}

.customer-total span {
    color: #aaa;

    font-size: 9px;
}

.customer-total strong {
    color: #6f9d9d;

    font-size: 15px;
}

.order-actions {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 10px;

    padding: 0 20px 16px;
}

.detail-button {
    display: flex;

    align-items: center;

    gap: 6px;

    padding: 9px 12px;

    border: 1px solid #dceeee;

    border-radius: 8px;

    background: #ffffff;

    color: #6f9d9d;

    font-size: 9px;

    cursor: pointer;
}

.detail-button:hover {
    background: #f3fafa;
}

.status-select {
    padding: 9px 12px;

    border: 1px solid #dceeee;

    border-radius: 8px;

    background: #ffffff;

    color: #6f9d9d;

    font-size: 9px;

    outline: none;

    cursor: pointer;
}

.order-detail {
    padding: 20px;

    border-top: 1px solid #edf2f2;

    background: #fbfdfd;
}

.detail-grid {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 12px;

    margin-bottom: 20px;
}

.detail-box {
    padding: 15px;

    background: #ffffff;

    border: 1px solid #e7eeee;

    border-radius: 10px;
}

.detail-title {
    display: flex;

    align-items: center;

    gap: 7px;

    margin-bottom: 11px;

    color: #6f9d9d;

    font-size: 9px;
}

.detail-box strong {
    display: block;

    margin-bottom: 4px;

    color: #5b6d6b;

    font-size: 11px;
}

.detail-box span {
    display: block;

    margin-bottom: 6px;

    color: #8fa19f;

    font-size: 9px;
}

.detail-box p {
    margin: 0;

    color: #7b8c8a;

    font-size: 9px;

    line-height: 1.7;
}

.products-title {
    display: flex;

    align-items: center;

    gap: 7px;

    margin-bottom: 10px;

    color: #6f9d9d;

    font-size: 10px;
}

.product-list {
    display: flex;

    flex-direction: column;

    gap: 8px;
}

.product-row {
    display: flex;

    align-items: center;

    gap: 11px;

    padding: 10px;

    background: #ffffff;

    border: 1px solid #e7eeee;

    border-radius: 9px;
}

.product-image {
    width: 52px;
    height: 52px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 8px;

    background: #f1f6f5;

    color: #9aabaa;
}

.product-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.product-info {
    flex: 1;

    min-width: 0;
}

.product-info strong {
    display: block;

    margin-bottom: 4px;

    color: #566765;

    font-size: 10px;
}

.product-info span {
    color: #99a8a6;

    font-size: 9px;
}

.product-subtotal {
    color: #5f8986;

    font-size: 11px;
}

.maps-button {
    margin-top: 12px;

    display: inline-flex;
    align-items: center;

    gap: 6px;

    padding: 8px 11px;

    border: 1px solid #dce7e5;
    border-radius: 8px;

    background: #f7faf9;

    color: #568381;

    font-size: 9px;

    text-decoration: none;

    transition: .2s ease;
}

.maps-button:hover {
    background: #edf4f2;
}


@media (max-width: 800px) {

    .main-content {
        padding: 20px 15px;
    }

    .customer-row {
        align-items: flex-start;

        flex-direction: column;
    }

    .order-actions {
        justify-content: space-between;
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 600px) {

    .order-top {
        align-items: flex-start;

        flex-direction: column;

        gap: 12px;
    }

    .order-actions {
        align-items: stretch;

        flex-direction: column;
    }

    .detail-button,
    .status-select {
        width: 100%;

        justify-content: center;
    }

    .customer-total {
        align-items: flex-start;
    }

}
</style>