<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ShoppingCart, Package, Bell, ShoppingBag } from 'lucide-vue-next';

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

    totalPenghasilan: {
        type: Number,
        default: 0,
    },

    parfumsTerendah: {
        type: Array,
        default: () => [],
    },

        notifications: {
        type: Array,
        default: () => [],
    },

    notificationCount: {
        type: Number,
        default: 0,
    },
    grafikPenghasilan: {
    type: Object,
        default: () => ({
            minggu_ini: [],
            bulan_ini: [],
            tahun_ini: [],
            semua: [],
        }),
    },
});

const logoUrl = '/images/logo-hf.png';


const sidebarOpen = ref(false);
const showNotifications = ref(false);
const periodeGrafik = ref('minggu_ini');
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
const bukaNotifikasi = (notification) => {
    router.put(
        `/super-admin/notifications/${notification.id}/read`,
        {},
        {
            preserveScroll: true,
        }
    );
};
const dataGrafikAktif = computed(() => {
    return props.grafikPenghasilan?.[periodeGrafik.value] ?? [];
});

const nilaiMaksimumGrafik = computed(() => {
    const nilai = dataGrafikAktif.value.map(item =>
        Number(item.value) || 0
    );

    return Math.max(...nilai, 1);
});

const formatGrafikRupiah = (value) => {
    const angka = Number(value) || 0;

    if (angka >= 1000000) {
        return `Rp ${(angka / 1000000).toFixed(1)}jt`;
    }

    if (angka >= 1000) {
        return `Rp ${(angka / 1000).toFixed(0)}rb`;
    }

    return `Rp ${Math.round(angka).toLocaleString('id-ID')}`;
};

const tinggiBar = (value) => {
    if (!nilaiMaksimumGrafik.value) {
        return 0;
    }

    const tinggi =
        (Number(value) / nilaiMaksimumGrafik.value) * 100;

    return Math.max(tinggi, Number(value) > 0 ? 5 : 0);
};
</script>

<template>
    <Head title="Dashboard Super Admin" />

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
                    class="menu-item active"
                >
                    <span>Dashboard</span>
                </Link>

                <Link
                    href="/super-admin/pesanan"
                    class="menu-item"
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

                    <div class="topbar-heading">
                        <h1>Dashboard</h1>
                    </div>

                </div>

                <div class="notification-wrapper">

                    <button
                        type="button"
                        class="notification-button"
                        @click="showNotifications = !showNotifications"
                    >
                        <Bell :size="20" />

                        <span
                            v-if="props.notificationCount > 0"
                            class="notification-badge"
                        >
                            {{ props.notificationCount }}
                        </span>
                    </button>

                    <div
                        v-if="showNotifications"
                        class="notification-dropdown"
                    >

                        <div class="notification-header">
                            <strong>Notifikasi</strong>

                            <span>
                                {{ props.notificationCount }} baru
                            </span>
                        </div>

                        <div
                            v-if="props.notifications.length === 0"
                            class="notification-empty"
                        >
                            Tidak ada notifikasi baru.
                        </div>

                        <div
                            v-else
                            class="notification-list"
                        >

                            <button
                                v-for="notification in props.notifications"
                                :key="notification.id"
                                type="button"
                                class="notification-item"
                                @click="bukaNotifikasi(notification)"
                            >

                                <div class="notification-icon">
                                    <ShoppingBag :size="15" />
                                </div>

                                <div class="notification-content">

                                    <strong>Pesanan baru</strong>

                                    <span>
                                        Pesanan #{{ notification.pesanan?.id }}
                                        —
                                        {{ notification.pesanan?.items?.[0]?.parfum?.nama ?? 'Produk' }}
                                    </span>

                                    <small>
                                        {{ notification.pesanan?.user?.name ?? 'Pelanggan' }}
                                    </small>

                                </div>

                            </button>

                        </div>

                    </div>

                </div>

            </header>

            <section class="stats-grid">

                <div class="stat-card">

                    <div class="stat-top">

                        <div class="stat-icon">
                            <ShoppingCart
                                :size="19"
                                :stroke-width="1.8"
                            />
                        </div>

                        <span class="stat-label">
                            Pesanan
                        </span>

                    </div>

                    <h2>{{ props.totalPesanan }}</h2>

                </div>

                <div class="stat-card">

                    <div class="stat-top">

                        <div class="stat-icon">
                            <Package
                                :size="19"
                                :stroke-width="1.8"
                            />
                        </div>

                        <span class="stat-label">
                            Stok
                        </span>

                    </div>

                    <h2>{{ props.totalStok }}</h2>

                </div>


                <div class="stat-card stat-card--highlight">

                    <div class="stat-top">
                        <div class="stat-icon stat-icon--accent">
                            Rp
                        </div>

                        <span class="stat-label">
                            Penghasilan
                        </span>
                    </div>

                    <h2>Rp{{ Number(props.totalPenghasilan).toLocaleString('id-ID') }}</h2>

                </div>

            </section>

            <section class="bottom-grid">

                <div class="info-card">

                    <div class="info-header">
                        <div>
                            <h2>Pesanan Terbaru</h2>
                        </div>

                        <Link href="/super-admin/pesanan">
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
                        </div>

                        <Link href="/super-admin/stok">
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

    min-height: 100vh;

    background: var(--hf-bg);
    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.notification-wrapper {
    position: relative;
    margin-left: auto;
}

.notification-button {
    position: relative;

    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid var(--hf-border);
    border-radius: 50%;

    background: #ffffff;
    color: var(--hf-green);

    cursor: pointer;

    transition: background .15s ease, border-color .15s ease;
}

.notification-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.notification-badge {
    position: absolute;

    top: -3px;
    right: -4px;

    min-width: 17px;
    height: 17px;

    padding: 0 4px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--hf-green);
    color: #ffffff;

    font-size: 8px;
    font-weight: 700;

    border: 2px solid var(--hf-bg);
}

.notification-dropdown {
    position: absolute;

    top: calc(100% + 10px);
    right: 0;

    width: 330px;
    max-height: 420px;

    overflow-y: auto;

    background: #ffffff;

    border: 1px solid var(--hf-border);
    border-radius: 12px;

    box-shadow: 0 18px 40px rgba(47, 86, 82, .12);

    z-index: 2000;

    animation: fadeInDown .15s ease both;
}

@keyframes fadeInDown {

    from {
        opacity: 0;
        transform: translateY(-6px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}

.notification-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 14px 16px;

    border-bottom: 1px solid var(--hf-border);
}

.notification-header strong {
    color: var(--hf-ink);
    font-size: 12px;
}

.notification-header span {
    color: var(--hf-ink-faint);
    font-size: 9px;
}

.notification-item {
    width: 100%;

    display: flex;
    align-items: flex-start;

    gap: 10px;

    padding: 13px 15px;

    border: none;
    border-bottom: 1px solid var(--hf-border);

    background: #ffffff;

    text-align: left;

    cursor: pointer;

    transition: background .15s ease;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: var(--hf-sage-soft);
}

.notification-icon {
    width: 34px;
    height: 34px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: var(--hf-sage-soft);
    color: var(--hf-green);
}

.notification-content {
    min-width: 0;
}

.notification-content strong {
    display: block;
    margin-bottom: 4px;

    color: var(--hf-ink);
    font-size: 10.5px;
}

.notification-content span {
    display: block;
    margin-bottom: 3px;

    color: var(--hf-ink-soft);
    font-size: 9.5px;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-content small {
    color: var(--hf-ink-faint);
    font-size: 8px;
}

.notification-empty {
    padding: 35px 20px;

    text-align: center;

    color: var(--hf-ink-faint);
    font-size: 10px;
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


.main-content {
    min-height: 100vh;

    padding: 30px 4%;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 28px;
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

.topbar p {
    margin: 3px 0 0;

    font-size: 11px;

    color: var(--hf-ink-faint);
}


.stats-grid {
    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 16px;

    margin-bottom: 20px;
}

.stat-card {
    padding: 20px;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 12px;
}

.stat-card--highlight {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 16px;
}

.stat-icon {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: var(--hf-sage-soft);

    color: var(--hf-green);

    font-size: 14px;
    font-weight: 700;
}

.stat-icon--accent {
    background: #ffffff;
}

.stat-label {
    font-size: 10.5px;
    font-weight: 600;

    color: var(--hf-ink-faint);
    letter-spacing: .02em;
    text-transform: uppercase;
}

.stat-card h2 {
    margin: 0;

    font-size: 23px;

    font-weight: 700;

    color: var(--hf-ink);

    letter-spacing: -.01em;
}

.stat-card--highlight h2 {
    color: var(--hf-green-dark);
    font-size: 20px;
    word-break: break-word;
}

.bottom-grid {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 16px;
}

.info-card {
    min-height: 240px;

    padding: 22px;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 12px;
}

.info-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    margin-bottom: 16px;

    padding-bottom: 14px;

    border-bottom: 1px solid var(--hf-border);
}

.info-header h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 15.5px;

    font-weight: normal;

    color: var(--hf-ink);
}

.info-header a {
    color: var(--hf-green);

    font-size: 10.5px;
    font-weight: 600;

    text-decoration: none;

    padding-bottom: 1px;
    border-bottom: 1px solid transparent;

    transition: border-color .15s ease;
}

.info-header a:hover {
    border-bottom-color: var(--hf-green);
}

.empty-state {
    height: 150px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    color: var(--hf-ink-faint);
}

.empty-state span {
    margin-bottom: 10px;

    font-size: 24px;

    opacity: .6;
}

.empty-state p {
    margin: 0;

    font-size: 10.5px;
}

.lowest-stock-list {
    display: flex;
    flex-direction: column;
}

.lowest-stock-item {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 12px 2px;

    border-bottom: 1px solid var(--hf-border);
}

.lowest-stock-item:last-child {
    border-bottom: none;
}

.lowest-stock-name {
    color: var(--hf-ink-soft);
    font-size: 11.5px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.lowest-stock-number {
    color: var(--hf-green-dark);
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
}

@media (max-width: 600px) {

    .main-content {
        padding: 20px 15px;
    }

    .topbar {
        margin-bottom: 22px;
    }

    .topbar h1 {
        font-size: 21px;
    }

    .notification-dropdown {
        width: calc(100vw - 32px);
        right: -8px;
    }

    .info-card {
        padding: 18px;
    }
}
</style>