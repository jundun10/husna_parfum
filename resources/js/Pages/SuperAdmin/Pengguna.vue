<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Plus, Pencil, Trash2, Search, X } from 'lucide-vue-next';

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

const logoUrl = '/images/logo-hf.png';
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

/* ===================================================
   SEARCH & FILTER
   (dilakukan di data yang sudah tersedia dari props,
   tidak ada request/endpoint baru)
=================================================== */

const searchQuery = ref('');
const filterRole = ref('Semua');

const usersFiltered = computed(() => {
    const keyword = searchQuery.value.trim().toLowerCase();

    return props.users.filter((user) => {
        const cocokKeyword =
            !keyword ||
            user.name?.toLowerCase().includes(keyword) ||
            user.email?.toLowerCase().includes(keyword);

        const cocokRole =
            filterRole.value === 'Semua' ||
            user.role === filterRole.value;

        return cocokKeyword && cocokRole;
    });
});

/* ===================================================
   TAMBAH PENGGUNA
   POST /super-admin/pengguna
=================================================== */

const showAddModal = ref(false);

const addForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'admin',
});

const openAddModal = () => {
    addForm.reset();
    addForm.clearErrors();
    showAddModal.value = true;
};

const closeAddModal = () => {
    if (addForm.processing) return;

    showAddModal.value = false;
    addForm.reset();
    addForm.clearErrors();
};

const submitAdd = () => {
    addForm.post('/super-admin/pengguna', {
        preserveScroll: true,

        onSuccess: () => {
            closeAddModal();
            showSuccessNotification('Pengguna baru berhasil ditambahkan.');
        },

        onError: (errors) => {
            const firstError = Object.values(errors)[0];

            showErrorNotification(
                firstError || 'Pengguna gagal ditambahkan.'
            );
        },
    });
};

/* ===================================================
   EDIT PENGGUNA (nama, email, role, password opsional)
   Memakai ulang route PUT /super-admin/pengguna/{user}/role
   yang sudah ada -- backend method di route ini sekarang
   menerima name/email/password/role sekaligus.
=================================================== */

const showEditModal = ref(false);
const userSedangDiedit = ref(null);

const editForm = useForm({
    name: '',
    email: '',
    role: 'admin',
    password: '',
    password_confirmation: '',
});

const openEditModal = (user) => {
    userSedangDiedit.value = user;

    editForm.clearErrors();

    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
    editForm.password = '';
    editForm.password_confirmation = '';

    showEditModal.value = true;
};

const closeEditModal = () => {
    if (editForm.processing) return;

    showEditModal.value = false;
    userSedangDiedit.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const submitEdit = () => {
    if (!userSedangDiedit.value) return;

    if (
        userSedangDiedit.value.id === props.authUser?.id &&
        editForm.role !== userSedangDiedit.value.role
    ) {
        showErrorNotification('Role akun sendiri tidak dapat diubah.');
        return;
    }

    editForm.put(`/super-admin/pengguna/${userSedangDiedit.value.id}/role`, {
        preserveScroll: true,

        onSuccess: () => {
            closeEditModal();
            showSuccessNotification('Data pengguna berhasil diperbarui.');
        },

        onError: (errors) => {
            const firstError = Object.values(errors)[0];

            showErrorNotification(
                firstError || 'Data pengguna gagal diperbarui.'
            );
        },
    });
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

                <button
                    type="button"
                    class="add-user-button"
                    @click="openAddModal"
                >
                    <Plus :size="16" :stroke-width="2" />
                    <span>Tambah Pengguna</span>
                </button>
            </header>

            <div class="toolbar">

                <div class="search-box">
                    <Search :size="15" :stroke-width="1.8" />

                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari nama atau email..."
                    >
                </div>

                <select
                    v-model="filterRole"
                    class="filter-select"
                >
                    <option value="Semua">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>

            </div>

            <section
                v-if="props.users.length === 0"
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
                v-else-if="usersFiltered.length === 0"
                class="empty-card"
            >
                <div class="empty-icon">
                    <Search :size="26" :stroke-width="1.4" />
                </div>

                <h2>Tidak Ditemukan</h2>

                <p>
                    Tidak ada pengguna yang cocok dengan pencarian
                    atau filter yang dipilih.
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
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="user in usersFiltered"
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
                                        :class="`role-select-${user.role}`"
                                        :disabled="user.id === props.authUser?.id || roleForm.processing"
                                        @change="updateRole(user, $event.target.value)"
                                    >
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
                                    <div class="action-buttons">

                                        <button
                                            type="button"
                                            class="icon-button edit-icon-button"
                                            title="Edit pengguna"
                                            @click="openEditModal(user)"
                                        >
                                            <Pencil :size="14" :stroke-width="1.8" />
                                        </button>

                                        <button
                                            type="button"
                                            class="icon-button delete-icon-button"
                                            title="Hapus pengguna"
                                            :disabled="user.id === props.authUser?.id"
                                            @click="confirmDelete(user)"
                                        >
                                            <Trash2 :size="14" :stroke-width="1.8" />
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </section>

            <!-- Modal: Tambah Pengguna -->
            <div
                v-if="showAddModal"
                class="modal-overlay"
                @click.self="closeAddModal"
            >
                <div class="form-modal">

                    <div class="form-modal-header">
                        <h2>Tambah Pengguna</h2>

                        <button
                            type="button"
                            class="modal-close"
                            @click="closeAddModal"
                            aria-label="Tutup"
                        >
                            <X :size="18" :stroke-width="1.8" />
                        </button>
                    </div>

                    <form
                        class="user-form"
                        @submit.prevent="submitAdd"
                    >

                        <div class="form-field">
                            <label>Nama</label>

                            <input
                                type="text"
                                v-model="addForm.name"
                                placeholder="Nama lengkap"
                            >

                            <span
                                v-if="addForm.errors.name"
                                class="field-error"
                            >
                                {{ addForm.errors.name }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Email</label>

                            <input
                                type="email"
                                v-model="addForm.email"
                                placeholder="nama@email.com"
                            >

                            <span
                                v-if="addForm.errors.email"
                                class="field-error"
                            >
                                {{ addForm.errors.email }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Password</label>

                            <input
                                type="password"
                                v-model="addForm.password"
                                placeholder="Minimal 8 karakter"
                            >

                            <span
                                v-if="addForm.errors.password"
                                class="field-error"
                            >
                                {{ addForm.errors.password }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Konfirmasi Password</label>

                            <input
                                type="password"
                                v-model="addForm.password_confirmation"
                                placeholder="Ulangi password"
                            >
                        </div>

                        <div class="form-field">
                            <label>Role</label>

                            <select v-model="addForm.role">
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>

                            <span
                                v-if="addForm.errors.role"
                                class="field-error"
                            >
                                {{ addForm.errors.role }}
                            </span>
                        </div>

                        <div class="form-actions">

                            <button
                                type="button"
                                class="cancel-button"
                                @click="closeAddModal"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="submit-button"
                                :disabled="addForm.processing"
                            >
                                {{
                                    addForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan'
                                }}
                            </button>

                        </div>

                    </form>

                </div>
            </div>

            <!-- Modal: Edit Pengguna -->
            <div
                v-if="showEditModal"
                class="modal-overlay"
                @click.self="closeEditModal"
            >
                <div class="form-modal">

                    <div class="form-modal-header">
                        <h2>Edit Pengguna</h2>

                        <button
                            type="button"
                            class="modal-close"
                            @click="closeEditModal"
                            aria-label="Tutup"
                        >
                            <X :size="18" :stroke-width="1.8" />
                        </button>
                    </div>

                    <form
                        class="user-form"
                        @submit.prevent="submitEdit"
                    >

                        <div class="form-field">
                            <label>Nama</label>

                            <input
                                type="text"
                                v-model="editForm.name"
                                placeholder="Nama lengkap"
                            >

                            <span
                                v-if="editForm.errors.name"
                                class="field-error"
                            >
                                {{ editForm.errors.name }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Email</label>

                            <input
                                type="email"
                                v-model="editForm.email"
                                placeholder="nama@email.com"
                            >

                            <span
                                v-if="editForm.errors.email"
                                class="field-error"
                            >
                                {{ editForm.errors.email }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Role</label>

                            <select
                                v-model="editForm.role"
                                :disabled="userSedangDiedit?.id === props.authUser?.id"
                            >
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>

                            <span
                                v-if="userSedangDiedit?.id === props.authUser?.id"
                                class="field-hint"
                            >
                                Role akun sendiri tidak dapat diubah.
                            </span>

                            <span
                                v-else-if="editForm.errors.role"
                                class="field-error"
                            >
                                {{ editForm.errors.role }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Password Baru (opsional)</label>

                            <input
                                type="password"
                                v-model="editForm.password"
                                placeholder="Kosongkan jika tidak diubah"
                            >

                            <span
                                v-if="editForm.errors.password"
                                class="field-error"
                            >
                                {{ editForm.errors.password }}
                            </span>
                        </div>

                        <div class="form-field">
                            <label>Konfirmasi Password Baru</label>

                            <input
                                type="password"
                                v-model="editForm.password_confirmation"
                                placeholder="Ulangi password baru"
                            >
                        </div>

                        <div class="form-actions">

                            <button
                                type="button"
                                class="cancel-button"
                                @click="closeEditModal"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="submit-button"
                                :disabled="editForm.processing"
                            >
                                {{
                                    editForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Perubahan'
                                }}
                            </button>

                        </div>

                    </form>

                </div>
            </div>

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
    box-shadow: 8px 0 24px rgba(100, 130, 130, .06);
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
    border-radius: 8px;
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
    border-radius: 8px;
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
    font-family: Arial, sans-serif;
}

.main-content {
    min-height: 100vh;
    padding: 30px 35px;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
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
    border-radius: 9px;
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
    font-size: 27px;
    font-weight: normal;
    color: #526363;
}

.add-user-button {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-shrink: 0;

    padding: 11px 17px;

    border: none;
    border-radius: 8px;

    background: #6f9d9d;
    color: #ffffff;

    font-size: 11.5px;
    font-weight: 600;

    cursor: pointer;
    transition: background .2s ease;
}

.add-user-button:hover {
    background: #5c8686;
}

.toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;

    margin-bottom: 18px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 8px;

    flex: 1;
    min-width: 220px;

    padding: 10px 14px;

    border: 1px solid #d4e2e0;
    border-radius: 8px;

    background: #ffffff;

    color: #8b9999;
}

.search-box input {
    flex: 1;

    border: none;
    outline: none;

    background: transparent;

    color: #526363;
    font-size: 11.5px;
}

.search-box input::placeholder {
    color: #a9b6b6;
}

.filter-select {
    padding: 10px 12px;

    border: 1px solid #d4e2e0;
    border-radius: 8px;

    background: #ffffff;
    color: #526363;

    font-size: 11px;

    cursor: pointer;
}

.table-card {
    width: 100%;
    background: #fbfdfc;
    border: 1px solid #d7e3e1;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(50, 90, 90, .04);
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
    border-radius: 6px;
    outline: none;
    background: #ffffff;
    color: #526363;
    font-size: 10px;
    font-weight: 500;
    cursor: pointer;
}

.role-select-user {
    background: #f3f6f6;
    border-color: #dfe7e6;
    color: #6f8080;
}

.role-select-admin {
    background: #eef4fb;
    border-color: #d3e2f2;
    color: #5a7cad;
}

.role-select-super_admin {
    background: #eaf7f4;
    border-color: #cbe6de;
    color: #3f8a72;
}

.role-select:focus {
    border-color: #7eabab;
    box-shadow: 0 0 0 3px rgba(126, 171, 171, .12);
}

.role-select:disabled {
    opacity: .7;
    cursor: not-allowed;
}

.action-buttons {
    display: flex;
    align-items: center;
    gap: 6px;
}

.icon-button {
    width: 30px;
    height: 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #dce8e6;
    border-radius: 6px;

    background: #ffffff;
    color: #6f8585;

    cursor: pointer;
    transition: .2s ease;
}

.edit-icon-button:hover {
    background: #f2f9f9;
    border-color: #b9d6d6;
    color: #47807e;
}

.delete-icon-button:hover:not(:disabled) {
    background: #fff5f5;
    border-color: #efd4d4;
    color: #b87575;
}

.icon-button:disabled {
    opacity: .4;
    cursor: not-allowed;
}

.empty-card {
    min-height: 420px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    background: #fbfdfc;
    border: 1px solid #d7e3e1;
    border-radius: 10px;
    text-align: center;
}

.empty-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    border-radius: 50%;
    background: #eaf3f2;
    color: #6f9d9d;
    font-size: 28px;
}

.empty-card h2 {
    margin: 0 0 9px;
    font-family: Georgia, serif;
    font-size: 21px;
    font-weight: normal;
    color: #344747;
}

.empty-card p {
    margin: 0;
    font-size: 11px;
    color: #8b9999;
}

/* Modals */

.modal-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(24, 49, 49, .42);
    z-index: 2000;
}

.confirm-modal {
    width: 100%;
    max-width: 390px;
    padding: 30px;
    background: #fbfdfc;
    border: 1px solid #d7e3e1;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 20px 55px rgba(20, 50, 50, .16);
}

.confirm-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #eaf3f2;
    color: #477878;
    font-size: 21px;
    font-weight: 600;
}

.confirm-modal h2 {
    margin: 0 0 10px;
    font-family: Georgia, serif;
    font-size: 20px;
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
    margin-top: 24px;
}

.cancel-button,
.confirm-delete-button,
.submit-button {
    padding: 11px 20px;
    border-radius: 7px;
    font-size: 10.5px;
    font-weight: 600;
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

.submit-button {
    border: none;
    background: #6f9d9d;
    color: #ffffff;
}

.submit-button:hover:not(:disabled) {
    background: #5c8686;
}

.submit-button:disabled {
    background: #b9cccc;
    cursor: not-allowed;
}

/* Form modal (Tambah / Edit) */

.form-modal {
    width: 100%;
    max-width: 440px;
    max-height: 90vh;

    overflow-y: auto;

    padding: 26px;

    background: #fbfdfc;
    border: 1px solid #d7e3e1;
    border-radius: 12px;

    box-shadow: 0 20px 55px rgba(20, 50, 50, .16);
}

.form-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 20px;
}

.form-modal-header h2 {
    margin: 0;
    font-family: Georgia, serif;
    font-size: 19px;
    font-weight: normal;
    color: #344747;
}

.modal-close {
    width: 32px;
    height: 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 8px;

    background: transparent;
    color: #93a0a0;

    cursor: pointer;
    transition: background .2s ease;
}

.modal-close:hover {
    background: #f0f5f4;
}

.user-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-field label {
    color: #6f8080;
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .2px;
}

.form-field input,
.form-field select {
    padding: 10px 12px;

    border: 1px solid #d4e2e0;
    border-radius: 7px;

    background: #ffffff;
    color: #344747;

    font-size: 11.5px;

    outline: none;

    transition: border-color .2s ease, box-shadow .2s ease;
}

.form-field input:focus,
.form-field select:focus {
    border-color: #7eabab;
    box-shadow: 0 0 0 3px rgba(126, 171, 171, .12);
}

.form-field select:disabled {
    background: #f4f6f6;
    color: #a2adad;
    cursor: not-allowed;
}

.field-error {
    color: #b5675f;
    font-size: 9.5px;
}

.field-hint {
    color: #8b9999;
    font-size: 9.5px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;

    margin-top: 6px;
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
    border-radius: 14px;
    text-align: center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, .14);
}

.success-icon,
.error-mark {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 40px;
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
    font-size: 23px;
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
        transform: scale(.85);
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
        transform: scale(.92);
    }
}

@media (max-width: 750px) {

    .main-content {
        padding: 22px 15px;
    }

    .topbar {
        flex-wrap: wrap;
    }

    .add-user-button span {
        display: none;
    }

    .add-user-button {
        padding: 11px;
    }

    .toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-select {
        width: 100%;
    }

}
</style>