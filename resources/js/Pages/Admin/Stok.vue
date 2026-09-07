<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Menu,
    X,
    Plus,
    Package,
    CheckCircle2,
    AlertTriangle,
    XCircle,
    Pencil,
    Trash2,
    ImagePlus,
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
const logoUrl = '/images/logo-hf.png';
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
const form = useForm({
    nama: '',
    harga: '',
    harga_per_ml: '',
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
    harga_per_ml: '',
    stok: '',
    kategori: '',
    foto: null,
});

const editHargaDisplay = ref('');
const editFotoNama = ref('');
const editFotoPreview = ref(null);
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
    editForm.harga_per_ml = String(parfum.harga_per_ml ?? '');
    editForm.stok = parfum.stok;
    editForm.kategori = parfum.kategori;
    editForm.foto = null;
    editFotoNama.value = '';
    editFotoPreview.value = parfum.foto
    ? `/storage/${parfum.foto}`
    : null;
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
    editFotoPreview.value = null;
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
                    <X :size="20" />
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
                    class="menu-item active"
                >
                    <span>Kelola Stok</span>
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
                        <Menu :size="19" />
                    </button>

                    <div class="topbar-heading">
                        <h1>Kelola Stok</h1>
                    </div>

                </div>


                <button
                    type="button"
                    class="add-button"
                    @click="openModal"
                >
                    <Plus :size="16" />
                    Tambah Stok
                </button>

            </header>

            <section
                v-if="parfums.length > 0"
                class="stats-grid"
            >

                <div class="stat-card">

                    <div class="stat-icon">
                        <Package :size="18" :stroke-width="1.8" />
                    </div>

                    <div class="stat-body">
                        <span class="stat-label">Total Produk</span>
                        <strong>{{ parfums.length }}</strong>
                    </div>

                </div>

                <div class="stat-card">

                    <div class="stat-icon stat-icon--ok">
                        <CheckCircle2 :size="18" :stroke-width="1.8" />
                    </div>

                    <div class="stat-body">
                        <span class="stat-label">Stok Tersedia</span>
                        <strong>
                            {{ parfums.filter((p) => p.stok > 5).length }}
                        </strong>
                    </div>

                </div>

                <div class="stat-card">

                    <div class="stat-icon stat-icon--warn">
                        <AlertTriangle :size="18" :stroke-width="1.8" />
                    </div>

                    <div class="stat-body">
                        <span class="stat-label">Stok Menipis</span>
                        <strong>
                            {{
                                parfums.filter(
                                    (p) => p.stok > 0 && p.stok <= 5
                                ).length
                            }}
                        </strong>
                    </div>

                </div>

                <div class="stat-card">

                    <div class="stat-icon stat-icon--danger">
                        <XCircle :size="18" :stroke-width="1.8" />
                    </div>

                    <div class="stat-body">
                        <span class="stat-label">Stok Habis</span>
                        <strong>
                            {{ parfums.filter((p) => p.stok === 0).length }}
                        </strong>
                    </div>

                </div>

            </section>

        <section
            v-if="parfums.length === 0"
            class="empty-card"
        >

            <div class="empty-icon">
                <Package :size="28" :stroke-width="1.6" />
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
                <Plus :size="15" />
                Tambah Stok Parfum
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
                    <th>Harga / ml</th>
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
                        <span class="table-price">
                            {{ formatRupiah(parfum.harga_per_ml) }} / ml
                        </span>
                    </td>

                    <td>
                        <div class="stock-cell">

                            <span class="stock-number">
                                {{ parfum.stok }}
                            </span>

                            <span
                                class="stock-badge"
                                :class="{
                                    'stock-badge--ok': parfum.stok > 5,
                                    'stock-badge--warn':
                                        parfum.stok > 0 && parfum.stok <= 5,
                                    'stock-badge--danger': parfum.stok === 0,
                                }"
                            >
                                {{
                                    parfum.stok === 0
                                        ? 'Habis'
                                        : parfum.stok <= 5
                                            ? 'Menipis'
                                            : 'Aman'
                                }}
                            </span>

                        </div>
                    </td>

                    <td>
                        <div class="action-buttons">

                        <button
                            type="button"
                            class="edit-button"
                            @click="openEditModal(parfum)"
                        >
                            <Pencil :size="12" />
                            Edit
                        </button>

                        <button
                            type="button"
                            class="delete-button"
                            @click="confirmDelete(parfum)"
                        >
                            <Trash2 :size="12" />
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

        <div
            class="confirm-icon"
            :class="{ 'confirm-icon--danger': confirmType === 'delete' }"
        >
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

            <div class="stok-modal">

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
                        <X :size="18" />
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

                        <label for="harga_per_ml">
                            Harga per ml
                        </label>

                        <div class="price-input">

                            <span>
                                Rp
                            </span>

                            <input
                                id="harga_per_ml"
                                v-model="form.harga_per_ml"
                                type="number"
                                min="0"
                                placeholder="Contoh: 2000"
                            >

                        </div>

                        <span
                            v-if="form.errors.harga_per_ml"
                            class="error"
                        >
                            {{ form.errors.harga_per_ml }}
                        </span>

                        <small>
                            Harga yang digunakan untuk perhitungan berdasarkan ukuran ml.
                        </small>

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

                            <ImagePlus :size="17" />

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
                            JPG, JPEG, PNG atau WEBP.
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

    <div class="stok-modal">

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
                <X :size="18" />
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

                <label for="edit-harga-per-ml">
                    Harga per ml
                </label>

                <div class="price-input">

                    <span>
                        Rp
                    </span>

                    <input
                        id="edit-harga-per-ml"
                        v-model="editForm.harga_per_ml"
                        type="number"
                        min="0"
                        placeholder="Contoh: 2000"
                    >

                </div>

                <span
                    v-if="editForm.errors.harga_per_ml"
                    class="error"
                >
                    {{ editForm.errors.harga_per_ml }}
                </span>

                <small>
                    Harga yang digunakan berdasarkan ukuran ml.
                </small>

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
                <div
                    v-if="editFotoPreview"
                    class="edit-photo-preview"
                >
                    <img
                        :src="editFotoPreview"
                        alt="Preview foto parfum"
                    >
                </div>

                <label class="upload-box">

                    <ImagePlus :size="17" />

                    <span>
                        Pilih foto baru
                    </span>

                    <input
                        id="edit-foto"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        @change="
                        editForm.foto = $event.target.files[0];
                        editFotoNama = $event.target.files[0]?.name || '';

                        if ($event.target.files[0]) {
                            editFotoPreview = URL.createObjectURL(
                                $event.target.files[0]
                            );}"
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

    min-height: 100vh;

    background: var(--hf-bg);

    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.edit-photo-preview {
    width: 100%;
    height: 150px;

    margin-bottom: 10px;

    overflow: hidden;

    border: 1px solid var(--hf-border);
    border-radius: 10px;

    background: var(--hf-bg);
}

.edit-photo-preview img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: contain;
}

/* ---------- Sidebar ---------- */

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

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 8px;

    background: transparent;

    color: var(--hf-ink-faint);

    cursor: pointer;

    transition: background .15s ease;
}

.close-button:hover {
    background: var(--hf-sage-soft);
}

.admin-info {
    display: flex;

    align-items: center;

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

/* ---------- Main ---------- */

.main-content {
    min-height: 100vh;

    padding: 30px 4%;
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

    gap: 16px;
}

.toggle-button {
    width: 42px;
    height: 42px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid var(--hf-border);

    border-radius: 10px;

    background: #ffffff;

    color: var(--hf-green);

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

    font-size: 24px;

    font-weight: normal;

    color: var(--hf-ink);
}

.topbar p {
    margin: 3px 0 0;

    font-size: 11px;

    color: var(--hf-ink-faint);
}

.add-button,
.empty-button {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    gap: 7px;

    padding: 11px 17px;

    border: none;
    border-radius: 9px;

    background: var(--hf-green);

    color: #ffffff;

    font-size: 11.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.add-button:hover,
.empty-button:hover {
    background: var(--hf-green-dark);
}

/* ---------- Stats ---------- */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;

    margin-bottom: 20px;
}

.stat-card {
    display: flex;
    align-items: center;

    gap: 12px;

    padding: 16px;

    background: #ffffff;

    border: 1px solid var(--hf-border);
    border-radius: 12px;
}

.stat-icon {
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

.stat-icon--ok {
    background: var(--hf-sage-soft);
    color: var(--hf-green);
}

.stat-icon--warn {
    background: var(--hf-amber-bg);
    color: var(--hf-amber);
}

.stat-icon--danger {
    background: var(--hf-red-bg);
    color: var(--hf-red);
}

.stat-body {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.stat-label {
    color: var(--hf-ink-faint);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .02em;
    text-transform: uppercase;

    margin-bottom: 3px;
}

.stat-body strong {
    color: var(--hf-ink);
    font-size: 19px;
    font-weight: 700;
}

/* ---------- Empty state ---------- */

.empty-card {
    min-height: 420px;

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

.empty-icon {
    width: 66px;
    height: 66px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 18px;

    border-radius: 14px;

    background: var(--hf-sage-soft);

    color: var(--hf-green);
}

.empty-card h2 {
    margin: 0 0 8px;

    font-family: Georgia, serif;

    font-size: 20px;
    font-weight: normal;

    color: var(--hf-ink);
}

.empty-card p {
    max-width: 400px;

    margin: 0 0 20px;

    font-size: 11px;

    line-height: 1.8;

    color: var(--hf-ink-faint);
}

/* ---------- Table ---------- */

.table-card {
    width: 100%;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 12px;

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
    background: var(--hf-bg);
}

.stok-table th {
    padding: 14px 18px;

    color: var(--hf-ink-faint);

    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;

    text-align: left;

    border-bottom: 1px solid var(--hf-border);

    white-space: nowrap;
}

.stok-table td {
    padding: 14px 18px;

    color: var(--hf-ink-soft);

    border-bottom: 1px solid var(--hf-border);

    vertical-align: middle;
}

.stok-table tbody tr:last-child td {
    border-bottom: none;
}

.stok-table tbody tr:hover {
    background: var(--hf-bg);
}

.product-name {
    color: var(--hf-ink);

    font-size: 12.5px;

    font-weight: 600;
}

.table-price {
    color: var(--hf-ink);

    font-size: 11px;
    font-weight: 600;

    white-space: nowrap;
}

.stock-cell {
    display: flex;
    align-items: center;
    gap: 9px;
}

.stock-number {
    color: var(--hf-ink);
    font-size: 12px;
    font-weight: 700;
}

.stock-badge {
    padding: 4px 9px;

    border-radius: 6px;

    font-size: 9px;
    font-weight: 600;
    white-space: nowrap;
}

.stock-badge--ok {
    background: var(--hf-sage-soft);
    color: var(--hf-green);
}

.stock-badge--warn {
    background: var(--hf-amber-bg);
    color: var(--hf-amber);
}

.stock-badge--danger {
    background: var(--hf-red-bg);
    color: var(--hf-red);
}

.action-buttons {
    display: flex;

    align-items: center;

    gap: 7px;
}

.edit-button,
.delete-button {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    padding: 7px 11px;

    border-radius: 7px;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease, border-color .15s ease;
}

.edit-button {
    border: 1px solid var(--hf-border);

    background: #ffffff;

    color: var(--hf-green);
}

.edit-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.delete-button {
    border: 1px solid var(--hf-red-bg);

    background: #ffffff;

    color: var(--hf-red);
}

.delete-button:hover {
    background: var(--hf-red-bg);
}

/* ---------- Modals ---------- */

.modal-overlay {
    position: fixed;

    inset: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 25px;

    background: rgba(30, 45, 43, .45);

    z-index: 2000;
}

.stok-modal {
    width: 100%;
    max-width: 480px;
    max-height: 90vh;

    overflow-y: auto;

    padding: 26px;

    background: #ffffff;

    border: 1px solid var(--hf-border);
    border-radius: 14px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    margin-bottom: 22px;
}

.modal-header h2 {
    margin: 0 0 4px;

    font-family: Georgia, serif;

    font-size: 19px;
    font-weight: normal;

    color: var(--hf-ink);
}

.modal-header p {
    margin: 0;

    font-size: 10px;

    color: var(--hf-ink-faint);
}

.modal-close {
    width: 32px;
    height: 32px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 8px;

    background: transparent;

    color: var(--hf-ink-faint);

    cursor: pointer;

    transition: background .15s ease;
}

.modal-close:hover {
    background: var(--hf-sage-soft);
}

.form-group {
    margin-bottom: 17px;
}

.form-group label {
    display: block;

    margin-bottom: 7px;

    font-size: 10.5px;
    font-weight: 600;

    color: var(--hf-ink-soft);
}

.form-group input {
    width: 100%;

    padding: 12px 13px;

    border: 1px solid var(--hf-border);

    border-radius: 8px;

    outline: none;

    background: var(--hf-bg);

    color: var(--hf-ink);

    font-size: 11.5px;

    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
}

.form-group input:focus {
    border-color: var(--hf-green);
    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(63, 110, 105, .10);
}

.form-group input::placeholder {
    color: var(--hf-ink-faint);
}

.form-group select {
    width: 100%;

    padding: 12px 13px;

    border: 1px solid var(--hf-border);
    border-radius: 8px;

    outline: none;

    background: var(--hf-bg);

    color: var(--hf-ink);

    font-size: 11.5px;

    cursor: pointer;

    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
}

.form-group select:focus {
    border-color: var(--hf-green);
    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(63, 110, 105, .10);
}

.upload-box {
    min-height: 76px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    padding: 15px;

    border: 1px dashed var(--hf-sage);

    border-radius: 9px;

    background: var(--hf-sage-soft);

    color: var(--hf-green);

    font-size: 10.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.upload-box:hover {
    background: #e4efec;
}

.upload-box input {
    display: none;
}

.form-group small {
    display: block;

    margin-top: 6px;

    font-size: 8.5px;

    color: var(--hf-ink-faint);
}

.price-input {
    display: flex;
    align-items: center;

    border: 1px solid var(--hf-border);

    border-radius: 8px;

    background: var(--hf-bg);

    overflow: hidden;
}

.price-input span {
    padding-left: 13px;

    color: var(--hf-ink-faint);

    font-size: 11px;
    font-weight: 600;
}

.price-input input {
    border: none;

    border-radius: 0;

    background: transparent;

    box-shadow: none;
}

.price-input input:focus {
    background: transparent;
    box-shadow: none;
}

.error {
    display: block;

    margin-top: 5px;

    color: var(--hf-red);

    font-size: 9.5px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;

    gap: 10px;

    margin-top: 22px;
}

.cancel-button,
.save-button {
    padding: 11px 18px;

    border-radius: 8px;

    font-size: 10.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.cancel-button {
    border: 1px solid var(--hf-border);

    background: #ffffff;

    color: var(--hf-ink-soft);
}

.cancel-button:hover {
    background: var(--hf-bg);
}

.save-button {
    border: none;

    background: var(--hf-green);

    color: white;
}

.save-button:hover {
    background: var(--hf-green-dark);
}

.save-button:disabled {
    opacity: 0.6;

    cursor: not-allowed;
}

/* ---------- Confirm modal ---------- */

.confirm-modal {
    width: 100%;
    max-width: 380px;

    padding: 28px;

    background: #ffffff;

    border: 1px solid var(--hf-border);

    border-radius: 14px;

    text-align: center;
}

.confirm-icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin: 0 auto 15px;

    border-radius: 50%;

    background: var(--hf-sage-soft);

    color: var(--hf-green);

    font-size: 20px;

    font-weight: 700;
}

.confirm-icon--danger {
    background: var(--hf-red-bg);
    color: var(--hf-red);
}

.confirm-modal h2 {
    margin: 0 0 9px;

    font-family: Georgia, serif;

    font-size: 19px;

    font-weight: normal;

    color: var(--hf-ink);
}

.confirm-modal p {
    margin: 0 auto;

    max-width: 290px;

    font-size: 11px;

    line-height: 1.7;

    color: var(--hf-ink-faint);
}

.confirm-actions {
    display: flex;

    justify-content: center;

    gap: 10px;

    margin-top: 22px;
}

.confirm-edit-button,
.confirm-delete-button {
    padding: 11px 20px;

    border: none;

    border-radius: 8px;

    font-size: 10.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease;
}

.confirm-edit-button {
    background: var(--hf-green);

    color: white;
}

.confirm-edit-button:hover {
    background: var(--hf-green-dark);
}

.confirm-delete-button {
    background: var(--hf-red);

    color: white;
}

.confirm-delete-button:hover {
    background: #9c433f;
}

.confirm-edit-button:disabled,
.confirm-delete-button:disabled {
    opacity: 0.6;

    cursor: not-allowed;
}

/* ---------- Success / error overlays ---------- */

.success-overlay {
    position: fixed;
    inset: 0;

    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(30, 45, 43, .28);

    backdrop-filter: blur(3px);
}

.success-modal {
    width: 320px;

    padding: 34px 28px;

    background: #ffffff;

    border-radius: 16px;

    text-align: center;

    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
}

.success-icon {
    width: 76px;
    height: 76px;

    margin: 0 auto 16px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.checkmark {
    width: 76px;
    height: 76px;
}

.check-circle {
    stroke: var(--hf-green);

    stroke-width: 2;

    stroke-dasharray: 151;
    stroke-dashoffset: 151;

    animation: circleDraw 0.5s ease forwards;
}

.check-line {
    stroke: var(--hf-green);

    stroke-width: 3.5;

    stroke-linecap: round;
    stroke-linejoin: round;

    stroke-dasharray: 40;
    stroke-dashoffset: 40;

    animation: checkDraw 0.45s ease 0.45s forwards;
}

.success-modal h3 {
    margin: 0 0 7px;

    font-family: Georgia, serif;

    font-size: 22px;

    font-weight: normal;

    color: var(--hf-ink);
}

.success-modal p {
    margin: 0;

    font-size: 12.5px;

    color: var(--hf-ink-faint);
}

.selected-file {
    display: block;

    margin-top: 8px;

    padding: 8px 10px;

    border-radius: 7px;

    background: var(--hf-sage-soft);

    color: var(--hf-green);

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
        transform: scale(0.9);
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
        transform: scale(0.92);
    }

}

.error-circle {
    stroke: var(--hf-red);
    stroke-width: 2;

    stroke-dasharray: 151;
    stroke-dashoffset: 151;

    animation: circleError 0.5s ease forwards;
}

.error-line {
    stroke: var(--hf-red);
    stroke-width: 3.5;

    stroke-linecap: round;

    stroke-dasharray: 50;
    stroke-dashoffset: 50;

    animation: errorDraw 0.4s ease 0.4s forwards;
}

.error-modal h3 {
    color: var(--hf-red);
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

@media (max-width: 900px) {

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 750px) {

    .main-content {
        padding: 20px 15px;
    }

    .topbar {
        flex-wrap: wrap;
    }

    .stok-table {
        min-width: 620px;
    }

}

@media (max-width: 500px) {

    .topbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .add-button {
        width: 100%;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stok-modal,
    .confirm-modal {
        padding: 20px;
    }

}
</style>