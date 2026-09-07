<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    ShoppingBag,
    ChevronDown,
    ChevronUp,
    Package,
    MapPin,
    User,
    Mail,
    CreditCard,
    Bell
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
    notifications: {
    type: Array,
    default: () => [],
    },

    notificationCount: {
        type: Number,
        default: 0,
    },
});


const logoUrl = '/images/logo-hf.png';

const sidebarOpen = ref(false);
const openOrders = ref([]);
const showNotifications = ref(false);
const filterWaktu = ref('semua');
const filterStatus = ref('semua');
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
const pesananTersaring = computed(() => {
    return props.pesanans.filter((pesanan) => {
        const statusCocok =
            filterStatus.value === 'semua' ||
            pesanan.status === filterStatus.value;

        if (!statusCocok) {
            return false;
        }

        if (filterWaktu.value === 'semua') {
            return true;
        }

        const tanggalPesanan = new Date(pesanan.created_at);
        const sekarang = new Date();

        if (filterWaktu.value === 'hari_ini') {
            return (
                tanggalPesanan.getDate() === sekarang.getDate() &&
                tanggalPesanan.getMonth() === sekarang.getMonth() &&
                tanggalPesanan.getFullYear() === sekarang.getFullYear()
            );
        }

        if (filterWaktu.value === 'minggu_ini') {
            const awalMinggu = new Date(sekarang);
            const hari = sekarang.getDay();

            awalMinggu.setDate(
                sekarang.getDate() - hari
            );

            awalMinggu.setHours(0, 0, 0, 0);

            return tanggalPesanan >= awalMinggu;
        }

        if (filterWaktu.value === 'bulan_ini') {
            return (
                tanggalPesanan.getMonth() === sekarang.getMonth() &&
                tanggalPesanan.getFullYear() === sekarang.getFullYear()
            );
        }

        return true;
    });
});

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

    form.put(`/super-admin/pesanan/${pesanan.id}/status`, {
        preserveScroll: true,
    });
};
const bukaNotifikasi = (notification) => {
    router.put(
        `/admin/notifications/${notification.id}/read`,
        {},
        {
            preserveScroll: true,
        }
    );
};
</script>

<template>
    <Head title="Pesanan Super Admin" />

    <div class="admin-page">

        <div
            v-if="sidebarOpen"
            class="overlay"
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="sidebar"
            :class="{ 'sidebar-open': sidebarOpen }"
        >

            <div class="sidebar-header">

                <div class="brand">
                    <img
                        :src="logoUrl"
                        alt="HF Parfum"
                    >

                    <div>
                        <h2>HF Parfum</h2>
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

            <div class="admin-info">

                <div class="admin-avatar">
                    {{ props.authUser?.name?.charAt(0).toUpperCase() }}
                </div>

                <div class="admin-details">

                    <strong>
                        {{ props.authUser?.name }}
                    </strong>

                    <span>
                        Super Admin
                    </span>

                    <small>
                        {{ props.authUser?.email }}
                    </small>

                </div>

            </div>
            <nav class="sidebar-menu">

                <Link
                    href="/super-admin/dashboard"
                    class="menu-item"
                >
                    <span>Dashboard</span>
                </Link>

                <Link
                     href="/super-admin/pesanan"
                    class="menu-item active"
                >
                    <span>Pesanan</span>
                </Link>

                <Link
                     href="/super-admin/stok"
                    class="menu-item"
                >
                    <span>Kelola Stok</span>
                </Link>

                <Link
                    href="/super-admin/pengguna"
                    class="menu-item"
                >
                    <span>Kelola Pengguna</span>
                </Link>

                <Link
                    href="/admin/pengaturan"
                    class="menu-item"
                >
                    <span>Pengaturan</span>
                </Link>

            </nav>

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


        <main class="main-content">

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

                    <div class="topbar-heading">
                        <h1>Pesanan</h1>
                    </div>

                </div>

            </header>


            <section class="order-section">

                <div class="section-heading">

                    <div>
                        <h2>Daftar Pesanan</h2>
                    </div>

                    <div class="order-count">
                        {{ pesananTersaring.length }} Pesanan
                    </div>

                </div>

                <div class="order-filters">

                    <div class="filter-group">
                        <span class="filter-label">Periode</span>

                        <div class="filter-list">
                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterWaktu === 'semua' }"
                                @click="filterWaktu = 'semua'"
                            >
                                Semua
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterWaktu === 'hari_ini' }"
                                @click="filterWaktu = 'hari_ini'"
                            >
                                Hari Ini
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterWaktu === 'minggu_ini' }"
                                @click="filterWaktu = 'minggu_ini'"
                            >
                                Minggu Ini
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterWaktu === 'bulan_ini' }"
                                @click="filterWaktu = 'bulan_ini'"
                            >
                                Bulan Ini
                            </button>
                        </div>
                    </div>

                    <div class="filter-group">
                        <span class="filter-label">Status</span>

                        <div class="filter-list">
                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'semua' }"
                                @click="filterStatus = 'semua'"
                            >
                                Semua
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'menunggu' }"
                                @click="filterStatus = 'menunggu'"
                            >
                                Menunggu
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'diproses' }"
                                @click="filterStatus = 'diproses'"
                            >
                                Diproses
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'dikirim' }"
                                @click="filterStatus = 'dikirim'"
                            >
                                Dikirim
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'diterima' }"
                                @click="filterStatus = 'diterima'"
                            >
                                Diterima
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'selesai' }"
                                @click="filterStatus = 'selesai'"
                            >
                                Selesai
                            </button>

                            <button
                                type="button"
                                class="filter-button"
                                :class="{ active: filterStatus === 'dibatalkan' }"
                                @click="filterStatus = 'dibatalkan'"
                            >
                                Dibatalkan
                            </button>
                        </div>
                    </div>

                </div>

                <div
                    v-if="pesananTersaring.length === 0"
                    class="empty-orders"
                >
                    <div class="empty-orders-icon">
                        <ShoppingBag :size="28" />
                    </div>

                    <h3>
                        Belum ada pesanan
                    </h3>

                    <p>
                        Pesanan pelanggan akan muncul di sini
                        setelah checkout.
                    </p>
                </div>


                <div
                    v-else
                    class="order-list"
                >

                    <article
                        v-for="pesanan in pesananTersaring"
                        :key="pesanan.id"
                        class="order-card"
                    >

                        <div class="order-top">

                            <div class="order-number">

                                <div class="order-icon">
                                    <ShoppingBag :size="17" />
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


                        <div class="customer-row">

                            <div class="customer-info">

                                <div class="customer-icon">
                                    <User :size="15" />
                                </div>

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
                                    :size="14"
                                />

                                <ChevronDown
                                    v-else
                                    :size="14"
                                />
                            </button>


                            <select
                                class="status-select"
                                :value="pesanan.status"
                                @change="
                                    (event) => {
                                        const form = statusForm(pesanan);

                                        form.status = event.target.value;

                                        form.put(
                                            `/super-admin/pesanan/${pesanan.id}/status`,
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


                        <div
                            v-if="isOpen(pesanan.id)"
                            class="order-detail"
                        >

                            <div class="detail-grid">

                                <div class="detail-box">

                                    <div class="detail-title">
                                        <MapPin :size="15" />

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
                                        <MapPin :size="13" />
                                        Buka di Maps
                                    </a>

                                </div>

                                <div class="detail-box">

                                    <div class="detail-title">
                                        <Mail :size="15" />

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

                            <div class="detail-box detail-box--payment">

                                <div class="detail-title">
                                    <CreditCard :size="15" />

                                    <span>
                                        Pembayaran
                                    </span>
                                </div>

                                <strong>
                                    {{
                                        pesanan.metode_pembayaran === 'cod'
                                            ? 'COD'
                                            : pesanan.metode_pembayaran === 'transfer'
                                                ? 'Transfer Bank'
                                                : pesanan.metode_pembayaran
                                    }}
                                </strong>

                                <span
                                    class="payment-status"
                                    :class="{
                                        'payment-status--paid':
                                            pesanan.status_pembayaran === 'sudah_bayar'
                                    }"
                                >
                                    Status:
                                    {{
                                        pesanan.status_pembayaran === 'sudah_bayar'
                                            ? 'Sudah Dibayar'
                                            : 'Belum Dibayar'
                                    }}
                                </span>

                            </div>

                            <div class="detail-title products-title">
                                <Package :size="16" />

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
                                            :size="18"
                                        />

                                    </div>

                                    <div class="product-info">

                                        <strong>
                                            {{ item.parfum?.nama }}
                                        </strong>

                                        <span class="product-size">
                                            {{ item.ukuran_ml }} ml
                                        </span>

                                        <span>
                                            {{ formatRupiah(item.harga) }}
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
    --hf-green: #3f6e69;
    --hf-green-dark: #2f5652;
    --hf-sage: #cddbd6;
    --hf-sage-soft: #eef4f2;
    --hf-bg: #f5f8f7;
    --hf-ink: #2c3f3d;
    --hf-ink-soft: #5f7472;
    --hf-ink-faint: #97a6a4;
    --hf-border: #e3ece9;
    --hf-red: #b5504b;
    --hf-red-bg: #fbeeee;
    --hf-amber: #b8862f;
    --hf-amber-bg: #f7f0e2;
    --hf-blue: #5c76a3;
    --hf-blue-bg: #edf1f8;

    min-height: 100vh;

    background: var(--hf-bg);

    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.sidebar {
    position: fixed;

    top: 0;
    left: 0;

    width: 270px;
    height: 100vh;

    padding: 24px 16px;

    display: flex;
    flex-direction: column;

    background: #ffffff;

    border-right: 1px solid var(--hf-border);

    transform: translateX(-100%);

    transition: transform .25s ease;

    z-index: 1000;

    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar.sidebar-open {
    transform: translateX(0);

    box-shadow: 10px 0 34px rgba(47, 86, 82, .12);
}

.sidebar-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 30px;
}

.brand {
    display: flex;

    align-items: center;

    gap: 11px;
}

.brand img {
    width: 40px;
    height: 40px;

    object-fit: contain;

    border-radius: 8px;

    mix-blend-mode: multiply;
}

.brand h2 {
    margin: 0 0 2px;

    font-family: Georgia, serif;

    font-size: 16px;

    font-weight: normal;

    color: var(--hf-ink);
}

.brand span {
    font-size: 9px;

    color: var(--hf-ink-faint);
    letter-spacing: .06em;
    text-transform: uppercase;
}

.close-button {
    width: 34px;
    height: 34px;

    border: none;

    border-radius: 8px;

    background: transparent;

    color: var(--hf-ink-faint);

    font-size: 22px;
    line-height: 1;

    cursor: pointer;

    transition: background .15s ease;
}

.close-button:hover {
    background: var(--hf-sage-soft);
}


.admin-info {
    display: flex;

    align-items: center;

    width: 100%;

    margin: 4px 0 24px;

    padding: 13px;

    border-radius: 10px;
    background: var(--hf-bg);
}

.admin-avatar {
    width: 42px;
    height: 42px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-right: 12px;

    border-radius: 50%;

    background: var(--hf-sage-soft);

    color: var(--hf-green);

    font-family: Georgia, serif;

    font-size: 18px;
}

.admin-details {
    min-width: 0;
}

.admin-details strong {
    display: block;

    margin-bottom: 3px;

    color: var(--hf-ink);

    font-size: 12.5px;

    font-weight: 600;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.admin-details span {
    display: block;

    margin-bottom: 3px;

    color: var(--hf-green);

    font-size: 10px;
    font-weight: 600;
}

.admin-details small {
    display: block;

    overflow: hidden;

    color: var(--hf-ink-faint);

    font-size: 9px;

    text-overflow: ellipsis;

    white-space: nowrap;
}

.sidebar-menu {
    display: flex;

    flex-direction: column;

    gap: 3px;
}

.menu-item {
    display: flex;

    align-items: center;

    gap: 12px;

    padding: 12px 13px;

    border-radius: 8px;

    color: var(--hf-ink-soft);

    text-decoration: none;

    font-size: 12px;
    font-weight: 500;

    transition: background .15s ease, color .15s ease;
}

.menu-item:hover {
    background: var(--hf-sage-soft);

    color: var(--hf-green);
}

.menu-item.active {
    background: var(--hf-sage-soft);

    color: var(--hf-green-dark);

    font-weight: 700;
}

.sidebar-footer {
    margin-top: 22px;

    padding-top: 14px;

    border-top: 1px solid var(--hf-border);
}

.logout-button {
    width: 100%;

    display: flex;

    align-items: center;
    justify-content: center;

    gap: 10px;

    padding: 12px 14px;

    border: 1px solid var(--hf-red-bg);

    border-radius: 9px;

    background: var(--hf-red-bg);

    color: var(--hf-red);

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.logout-button:hover {
    background: #f6e2e1;
}

.logout-button:disabled {
    opacity: .6;
    cursor: not-allowed;
}

.overlay {
    position: fixed;

    inset: 0;

    background: rgba(30, 45, 43, .32);

    z-index: 999;
}

/* ---------- Main / topbar ---------- */

.main-content {
    min-height: 100vh;

    padding: 30px 4%;
}

.topbar {
    display: flex;

    align-items: center;

    margin-bottom: 26px;
}

.topbar-left {
    display: flex;

    align-items: center;

    gap: 16px;
}

.toggle-button {
    width: 42px;
    height: 42px;
    flex-shrink: 0;

    border: 1px solid var(--hf-border);

    border-radius: 10px;

    background: #ffffff;

    color: var(--hf-green);

    font-size: 17px;

    cursor: pointer;

    transition: background .15s ease;
}

.toggle-button:hover {
    background: var(--hf-sage-soft);
}

.topbar-heading {
    display: flex;
    flex-direction: column;
}

.topbar-eyebrow {
    font-size: 10px;
    letter-spacing: .1em;
    text-transform: uppercase;

    color: var(--hf-ink-faint);
    margin-bottom: 2px;
}

.topbar h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 25px;

    font-weight: normal;

    color: var(--hf-ink);
}

/* ---------- Order section ---------- */

.order-section {
    max-width: 1180px;

    margin: 0 auto;
}

.section-heading {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 16px;
}

.section-heading h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 19px;

    font-weight: normal;

    color: var(--hf-ink);
}

.order-count {
    padding: 8px 13px;

    border-radius: 8px;

    background: var(--hf-sage-soft);

    color: var(--hf-green-dark);

    font-size: 10.5px;
    font-weight: 600;
}

.order-filters {
    margin-bottom: 20px;
    padding: 16px;

    background: #ffffff;
    border: 1px solid var(--hf-border);
    border-radius: 12px;
}

.filter-group + .filter-group {
    margin-top: 14px;
}

.filter-label {
    display: block;
    margin-bottom: 8px;

    color: var(--hf-ink-faint);
    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.filter-list {
    display: flex;
    gap: 7px;

    overflow-x: auto;
    scrollbar-width: none;
}

.filter-list::-webkit-scrollbar {
    display: none;
}

.filter-button {
    flex: 0 0 auto;

    padding: 8px 13px;

    border: 1px solid var(--hf-border);
    border-radius: 7px;

    background: #ffffff;
    color: var(--hf-ink-soft);

    font-size: 10.5px;
    font-weight: 500;

    cursor: pointer;
    transition: background .15s ease, border-color .15s ease, color .15s ease;
}

.filter-button:hover {
    background: var(--hf-sage-soft);
    color: var(--hf-green);
}

.filter-button.active {
    background: var(--hf-green);
    border-color: var(--hf-green);
    color: #ffffff;
}

/* ---------- Empty state ---------- */

.empty-orders {
    min-height: 300px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    padding: 40px;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 12px;

    text-align: center;
}

.empty-orders-icon {
    width: 54px;
    height: 54px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 14px;

    border-radius: 12px;

    background: var(--hf-sage-soft);

    color: var(--hf-green);
}

.empty-orders h3 {
    margin: 0 0 6px;

    font-family: Georgia, serif;

    font-size: 17px;

    font-weight: normal;

    color: var(--hf-ink);
}

.empty-orders p {
    margin: 0;

    font-size: 10.5px;

    color: var(--hf-ink-faint);
}


/* ---------- Order cards ---------- */

.order-list {
    display: flex;

    flex-direction: column;

    gap: 12px;
}

.order-card {
    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 12px;

    overflow: hidden;
}

.order-top {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 12px;

    padding: 16px 18px;

    border-bottom: 1px solid var(--hf-border);
}

.order-number {
    display: flex;

    align-items: center;

    gap: 12px;
}

.order-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: var(--hf-sage-soft);

    color: var(--hf-green);
}

.order-number strong {
    display: block;

    margin-bottom: 3px;

    font-size: 12.5px;
    font-weight: 600;

    color: var(--hf-ink);
}

.order-number span {
    font-size: 9.5px;

    color: var(--hf-ink-faint);
}

.status-badge {
    flex-shrink: 0;

    padding: 6px 11px;

    border-radius: 6px;

    font-size: 9.5px;
    font-weight: 600;
    text-transform: capitalize;
    letter-spacing: .01em;
}

.status-menunggu {
    background: var(--hf-amber-bg);

    color: var(--hf-amber);
}

.status-diproses {
    background: var(--hf-sage-soft);

    color: var(--hf-green);
}

.status-dikirim {
    background: var(--hf-blue-bg);

    color: var(--hf-blue);
}

.status-diterima {
    background: var(--hf-sage-soft);

    color: var(--hf-green-dark);
}

.status-selesai {
    background: var(--hf-sage-soft);

    color: var(--hf-green-dark);

    font-weight: 700;
}

.status-dibatalkan {
    background: var(--hf-red-bg);

    color: var(--hf-red);
}

.customer-row {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 16px;

    padding: 14px 18px;
}

.customer-info {
    display: flex;

    align-items: center;

    gap: 11px;

    min-width: 0;
}

.customer-icon {
    width: 32px;
    height: 32px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--hf-bg);
    color: var(--hf-ink-soft);
}

.customer-info strong {
    display: block;

    margin-bottom: 3px;

    color: var(--hf-ink);

    font-size: 12px;
    font-weight: 600;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.customer-info span {
    display: block;

    color: var(--hf-ink-faint);

    font-size: 9.5px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.customer-total {
    display: flex;

    align-items: flex-end;

    flex-direction: column;

    gap: 3px;

    flex-shrink: 0;
}

.customer-total span {
    color: var(--hf-ink-faint);

    font-size: 9.5px;
}

.customer-total strong {
    color: var(--hf-green-dark);

    font-size: 15px;
    font-weight: 700;
}

.order-actions {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 10px;

    padding: 0 18px 16px;
}

.detail-button {
    display: flex;

    align-items: center;

    gap: 6px;

    padding: 9px 12px;

    border: 1px solid var(--hf-border);

    border-radius: 8px;

    background: #ffffff;

    color: var(--hf-green);

    font-size: 10.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease, border-color .15s ease;
}

.detail-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.status-select {
    padding: 9px 12px;

    border: 1px solid var(--hf-border);

    border-radius: 8px;

    background: #ffffff;

    color: var(--hf-ink);

    font-size: 10.5px;
    font-weight: 500;

    outline: none;

    cursor: pointer;

    transition: border-color .15s ease, box-shadow .15s ease;
}

.status-select:focus {
    border-color: var(--hf-green);
    box-shadow: 0 0 0 3px rgba(63, 110, 105, .10);
}

.order-detail {
    padding: 18px;

    border-top: 1px solid var(--hf-border);

    background: var(--hf-bg);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 12px;
}

.detail-box {
    padding: 15px;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 10px;
}

.detail-box--payment {
    margin-bottom: 18px;
}

.detail-title {
    display: flex;

    align-items: center;

    gap: 7px;

    margin-bottom: 11px;

    color: var(--hf-ink-faint);

    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.detail-box strong {
    display: block;

    margin-bottom: 4px;

    color: var(--hf-ink);

    font-size: 12px;
    font-weight: 600;
}

.detail-box span {
    display: block;

    margin-bottom: 6px;

    color: var(--hf-ink-soft);

    font-size: 10px;
}

.detail-box p {
    margin: 0;

    color: var(--hf-ink-soft);

    font-size: 10.5px;

    line-height: 1.7;
}

.payment-status {
    display: inline-block;

    color: var(--hf-amber) !important;
    font-weight: 600;
}

.payment-status--paid {
    color: var(--hf-green-dark) !important;
}

.products-title {
    margin-top: 4px;
    margin-bottom: 10px;
}



.product-list {
    display: flex;

    flex-direction: column;

    gap: 8px;

    margin-top: 0;
}

.product-row {
    display: flex;

    align-items: center;

    gap: 11px;

    padding: 10px;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 9px;
}

.product-image {
    width: 48px;
    height: 48px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 8px;

    background: var(--hf-sage-soft);

    color: var(--hf-ink-faint);
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

    color: var(--hf-ink);

    font-size: 11px;
    font-weight: 600;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-info span {
    color: var(--hf-ink-faint);

    font-size: 9.5px;
}

.product-subtotal {
    flex-shrink: 0;

    color: var(--hf-green-dark);

    font-size: 11.5px;
    font-weight: 700;
}

.product-size {
    display: block;
    margin-bottom: 3px;

    color: var(--hf-green);
    font-size: 9.5px;
    font-weight: 600;
}

.maps-button {
    margin-top: 12px;

    display: inline-flex;
    align-items: center;

    gap: 6px;

    padding: 8px 11px;

    border: 1px solid var(--hf-border);
    border-radius: 8px;

    background: var(--hf-bg);

    color: var(--hf-green);

    font-size: 10px;
    font-weight: 600;

    text-decoration: none;

    transition: background .15s ease;
}

.maps-button:hover {
    background: var(--hf-sage-soft);
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

    .topbar h1 {
        font-size: 21px;
    }

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