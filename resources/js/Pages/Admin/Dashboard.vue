<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ShoppingCart, Package } from 'lucide-vue-next';

const props = defineProps({
    authUser: Object,

    totalStok: {
        type: Number,
        default: 0,
    },

    totalPesanan: {
        type: Number,
        default: 0,
    },

    parfumsTerendah: {
        type: Array,
        default: () => [],
    },
});

const logoUrl = '/images/logo.jpg';


const sidebarOpen = ref(false);
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <Head title="Dashboard Admin" />

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

            <nav class="sidebar-menu">

                <Link
                    href="/admin/dashboard"
                    class="menu-item active"
                >
                    <span>Dashboard</span>
                </Link>

                <Link
                    href="/admin/pesanan"
                    class="menu-item"
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

            <div class="sidebar-footer">
            <button
                type="button"
                class="logout-button"
                @click="logout"
                :disabled="logoutForm.processing"
            >
                <span>
                    {{ logoutForm.processing ? 'Keluar...' : 'Logout' }}
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

                    <div>
        <h1   h1>Dashboard</h1>

        <p>
            Kelola dan pantau aktivitas Lamore Perfumes.
        </p>
    </div>

    </div>

    </header>

    <section class="stats-grid">

        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon">
                    <ShoppingCart
                        :size="20"
                        :stroke-width="1.8"
                    />
                </div>

                <span class="stat-label">
                    Pesanan
                </span>

            </div>

            <h2>{{ props.totalPesanan }}</h2>

            <p>
                Total pesanan
            </p>

        </div>

    <div class="stat-card">

        <div class="stat-top">

            <div class="stat-icon">
                <Package
                    :size="20"
                    :stroke-width="1.8"
                />
            </div>

            <span class="stat-label">
                Stok
            </span>

        </div>

        <h2>{{ props.totalStok }}</h2>

        <p>
            Total stok parfum
        </p>

    </div>

                
                <div class="stat-card">

                    <div class="stat-top">
                        <div class="stat-icon">
                            Rp
                        </div>

                        <span class="stat-label">
                            Penghasilan
                        </span>
                    </div>

                    <h2>Rp0</h2>

                    <p>
                        Total penghasilan
                    </p>

                </div>

            </section>

            <section class="chart-card">

                <div class="section-header">

                    <div>
                        <h2>Grafik Penjualan</h2>

                        <p>
                            Ringkasan perkembangan penjualan.
                        </p>
                    </div>

                    <select class="period-select">
                        <option>Minggu ini</option>
                        <option>Bulan ini</option>
                        <option>Tahun ini</option>
                    </select>

                </div>

                <div class="chart">

                    <div class="chart-y-axis">
                        <span>Rp 1jt</span>
                        <span>Rp 750rb</span>
                        <span>Rp 500rb</span>
                        <span>Rp 250rb</span>
                        <span>Rp 0</span>
                    </div>

                    <div class="chart-content">

                        <div class="grid-line line-1"></div>
                        <div class="grid-line line-2"></div>
                        <div class="grid-line line-3"></div>
                        <div class="grid-line line-4"></div>
                        <div class="grid-line line-5"></div>

                        <div class="empty-chart">
                            <div class="empty-icon">
                                📊
                            </div>

                            <p>
                                Belum ada data penjualan
                            </p>

                            <span>
                                Grafik akan terisi setelah transaksi tersedia.
                            </span>
                        </div>

                        <div class="chart-days">
                            <span>Sen</span>
                            <span>Sel</span>
                            <span>Rab</span>
                            <span>Kam</span>
                            <span>Jum</span>
                            <span>Sab</span>
                            <span>Min</span>
                        </div>

                    </div>

                </div>

            </section>

            <section class="bottom-grid">

                <div class="info-card">

                    <div class="info-header">
                        <div>
                            <h2>Pesanan Terbaru</h2>
                            <p>Pesanan yang baru masuk.</p>
                        </div>

                        <Link href="/admin/pesanan">
                            Lihat semua
                        </Link>
                    </div>

                    <div class="empty-state">
                        <span>🛍</span>

                        <p>
                            Belum ada pesanan.
                        </p>
                    </div>

                </div>

                <div class="info-card">

                    <div class="info-header">
                        <div>
                            <h2>Stok Terendah</h2>
                            <p>Parfum dengan stok paling sedikit.</p>
                        </div>

                        <Link href="/admin/stok">
                            Kelola stok
                        </Link>
                    </div>

                    <div class="lowest-stock-list">

                        <div
                            v-for="parfum in props.parfumsTerendah"
                            :key="parfum.id"
                            class="lowest-stock-item"
                        >

                            <span class="lowest-stock-name">
                                {{ parfum.nama }}
                            </span>

                            <strong class="lowest-stock-number">
                                {{ parfum.stok }}
                            </strong>

                        </div>

                        <div
                            v-if="props.parfumsTerendah.length === 0"
                            class="empty-state"
                        >
                            <p>
                                Belum ada data stok.
                            </p>
                        </div>

                    </div>

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
    transition: transform 0.3s ease;

    z-index: 1000;

    box-shadow: 8px 0 30px rgba(100, 130, 130, 0.08);

    overflow-y: auto;
    overflow-x: hidden;
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

    flex-shrink: 0;

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
    margin: 0 0 2px;

    font-family: Georgia, serif;

    font-size: 18px;
    font-weight: normal;

    color: #6f9d9d;
}

.brand span {
    font-size: 9px;

    color: #aaa;

    letter-spacing: 0.3px;
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

    transition: all 0.2s ease;
}

.menu-icon {
    width: 20px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    font-size: 14px;
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

    gap: 13px;

    padding: 13px 14px;

    border: 1px solid #f0dddd;

    border-radius: 9px;

    background: #ffffff;

    color: #b77777;

    font-size: 12px;

    cursor: pointer;

    transition: 0.2s;
}

.logout-button:hover {
    background: #fff8f8;
}

.overlay {
    position: fixed;

    inset: 0;

    background: rgba(0, 0, 0, 0.22);

    z-index: 999;
}


.main-content {
    min-height: 100vh;

    padding: 30px 35px;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

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

    transition: 0.2s;

    box-shadow: 0 5px 18px rgba(100, 130, 130, 0.04);
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

.admin-profile {
    display: flex;
    align-items: center;

    gap: 10px;
}

.profile-avatar {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #eaf7f7;

    color: #6f9d9d;

    font-family: Georgia, serif;

    font-size: 16px;
}

.profile-info strong {
    display: block;

    font-size: 11px;

    font-weight: 500;

    color: #666;
}

.profile-info span {
    display: block;

    margin-top: 2px;

    font-size: 9px;

    color: #aaa;
}


.stats-grid {
    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 20px;

    margin-bottom: 25px;
}

.stat-card {
    padding: 22px;

    background: #ffffff;

    border: 1px solid #e2eeee;

    border-radius: 14px;

    box-shadow: 0 8px 25px rgba(100, 130, 130, 0.045);
}

.stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 17px;
}

.stat-icon {
    width: 43px;
    height: 43px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #effafa;

    color: #6f9d9d;

    font-size: 17px;
}

.stat-label {
    font-size: 10px;

    color: #aaa;
}

.stat-card h2 {
    margin: 0 0 6px;

    font-size: 24px;

    font-weight: 500;

    color: #6f9d9d;
}

.stat-card p {
    margin: 0;

    font-size: 9px;

    color: #aaa;
}

.chart-card {
    padding: 25px;

    margin-bottom: 25px;

    background: #ffffff;

    border: 1px solid #e2eeee;

    border-radius: 14px;

    box-shadow: 0 8px 25px rgba(100, 130, 130, 0.045);
}

.section-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    margin-bottom: 25px;
}

.section-header h2 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 20px;

    font-weight: normal;

    color: #666;
}

.section-header p {
    margin: 0;

    font-size: 10px;

    color: #aaa;
}

.period-select {
    padding: 9px 12px;

    border: 1px solid #dceeee;

    border-radius: 7px;

    background: #ffffff;

    color: #777;

    font-size: 10px;

    outline: none;

    cursor: pointer;
}


.chart {
    height: 300px;

    display: flex;

    position: relative;
}

.chart-y-axis {
    width: 55px;

    display: flex;
    flex-direction: column;
    justify-content: space-between;

    padding: 0 10px 28px 0;

    color: #bbb;

    font-size: 8px;

    text-align: right;
}

.chart-content {
    position: relative;

    flex: 1;

    border-bottom: 1px solid #e7eeee;
}

.grid-line {
    position: absolute;

    left: 0;
    right: 0;

    border-top: 1px dashed #e7eeee;
}

.line-1 {
    top: 0;
}

.line-2 {
    top: 25%;
}

.line-3 {
    top: 50%;
}

.line-4 {
    top: 75%;
}

.line-5 {
    bottom: 28px;
}

.empty-chart {
    position: absolute;

    top: 42%;
    left: 50%;

    transform: translate(-50%, -50%);

    text-align: center;

    color: #aaa;
}

.empty-icon {
    margin-bottom: 10px;

    font-size: 28px;

    opacity: 0.65;
}

.empty-chart p {
    margin: 0 0 5px;

    font-size: 11px;
}

.empty-chart span {
    font-size: 9px;

    color: #bbb;
}

.chart-days {
    position: absolute;

    left: 0;
    right: 0;
    bottom: 0;

    display: grid;

    grid-template-columns: repeat(7, 1fr);

    text-align: center;

    color: #aaa;

    font-size: 8px;
}


.bottom-grid {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 20px;
}

.info-card {
    min-height: 250px;

    padding: 23px;

    background: #ffffff;

    border: 1px solid #e2eeee;

    border-radius: 14px;

    box-shadow: 0 8px 25px rgba(100, 130, 130, 0.045);
}

.info-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    margin-bottom: 20px;
}

.info-header h2 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 18px;

    font-weight: normal;

    color: #666;
}

.info-header p {
    margin: 0;

    font-size: 9px;

    color: #aaa;
}

.info-header a {
    color: #6f9d9d;

    font-size: 9px;

    text-decoration: none;
}

.info-header a:hover {
    text-decoration: underline;
}

.empty-state {
    height: 150px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    color: #aaa;
}

.empty-state span {
    margin-bottom: 10px;

    font-size: 25px;

    opacity: 0.7;
}

.empty-state p {
    margin: 0;

    font-size: 10px;
}

.lowest-stock-list {
    display: flex;
    flex-direction: column;
}

.lowest-stock-item {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 13px 4px;

    border-bottom: 1px solid #edf2f2;
}

.lowest-stock-item:last-child {
    border-bottom: none;
}

.lowest-stock-name {
    color: #666;
    font-size: 11px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.lowest-stock-number {
    color: #6f9d9d;
    font-size: 13px;
    font-weight: 700;

    margin-left: 15px;
}

@media (max-width: 900px) {

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .bottom-grid {
        grid-template-columns: 1fr;
    }

    .admin-profile {
        display: none;
    }
}

@media (max-width: 600px) {

    .main-content {
        padding: 20px 15px;
    }

    .topbar {
        margin-bottom: 25px;
    }

    .topbar h1 {
        font-size: 24px;
    }

    .chart-card,
    .info-card {
        padding: 18px;
    }

    .section-header {
        gap: 15px;
    }

    .period-select {
        flex-shrink: 0;
    }

}
</style>