<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
    authUser: {
        type: Object,
        default: null,
    },
});

const sidebarOpen = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const showConfirm = ref(false);
const selectedUser = ref(null);

const logoUrl = '/images/logo.jpg';
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};

const roleForm = useForm({
    role: '',
});

const showSuccessNotification = (message) => {
    successMessage.value = message;
    showSuccess.value = true;

    setTimeout(() => {
        showSuccess.value = false;
    }, 2000);
};

const showErrorNotification = (message) => {
    errorMessage.value = message;
    showError.value = true;

    setTimeout(() => {
        showError.value = false;
    }, 2500);
};

const updateRole = (user, role) => {
    if (user.id === props.authUser?.id) {
        showErrorNotification('Role akun sendiri tidak dapat diubah.');
        return;
    }

    roleForm.role = role;

    roleForm.put(`/super-admin/pengguna/${user.id}/role`, {
        preserveScroll: true,

        onSuccess: () => {
            showSuccessNotification('Role pengguna berhasil diperbarui.');
        },

        onError: (errors) => {
            const firstError = Object.values(errors)[0];

            showErrorNotification(
                firstError || 'Role pengguna gagal diperbarui.'
            );
        },
    });
};

const confirmDelete = (user) => {
    if (user.id === props.authUser?.id) {
        showErrorNotification('Akun sendiri tidak dapat dihapus.');
        return;
    }

    selectedUser.value = user;
    showConfirm.value = true;
};

const deleteUser = () => {
    if (!selectedUser.value) return;

    const userId = selectedUser.value.id;

    useForm({}).delete(
        `/super-admin/pengguna/${userId}`,
        {
            preserveScroll: true,

            onSuccess: () => {
                showConfirm.value = false;
                showSuccessNotification('Pengguna berhasil dihapus.');
                selectedUser.value = null;
            },

            onError: () => {
                showErrorNotification('Pengguna gagal dihapus.');
            },
        }
    );
};

const closeConfirm = () => {
    showConfirm.value = false;
    selectedUser.value = null;
};

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-';

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

const roleLabel = (role) => {
    if (role === 'super_admin') return 'Super Admin';
    if (role === 'admin') return 'Admin';
    return 'Pengguna';
};
</script>

<template>
    <Head title="Kelola Pengguna" />

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
                    class="menu-item active"
                >
                    <span>Kelola Pengguna</span>
                </Link>

                <Link
                    href="/super-admin/laporan"
                    class="menu-item"
                >
                    <span>Laporan</span>
                </Link>

                <Link
                    href="/super-admin/pengaturan"
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
                    {{
                        logoutForm.processing
                            ? 'Keluar...'
                            : 'Logout'
                    }}
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
                        <h1>Kelola Pengguna</h1>
                    </div>
                </div>
            </header>

            <section
                v-if="users.length === 0"
                class="empty-card"
            >
                <div class="empty-icon">
                    ♡
                </div>

                <h2>Belum Ada Pengguna</h2>

                <p>
                    Belum ada pengguna yang terdaftar.
                </p>
            </section>

            <section
                v-else
                class="table-card"
            >
                <div class="table-wrapper">

                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="user in users"
                                :key="user.id"
                            >
                                <td>
                                    <strong class="user-name">
                                        {{ user.name }}
                                    </strong>
                                </td>

                                <td>
                                    <span class="user-email">
                                        {{ user.email }}
                                    </span>
                                </td>

                                <td>
                                    <select
                                        :value="user.role"
                                        class="role-select"
                                        :disabled="user.id === props.authUser?.id || roleForm.processing"
                                        @change="updateRole(user, $event.target.value)"
                                    >
                                        <option value="user">
                                            Pengguna
                                        </option>

                                        <option value="admin">
                                            Admin
                                        </option>

                                        <option value="super_admin">
                                            Super Admin
                                        </option>
                                    </select>
                                </td>

                                <td>
                                    {{ formatTanggal(user.created_at) }}
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="delete-button"
                                        :disabled="user.id === props.authUser?.id"
                                        @click="confirmDelete(user)"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </section>

            <div
                v-if="showConfirm"
                class="modal-overlay"
                @click.self="closeConfirm"
            >
                <div class="confirm-modal">

                    <div class="confirm-icon">
                        !
                    </div>

                    <h2>Hapus Pengguna?</h2>

                    <p v-if="selectedUser">
                        Apakah anda ingin menghapus pengguna
                        "{{ selectedUser.name }}"?
                    </p>

                    <div class="confirm-actions">
                        <button
                            type="button"
                            class="cancel-button"
                            @click="closeConfirm"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            class="confirm-delete-button"
                            @click="deleteUser"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            <Transition name="success">
                <div
                    v-if="showSuccess"
                    class="success-overlay"
                >
                    <div class="success-modal">
                        <div class="success-icon">
                            ✓
                        </div>

                        <h3>Berhasil!</h3>

                        <p>
                            {{ successMessage }}
                        </p>
                    </div>
                </div>
            </Transition>

            <Transition name="error">
                <div
                    v-if="showError"
                    class="success-overlay"
                >
                    <div class="success-modal error-modal">
                        <div class="error-mark">
                            ×
                        </div>

                        <h3>Gagal!</h3>

                        <p>
                            {{ errorMessage }}
                        </p>
                    </div>
                </div>
            </Transition>

        </main>
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
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
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #999;
    cursor: pointer;
}

.close-button:hover {
    background: #f3f9f9;
}

.admin-info {
    display: flex;
    align-items: center;
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

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 13px 14px;
    border-radius: 9px;
    color: #777;
    text-decoration: none;
    font-size: 12px;
    transition: .2s ease;
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
    padding: 13px 14px;
    border: 1px solid #f0dddd;
    border-radius: 9px;
    background: #ffffff;
    color: #b77777;
    font-size: 12px;
    cursor: pointer;
    transition: .2s;
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

.admin-page {
    min-height: 100vh;
    background: #edf3f2;
    color: #526363;
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
    transition: .2s;
}

.toggle-button:hover {
    background: #effafa;
}

.topbar h1 {
    margin: 0;
    font-family: Georgia, serif;
    font-size: 29px;
    font-weight: normal;
    color: #666;
}

.table-card {
    width: 100%;
    background: #fbfdfc;
    border: 1px solid #d7e3e1;
    border-radius: 14px;
    box-shadow: 0 8px 25px rgba(50, 90, 90, .05);
    overflow: hidden;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.user-table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
    font-size: 11px;
}

.user-table thead {
    background: #f2f7f6;
}

.user-table th {
    padding: 15px 18px;
    color: #6f8585;
    font-size: 10px;
    font-weight: 600;
    text-align: left;
    border-bottom: 1px solid #dce8e6;
    white-space: nowrap;
}

.user-table td {
    padding: 14px 18px;
    color: #526363;
    border-bottom: 1px solid #e8efee;
    vertical-align: middle;
}

.user-table tbody tr:last-child td {
    border-bottom: none;
}

.user-table tbody tr:hover {
    background: #f8fbfa;
}

.user-name {
    color: #344747;
    font-family: Georgia, serif;
    font-size: 13px;
    font-weight: normal;
}

.user-email {
    color: #819090;
    font-size: 11px;
}

.role-select {
    min-width: 135px;
    padding: 8px 10px;
    border: 1px solid #d4e2e0;
    border-radius: 8px;
    outline: none;
    background: #ffffff;
    color: #526363;
    font-size: 10px;
    cursor: pointer;
}

.role-select:focus {
    border-color: #7eabab;
    box-shadow: 0 0 0 3px rgba(126, 171, 171, .12);
}

.role-select:disabled {
    background: #f4f6f6;
    color: #a2adad;
    cursor: not-allowed;
}

.delete-button {
    padding: 7px 11px;
    border: 1px solid #efd4d4;
    border-radius: 6px;
    background: #ffffff;
    color: #b87575;
    font-size: 9px;
    cursor: pointer;
    transition: .2s;
}

.delete-button:hover:not(:disabled) {
    background: #fff5f5;
}

.delete-button:disabled {
    opacity: .45;
    cursor: not-allowed;
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
    margin: 0;
    font-size: 11px;
    color: #8b9999;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 25px;
    background: rgba(24, 49, 49, .45);
    z-index: 2000;
}

.confirm-modal {
    width: 100%;
    max-width: 390px;
    padding: 30px;
    background: #fbfdfc;
    border: 1px solid #d7e3e1;
    border-radius: 17px;
    text-align: center;
    box-shadow: 0 25px 70px rgba(20, 50, 50, .2);
}

.confirm-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 17px;
    border-radius: 50%;
    background: #eaf3f2;
    color: #477878;
    font-size: 22px;
    font-weight: 600;
}

.confirm-modal h2 {
    margin: 0 0 10px;
    font-family: Georgia, serif;
    font-size: 21px;
    font-weight: normal;
    color: #344747;
}

.confirm-modal p {
    margin: 0 auto;
    max-width: 300px;
    font-size: 11px;
    line-height: 1.7;
    color: #879797;
}

.confirm-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 25px;
}

.cancel-button,
.confirm-delete-button {
    padding: 11px 20px;
    border-radius: 8px;
    font-size: 10px;
    cursor: pointer;
    transition: .2s;
}

.cancel-button {
    border: 1px solid #d5e2e0;
    background: #ffffff;
    color: #788888;
}

.cancel-button:hover {
    background: #f0f5f4;
}

.confirm-delete-button {
    border: none;
    background: #b87575;
    color: white;
}

.confirm-delete-button:hover {
    background: #a46262;
}

.success-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(20, 40, 40, .25);
    backdrop-filter: blur(3px);
}

.success-modal {
    width: 330px;
    padding: 35px 30px;
    background: #ffffff;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, .15);
}

.success-icon,
.error-mark {
    width: 80px;
    height: 80px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 45px;
    font-weight: 300;
}

.success-icon {
    color: #62b66a;
}

.error-mark {
    color: #d96b6b;
}

.success-modal h3 {
    margin: 0 0 8px;
    font-family: Georgia, serif;
    font-size: 25px;
    font-weight: normal;
    color: #31504f;
}

.error-modal h3 {
    color: #b75c5c;
}

.success-modal p {
    margin: 0;
    font-size: 13px;
    color: #819393;
}

.success-enter-active {
    animation: successIn .3s ease;
}

.success-leave-active {
    animation: successOut .25s ease;
}

.error-enter-active {
    animation: successIn .3s ease;
}

.error-leave-active {
    animation: successOut .25s ease;
}

@keyframes successIn {
    from {
        opacity: 0;
        transform: scale(.8);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes successOut {
    from {
        opacity: 1;
        transform: scale(1);
    }

    to {
        opacity: 0;
        transform: scale(.9);
    }
}

@media (max-width: 750px) {
    .main-content {
        padding: 22px 15px;
    }
}
</style>