<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Menu,
    X,
} from 'lucide-vue-next';
import { ref } from 'vue';

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
const showSuccess = ref(false);
const successMessage = ref('');

const showError = ref(false);
const errorMessage = ref('');

const showModal = ref(false);
const sidebarOpen = ref(false);
const logoUrl = '/images/logo.jpg';
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
const form = useForm({
    nama: '',
    harga: '',
    stok: '',
    kategori: '',
    foto: null,
});

const hargaDisplay = ref('');
const fotoNama = ref('');
const showEditModal = ref(false);
const editParfum = ref(null);

const editForm = useForm({
    nama: '',
    harga: '',
    stok: '',
    kategori: '',
    foto: null,
});

const editHargaDisplay = ref('');
const editFotoNama = ref('');
const showConfirm = ref(false);
const confirmType = ref(null);
const selectedParfum = ref(null);

const openModal = () => {
    form.reset();
    form.clearErrors();

    hargaDisplay.value = '';
    fotoNama.value = '';

    showModal.value = true;
};

const closeModal = () => {

    showModal.value = false;

    form.reset();
    form.clearErrors();

    hargaDisplay.value = '';
    fotoNama.value = '';
};

const submit = () => {
    
    form.post('/admin/stok', {
        forceFormData: true,

        onSuccess: () => {

            closeModal();

            showSuccessNotification(
                'Stok parfum berhasil ditambahkan.'
            );
        },

        onError: (errors) => {

            const firstError = Object.values(errors)[0];

            showErrorNotification(
                firstError || 'Stok parfum gagal ditambahkan.'
            );
        },
    });
};

const openEditModal = (parfum) => {

    editParfum.value = parfum;

    editForm.clearErrors();

    editForm.nama = parfum.nama;
    editForm.harga = String(parfum.harga);
    editForm.stok = parfum.stok;
    editForm.kategori = parfum.kategori;
    editForm.foto = null;
    editFotoNama.value = '';
    editHargaDisplay.value =
        Number(parfum.harga).toLocaleString('id-ID');

    showEditModal.value = true;
};
const confirmSubmitEdit = () => {
    if (!editParfum.value) return;

    selectedParfum.value = editParfum.value;
    confirmType.value = 'edit';
    showConfirm.value = true;
};

const closeEditModal = () => {

    showEditModal.value = false;

    editForm.reset();
    editForm.clearErrors();

    editHargaDisplay.value = '';
    editFotoNama.value = '';
    editParfum.value = null;
    selectedParfum.value = null;
};

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
const submitEdit = () => {

    if (!editParfum.value) return;

    editForm
        .transform((data) => ({
            ...data,
            _method: 'PUT',
        }))
        .post(`/admin/stok/${editParfum.value.id}`, {

            forceFormData: true,

            onSuccess: () => {

                showConfirm.value = false;
                confirmType.value = null;
                selectedParfum.value = null;

                closeEditModal();

                showSuccessNotification(
                    'Data parfum berhasil diperbarui.'
                );
            },

            onError: (errors) => {

                const firstError = Object.values(errors)[0];

                showErrorNotification(
                    firstError || 'Data parfum gagal diperbarui.'
                );
            },
        });
};

const confirmDelete = (parfum) => {
    selectedParfum.value = parfum;
    confirmType.value = 'delete';
    showConfirm.value = true;
};

const deleteParfum = () => {

    if (!selectedParfum.value) return;

    form.delete(
        `/admin/stok/${selectedParfum.value.id}`,
        {
            onSuccess: () => {

                showConfirm.value = false;
                confirmType.value = null;
                selectedParfum.value = null;

                showSuccessNotification(
                    'Parfum berhasil dihapus.'
                );
            },

            onError: () => {

                showErrorNotification(
                    'Parfum gagal dihapus.'
                );
            },
        }
    );
};

const handleConfirm = () => {

    if (!selectedParfum.value) return;
    if (confirmType.value === 'edit') {

        submitEdit();

        return;
    }
    if (confirmType.value === 'delete') {

        deleteParfum();

        return;
    }
};

const closeConfirm = () => {

    if (form.processing || editForm.processing) return;

    showConfirm.value = false;
    confirmType.value = null;
    selectedParfum.value = null;
};
const formatHargaInput = (value) => {

    const angka = String(value).replace(/\D/g, '');

    if (!angka) {
        hargaDisplay.value = '';
        form.harga = '';
        return;
    }

    hargaDisplay.value =
        Number(angka).toLocaleString('id-ID');

    form.harga = angka;
};

const formatEditHargaInput = (value) => {

    const angka = String(value).replace(/\D/g, '');

    if (!angka) {
        editHargaDisplay.value = '';
        editForm.harga = '';
        return;
    }

    editHargaDisplay.value =
        Number(angka).toLocaleString('id-ID');

    editForm.harga = angka;
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>

    <Head title="Kelola Stok" />

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
                    class="menu-item"
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

                        <h1>
                            Kelola Stok
                        </h1>

                        <p>
                            Kelola persediaan parfum Lamore Perfumes.
                        </p>

                    </div>

                </div>


                <button
                    type="button"
                    class="add-button"
                    @click="openModal"
                >
                    <span>+</span>
                    Tambah Stok
                </button>

            </header>

        <section
            v-if="parfums.length === 0"
            class="empty-card"
        >

            <div class="empty-icon">
                ♡
            </div>

            <h2>Belum Ada Stok Parfum</h2>

            <p>
                Belum ada parfum yang tersimpan.
                Tambahkan produk pertama untuk mulai mengelola stok.
            </p>

            <button
                type="button"
                class="empty-button"
                @click="openModal"
            >
                + Tambah Stok Parfum
            </button>

        </section>

        <section
    v-else
    class="table-card"
>

    <div class="table-wrapper">

        <table class="stok-table">

            <thead>
                <tr>
                    <th>Nama Parfum</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <tr
                    v-for="parfum in parfums"
                    :key="parfum.id"
                >

                    <td>
                        <strong class="product-name">
                            {{ parfum.nama }}
                        </strong>
                    </td>

                    <td>
                        <span class="table-price">
                            {{ formatRupiah(parfum.harga) }}
                        </span>
                    </td>

                    <td>
                        <span class="stock-number">
                            {{ parfum.stok }}
                        </span>
                    </td>

                    <td>
                        <div class="action-buttons">

                        <button
                            type="button"
                            class="edit-button"
                            @click="openEditModal(parfum)"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="delete-button"
                            @click="confirmDelete(parfum)"
                        >
                            Hapus
                        </button>

                        </div>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>

<div
    v-if="showConfirm"
    class="modal-overlay"
>
    <div class="confirm-modal">

        <div class="confirm-icon">
            {{ confirmType === 'delete' ? '!' : '?' }}
        </div>

        <h2>
            {{
                confirmType === 'delete'
                    ? 'Hapus Parfum?'
                    : 'Ubah Parfum?'
            }}
        </h2>

        <p v-if="selectedParfum">
            {{
                confirmType === 'delete'
                    ? `Apakah anda ingin menghapus parfum "${selectedParfum.nama}"?`
                    : `Apakah anda ingin merubah parfum "${selectedParfum.nama}"?`
            }}
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
                :class="
                    confirmType === 'delete'
                        ? 'confirm-delete-button'
                        : 'confirm-edit-button'
                "
                @click="handleConfirm"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Memproses...' : 'Oke' }}
            </button>

        </div>

    </div>
</div>

        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="closeModal"
        >

            <div class="modal">

                <div class="modal-header">

                    <div>
                        <h2>Tambah Stok Parfum</h2>
                        <p>
                            Masukkan informasi produk parfum.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        @click="closeModal"
                    >
                        ×
                    </button>

                </div>

                <form
                    class="stok-form"
                    @submit.prevent="submit"
                >

                    <div class="form-group">

                        <label for="nama">
                            Nama Parfum
                        </label>

                        <input
                            id="nama"
                            v-model="form.nama"
                            type="text"
                            placeholder="Masukkan nama parfum"
                        >

                        <span
                            v-if="form.errors.nama"
                            class="error"
                        >
                            {{ form.errors.nama }}
                        </span>

                    </div>

                    <div class="form-group">

                        <label for="harga">
                            Harga
                        </label>

                        <div class="price-input">

                            <span>
                                Rp
                            </span>

                        <input
                            id="harga"
                            :value="hargaDisplay"
                            @input="formatHargaInput($event.target.value)"
                            type="text"
                            inputmode="numeric"
                            placeholder="Masukkan harga"
                        >

                        </div>

                        <span
                            v-if="form.errors.harga"
                            class="error"
                        >
                            {{ form.errors.harga }}
                        </span>

                    </div>

                    <div class="form-group">

                        <label for="stok">
                            Jumlah Stok
                        </label>

                        <input
                            id="stok"
                            v-model="form.stok"
                            type="number"
                            min="0"
                            placeholder="Masukkan jumlah"
                        >

                        <span
                            v-if="form.errors.stok"
                            class="error"
                        >
                            {{ form.errors.stok }}
                        </span>

                    </div>
                    <div class="form-group">

                        <label for="foto">
                            Foto Parfum
                        </label>

                        <label class="upload-box">

                            <span>
                                Pilih foto 
                            </span>

                            <input
                                id="foto"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                @change="
                                    form.foto = $event.target.files[0];
                                    fotoNama = $event.target.files[0]?.name || '';
                                "
                                
                            >

                        </label>
                        <span
                            v-if="fotoNama"
                            class="selected-file"
                        >
                        {{ fotoNama }}
                        </span>

                        <small>
                            JPG, JPEG, PNG atau WEBP. Maksimal 2 MB.
                        </small>

                        <span
                            v-if="form.errors.foto"
                            class="error"
                        >
                            {{ form.errors.foto }}
                        </span>

                    </div>
                    <div class="form-group">

                    <label for="kategori">
                        Kategori
                    </label>

                    <select
                        id="kategori"
                        v-model="form.kategori"
                        class="form-input"
                    >
                        <option value="" disabled>
                            Pilih kategori
                        </option>

                        <option value="Pria">
                            Pria
                        </option>

                        <option value="Wanita">
                            Wanita
                        </option>

                        <option value="Unisex">
                            Unisex
                        </option>

                        <option value="Parfum Lain">
                            Parfum Lain
                        </option>
                    </select>

                    <span
                        v-if="form.errors.kategori"
                        class="error"
                    >
                        {{ form.errors.kategori }}
                    </span>

                </div>

                    <div class="modal-actions">

                    <button
                        type="button"
                        class="cancel-button"
                        @click="closeModal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="save-button"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Stok' }}
                    </button>

                </div>


                </form>

            </div>

        </div>
        <div
            v-if="showEditModal"
            class="modal-overlay"
            @click.self="closeEditModal"
        >

    <div class="modal">

        <div class="modal-header">

            <div>

                <h2>
                    Edit Stok Parfum
                </h2>

                <p>
                    Ubah informasi produk parfum.
                </p>

            </div>

            <button
                type="button"
                class="modal-close"
                @click="closeEditModal"
            >
                ×
            </button>

        </div>

        <form
            class="stok-form"
            @submit.prevent="submitEdit"
        >
            <div class="form-group">

                <label for="edit-nama">
                    Nama Parfum
                </label>

                <input
                    id="edit-nama"
                    v-model="editForm.nama"
                    type="text"
                    placeholder="Contoh: Dior Sauvage"
                >

                <span
                    v-if="editForm.errors.nama"
                    class="error"
                >
                    {{ editForm.errors.nama }}
                </span>

            </div>
            <div class="form-group">

                <label for="edit-harga">
                    Harga
                </label>

                <div class="price-input">

                    <span>
                        Rp
                    </span>

                    <input
                        id="edit-harga"
                        :value="editHargaDisplay"
                        @input="
                            formatEditHargaInput(
                                $event.target.value
                            )
                        "
                        type="text"
                        inputmode="numeric"
                        placeholder="Masukkan harga"
                    >

                </div>

                <span
                    v-if="editForm.errors.harga"
                    class="error"
                >
                    {{ editForm.errors.harga }}
                </span>

            </div>
            <div class="form-group">

                <label for="edit-stok">
                    Jumlah Stok
                </label>

                <input
                    id="edit-stok"
                    v-model="editForm.stok"
                    type="number"
                    min="0"
                    placeholder="Contoh: 10"
                >

                <span
                    v-if="editForm.errors.stok"
                    class="error"
                >
                    {{ editForm.errors.stok }}
                </span>

            </div>
            <div class="form-group">

                <label for="edit-foto">
                    Foto Parfum
                </label>

                <label class="upload-box">

                    <span>
                        Pilih foto baru
                    </span>

                    <input
                        id="edit-foto"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        @change="editForm.foto = $event.target.files[0]
                        editFotoNama = $event.target.files[0]?.name || '';
                        "
                    >

                </label>
                <span
                    v-if="editFotoNama"
                    class="selected-file"
                >
                {{ editFotoNama }}
                </span>

                <span
                    v-if="editForm.errors.foto"
                    class="error"
                >
                    {{ editForm.errors.foto }}
                </span>

            </div>
            <div class="form-group">

                <label for="edit-kategori">
                    Kategori
                </label>

                <select
                    id="edit-kategori"
                    v-model="editForm.kategori"
                    class="form-input"
                >
                    <option value="" disabled>
                        Pilih kategori
                    </option>

                    <option value="Pria">
                        Pria
                    </option>

                    <option value="Wanita">
                        Wanita
                    </option>

                    <option value="Unisex">
                        Unisex
                    </option>

                    <option value="Parfum Lain">
                        Parfum Lain
                    </option>
                </select>

                <span
                    v-if="editForm.errors.kategori"
                    class="error"
                >
                    {{ editForm.errors.kategori }}
                </span>

            </div>
            <div class="modal-actions">

                <button
                    type="button"
                    class="cancel-button"
                    @click="closeEditModal"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="save-button"
                    :disabled="editForm.processing"
                >
                    {{
                        editForm.processing
                            ? 'Memperbarui...'
                            : 'Simpan Perubahan'
                    }}
                </button>

            </div>

        </form>

    </div>

</div>

<Transition name="success">

    <div
        v-if="showSuccess"
        class="success-overlay"
    >

        <div class="success-modal">

            <div class="success-icon">

                <svg
                    viewBox="0 0 52 52"
                    class="checkmark"
                >

                    <circle
                        cx="26"
                        cy="26"
                        r="24"
                        fill="none"
                        class="check-circle"
                    />

                    <path
                        d="M14 27 L22 35 L39 18"
                        fill="none"
                        class="check-line"
                    />

                </svg>

            </div>

            <h3>
                Berhasil!
            </h3>

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

            <div class="success-icon">

                <svg
                    viewBox="0 0 52 52"
                    class="checkmark"
                >

                    <circle
                        cx="26"
                        cy="26"
                        r="24"
                        fill="none"
                        class="error-circle"
                    />

                    <path
                        d="M17 17 L35 35 M35 17 L17 35"
                        fill="none"
                        class="error-line"
                    />

                </svg>

            </div>

            <h3>
                Gagal!
            </h3>

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

.logout-button:disabled {
    opacity: .6;

    cursor: not-allowed;
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

    transition: 0.2s;

    box-shadow:
        0 5px 18px
        rgba(100, 130, 130, 0.04);
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

.add-button,
.empty-button {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    padding: 12px 18px;

    border: none;
    border-radius: 9px;

    background: #477878;

    color: #ffffff;

    font-size: 11px;

    cursor: pointer;

    transition: 0.2s;
}

.add-button:hover,
.empty-button:hover {
    background: #386666;
}

.add-button span {
    font-size: 18px;
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

    box-shadow: 0 8px 25px rgba(50, 90, 90, 0.05);
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
    max-width: 420px;

    margin: 0 0 22px;

    font-size: 11px;

    line-height: 1.8;

    color: #8b9999;
}

.table-card {
    width: 100%;

    background: #fbfdfc;

    border: 1px solid #d7e3e1;

    border-radius: 14px;

    box-shadow: 0 8px 25px rgba(50, 90, 90, 0.05);

    overflow: hidden;
}

.table-wrapper {
    width: 100%;

    overflow-x: auto;
}

.stok-table {
    width: 100%;

    border-collapse: collapse;

    font-size: 11px;
}

.stok-table thead {
    background: #f2f7f6;
}

.stok-table th {
    padding: 15px 18px;

    color: #6f8585;

    font-size: 10px;

    font-weight: 600;

    text-align: left;

    border-bottom: 1px solid #dce8e6;

    white-space: nowrap;
}

.stok-table td {
    padding: 14px 18px;

    color: #526363;

    border-bottom: 1px solid #e8efee;

    vertical-align: middle;
}
.stok-table th:nth-child(4),
.stok-table td:nth-child(4) {
    width: 260px;
    padding-left: 50px;
}

.stok-table tbody tr:last-child td {
    border-bottom: none;
}

.stok-table tbody tr:hover {
    background: #f8fbfa;
}

.product-name {
    color: #344747;

    font-family: Georgia, serif;

    font-size: 13px;

    font-weight: normal;
}

.table-price {
    color: #477878;

    font-size: 11px;

    font-weight: 600;

    white-space: nowrap;
}

.stock-number {
    color: #555;
    font-size: 11px;
    font-weight: 600;
}

.action-buttons {
    display: flex;

    align-items: center;

    gap: 7px;
}

.edit-button,
.delete-button {
    padding: 7px 11px;

    border-radius: 6px;

    font-size: 9px;

    cursor: pointer;

    transition: .2s;
}

.edit-button {
    border: 1px solid #cbdedc;

    background: #ffffff;

    color: #477878;
}

.edit-button:hover {
    background: #edf5f4;
}

.delete-button {
    border: 1px solid #efd4d4;

    background: #ffffff;

    color: #b87575;
}

.delete-button:hover {
    background: #fff5f5;
}

.modal-overlay {
    position: fixed;

    inset: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 25px;

    background: rgba(24, 49, 49, 0.45);

    z-index: 2000;
}

.modal {
    width: 100%;
    max-width: 480px;
    max-height: 90vh;

    overflow-y: auto;

    padding: 28px;

    background: #fbfdfc;

    border: 1px solid #d7e3e1;

    border-radius: 17px;

    box-shadow: 0 25px 70px rgba(20, 50, 50, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    margin-bottom: 25px;
}

.modal-header h2 {
    margin: 0 0 5px;

    font-family: Georgia, serif;

    font-size: 21px;
    font-weight: normal;

    color: #344747;
}

.modal-header p {
    margin: 0;

    font-size: 10px;

    color: #899999;
}

.modal-close {
    width: 32px;
    height: 32px;

    border: none;
    border-radius: 8px;

    background: transparent;

    color: #899999;

    font-size: 24px;

    cursor: pointer;
}

.modal-close:hover {
    background: #edf3f2;
}
.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-size: 10px;

    color: #657575;
}

.form-group input {
    width: 100%;

    padding: 12px 13px;

    border: 1px solid #d4e2e0;

    border-radius: 8px;

    outline: none;

    background: #ffffff;

    color: #526363;

    font-size: 11px;

    transition: 0.2s;
}

.form-group input:focus {
    border-color: #7eabab;

    box-shadow: 0 0 0 3px rgba(126, 171, 171, 0.12);
}

.form-group input::placeholder {
    color: #b0bbbb;
}
.form-group select {
    width: 100%;

    padding: 12px 13px;

    border: 1px solid #d4e2e0;
    border-radius: 8px;

    outline: none;

    background: #ffffff;

    color: #526363;

    font-size: 11px;

    cursor: pointer;

    transition: 0.2s;
}

.form-group select:focus {
    border-color: #7eabab;

    box-shadow: 0 0 0 3px rgba(126, 171, 171, 0.12);
}

.upload-box {
    min-height: 80px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 9px;

    padding: 15px;

    border: 1px dashed #abc4c2;

    border-radius: 9px;

    background: #f6faf9;

    color: #779292;

    font-size: 10px;

    cursor: pointer;
}

.upload-box:hover {
    background: #edf5f4;
}

.upload-box input {
    display: none;
}

.upload-icon {
    font-size: 20px;
}

.form-group small {
    display: block;

    margin-top: 6px;

    font-size: 8px;

    color: #a0adad;
}

.price-input {
    display: flex;
    align-items: center;

    border: 1px solid #d4e2e0;

    border-radius: 8px;

    background: #ffffff;

    overflow: hidden;
}

.price-input span {
    padding-left: 13px;

    color: #819090;

    font-size: 11px;
}

.price-input input {
    border: none;

    border-radius: 0;

    box-shadow: none;
}

.error {
    display: block;

    margin-top: 5px;

    color: #c87878;

    font-size: 9px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;

    gap: 10px;

    margin-top: 25px;
}

.cancel-button,
.save-button {
    padding: 11px 18px;

    border-radius: 8px;

    font-size: 10px;

    cursor: pointer;

    transition: 0.2s;
}

.cancel-button {
    border: 1px solid #d5e2e0;

    background: #ffffff;

    color: #788888;
}

.cancel-button:hover {
    background: #f0f5f4;
}

.save-button {
    border: none;

    background: #477878;

    color: white;
}

.save-button:hover {
    background: #386666;
}

.save-button:disabled {
    opacity: 0.6;

    cursor: not-allowed;
}

.confirm-modal {
    width: 100%;
    max-width: 390px;

    padding: 30px;

    background: #fbfdfc;

    border: 1px solid #d7e3e1;

    border-radius: 17px;

    text-align: center;

    box-shadow: 0 25px 70px rgba(20, 50, 50, 0.2);
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

.confirm-edit-button,
.confirm-delete-button {
    padding: 11px 20px;

    border: none;

    border-radius: 8px;

    font-size: 10px;

    cursor: pointer;

    transition: 0.2s;
}

.confirm-edit-button {
    background: #477878;

    color: white;
}

.confirm-edit-button:hover {
    background: #386666;
}

.confirm-delete-button {
    background: #b87575;

    color: white;
}

.confirm-delete-button:hover {
    background: #a46262;
}

.confirm-edit-button:disabled,
.confirm-delete-button:disabled {
    opacity: 0.6;

    cursor: not-allowed;
}

.success-overlay {
    position: fixed;
    inset: 0;

    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(20, 40, 40, 0.25);

    backdrop-filter: blur(3px);
}

.success-modal {
    width: 330px;

    padding: 35px 30px;

    background: #ffffff;

    border-radius: 20px;

    text-align: center;

    box-shadow:
        0 20px 50px rgba(0, 0, 0, 0.15);
}
.success-icon {
    width: 80px;
    height: 80px;

    margin: 0 auto 18px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.checkmark {
    width: 80px;
    height: 80px;
}

.check-circle {
    stroke: #62b66a;

    stroke-width: 2;

    stroke-dasharray: 151;
    stroke-dashoffset: 151;

    animation:
        circleDraw 0.5s ease forwards;
}

.check-line {
    stroke: #62b66a;

    stroke-width: 3.5;

    stroke-linecap: round;
    stroke-linejoin: round;

    stroke-dasharray: 40;
    stroke-dashoffset: 40;

    animation:
        checkDraw 0.45s ease 0.45s forwards;
}

.success-modal h3 {
    margin: 0 0 8px;

    font-family: Georgia, serif;

    font-size: 25px;

    font-weight: normal;

    color: #31504f;
}

.success-modal p {
    margin: 0;

    font-size: 13px;

    color: #819393;
}
.selected-file {
    display: block;

    margin-top: 8px;

    padding: 8px 10px;

    border-radius: 7px;

    background: #edf5f4;

    color: #477878;

    font-size: 9px;

    word-break: break-all;
}

@keyframes circleDraw {

    from {
        stroke-dashoffset: 151;
    }

    to {
        stroke-dashoffset: 0;
    }

}

@keyframes checkDraw {

    from {
        stroke-dashoffset: 40;
    }

    to {
        stroke-dashoffset: 0;
    }

}

.success-enter-active {
    animation: successIn 0.3s ease;
}

.success-leave-active {
    animation: successOut 0.25s ease;
}

@keyframes successIn {

    from {
        opacity: 0;
        transform: scale(0.8);
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
        transform: scale(0.9);
    }

}

.error-circle {
    stroke: #d96b6b;
    stroke-width: 2;

    stroke-dasharray: 151;
    stroke-dashoffset: 151;

    animation: circleError 0.5s ease forwards;
}

.error-line {
    stroke: #d96b6b;
    stroke-width: 3.5;

    stroke-linecap: round;

    stroke-dasharray: 50;
    stroke-dashoffset: 50;

    animation: errorDraw 0.4s ease 0.4s forwards;
}

.error-modal h3 {
    color: #b75c5c;
}

@keyframes circleError {

    from {
        stroke-dashoffset: 151;
    }

    to {
        stroke-dashoffset: 0;
    }
}

@keyframes errorDraw {

    from {
        stroke-dashoffset: 50;
    }

    to {
        stroke-dashoffset: 0;
    }
}

.error-enter-active {
    animation: successIn 0.3s ease;
}

.error-leave-active {
    animation: successOut 0.25s ease;
}

@media (max-width: 750px) {
    .stok-page {
        padding: 22px 15px;
    }

    .page-header {
        align-items: flex-start;
        gap: 15px;
    }

    .stok-table {
        min-width: 600px;
    }
}

@media (max-width: 500px) {
    .page-header {
        flex-direction: column;
    }

    .add-button {
        width: 100%;
    }

    .modal {
        padding: 22px;
    }
}
</style>