<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ArrowLeft, Minus, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    alamat: {
    type: Object,
    default: null,
    },
    authUser: {
        type: Object,
        default: null,
    },
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const selectedIds = ref(
    props.items.map(item => item.id)
);
const metodePembayaran = ref('cod');

const jumlahMap = ref(
    Object.fromEntries(
        props.items.map(item => [
            item.id,
            item.jumlah,
        ])
    )
);

const toggleItem = (id) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(
            itemId => itemId !== id
        );
    } else {
        selectedIds.value.push(id);
    }
};

const semuaDipilih = computed(() => {
    return props.items.length > 0 &&
        selectedIds.value.length === props.items.length;
});

const toggleSemua = () => {
    if (semuaDipilih.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.items.map(item => item.id);
    }
};

const tambahJumlah = (item) => {
    const jumlahBaru = jumlahMap.value[item.id] + 1;

    if (jumlahBaru > item.parfum?.stok) {
        return;
    }

    jumlahMap.value[item.id] = jumlahBaru;

    router.put(
        `/pelanggan/keranjang/${item.id}`,
        {
            jumlah: jumlahBaru,
        },
        {
            preserveScroll: true,
        }
    );
};

const kurangiJumlah = (item) => {
    const jumlahBaru = jumlahMap.value[item.id] - 1;

    if (jumlahBaru < 1) {
        return;
    }

    jumlahMap.value[item.id] = jumlahBaru;

    router.put(
        `/pelanggan/keranjang/${item.id}`,
        {
            jumlah: jumlahBaru,
        },
        {
            preserveScroll: true,
        }
    );
};
const hapusItem = (item) => {
    router.delete(
        `/pelanggan/keranjang/${item.id}`,
        {
            preserveScroll: true,
        }
    );
};

const totalHarga = computed(() => {
    return props.items
        .filter(item => selectedIds.value.includes(item.id))
        .reduce((total, item) => {
            const jumlah = jumlahMap.value[item.id] ?? item.jumlah;

            return total + (
                Number(item.parfum?.harga || 0) * jumlah
            );
        }, 0);
});
const ongkir = computed(() => {
    return selectedIds.value.length > 0 ? 25000 : 0;
});

const totalBayar = computed(() => {
    return totalHarga.value + ongkir.value;
});


const jumlahDipilih = computed(() => {
    return props.items
        .filter(item => selectedIds.value.includes(item.id))
        .reduce((total, item) => {
            return total + (
                jumlahMap.value[item.id] ?? item.jumlah
            );
        }, 0);
});
const checkout = () => {
    if (selectedIds.value.length === 0) {
        return;
    }

    router.post(
        '/pelanggan/keranjang/checkout',
        {
            item_ids: selectedIds.value,
            metode_pembayaran: metodePembayaran.value,
        },
        {
            preserveScroll: true,
        }
    );
};

</script>

<template>
    <Head title="Keranjang" />

    <div class="cart-page">

        <header class="cart-header">

            <Link
                href="/Pelanggan"
                class="back-button"
            >
                <ArrowLeft :size="21" />
            </Link>

            <div class="header-title">
                <h1>Keranjang Saya</h1>

                <span>
                    {{ props.items.length }} produk
                </span>
            </div>

        </header>

        <main class="cart-container">
            <section class="address-section">

    <div class="address-heading">

        <div>
            <h2>Alamat Pengiriman</h2>
            <p>Alamat tujuan pesananmu</p>
        </div>
    </div>

    <div
        v-if="props.alamat"
        class="address-filled"
    >
        <div class="address-main">
            <strong>
                {{ props.alamat.nama_penerima }}
            </strong>

            <span>
                {{ props.alamat.no_hp }}
            </span>
        </div>

        <p>
            {{ props.alamat.alamat_lengkap }},
            {{ props.alamat.kecamatan }},
            {{ props.alamat.kabupaten_kota }},
            {{ props.alamat.provinsi }},
            {{ props.alamat.kode_pos }}
        </p>

        <Link
            href="/pelanggan/alamat"
            class="address-edit"
        >
            Ubah
        </Link>
    </div>

    <div
        v-else
        class="address-empty"
    >
        <div>
            <strong>Belum ada alamat</strong>

            <p>
                Tambahkan alamat pengiriman sebelum checkout.
            </p>
        </div>

        <Link
            href="/pelanggan/alamat"
            class="address-add"
        >
            Tambah Alamat
        </Link>
    </div>

        </section>

            <div
                v-if="props.items.length === 0"
                class="empty-cart"
            >
                <div class="empty-icon">
                    🛒
                </div>

                <h2>Keranjang masih kosong</h2>

                <p>
                    Yuk pilih parfum favoritmu terlebih dahulu.
                </p>

                <Link
                    href="/Pelanggan"
                    class="shop-button"
                >
                    Pilih Parfum
                </Link>
            </div>

            <div
                v-else
                class="cart-content"
            >

                <div class="cart-toolbar">

                    <button
                        type="button"
                        class="select-all"
                        @click="toggleSemua"
                    >
                        <span
                            class="checkbox"
                            :class="{ checked: semuaDipilih }"
                        >
                            <span v-if="semuaDipilih">✓</span>
                        </span>

                        <span>
                            Pilih Semua
                        </span>
                    </button>

                    <span class="cart-user">
                        {{ props.authUser?.name }}
                    </span>

                </div>


                <div class="cart-list">

                    <article
                        v-for="item in props.items"
                        :key="item.id"
                        class="cart-item"
                    >
                        <button
                            type="button"
                            class="item-check"
                            @click="toggleItem(item.id)"
                        >
                            <span
                                class="checkbox"
                                :class="{
                                    checked: selectedIds.includes(item.id)
                                }"
                            >
                                <span
                                    v-if="
                                        selectedIds.includes(item.id)
                                    "
                                >
                                    ✓
                                </span>
                            </span>
                        </button>

                        <div class="cart-image">

                            <img
                                v-if="item.parfum?.foto"
                                :src="`/storage/${item.parfum.foto}`"
                                :alt="item.parfum.nama"
                            >

                            <span v-else>
                                FOTO
                            </span>

                        </div>

                        <div class="cart-info">

                            <span class="cart-category">
                                LAMORE PERFUMES
                            </span>

                            <h2>
                                {{ item.parfum?.nama }}
                            </h2>

                            <strong class="cart-price">
                                {{
                                    formatRupiah(
                                        item.parfum?.harga || 0
                                    )
                                }}
                            </strong>

                            <span class="stock-info">
                                Stok tersedia:
                                {{ item.parfum?.stok ?? 0 }}
                            </span>

                        </div>

                        <div class="cart-actions">

                            <div class="quantity-control">

                                <button
                                    type="button"
                                    @click="kurangiJumlah(item)"
                                    :disabled="
                                        jumlahMap[item.id] <= 1
                                    "
                                >
                                    <Minus :size="15" />
                                </button>

                                <span>
                                    {{ jumlahMap[item.id] }}
                                </span>

                                <button
                                    type="button"
                                    @click="tambahJumlah(item)"
                                    :disabled="
                                        jumlahMap[item.id] >=
                                        item.parfum?.stok
                                    "
                                >
                                    <Plus :size="15" />
                                </button>

                            </div>

                            <strong class="item-subtotal">
                                {{
                                    formatRupiah(
                                        (item.parfum?.harga || 0) *
                                        jumlahMap[item.id]
                                    )
                                }}
                            </strong>

                            <button
                                type="button"
                                class="delete-button"
                                title="Hapus"
                                @click="hapusItem(item)"
                            >
                                <Trash2 :size="16" />
                            </button>

                        </div>

                    </article>

                </div>

            </div>

        </main>

        <div
    v-if="props.items.length"
    class="checkout-panel"
>

    <section class="payment-card">

        <div class="payment-card-header">

            <h2>
                Metode Pembayaran
            </h2>

            <Link
                href="/pelanggan/pembayaran"
                class="see-all-payment"
            >
                Lihat Semua
                <span>›</span>
            </Link>

        </div>

        <label
            class="payment-item"
            :class="{
                selected: metodePembayaran === 'cod'
            }"
        >

            <input
                type="radio"
                value="cod"
                v-model="metodePembayaran"
            >

            <div class="payment-item-content">

                <strong>
                    COD
                </strong>

                <span>
                    Bayar saat pesanan diterima
                </span>

            </div>

        </label>

        <label
            class="payment-item"
            :class="{
                selected: metodePembayaran === 'transfer'
            }"
        >

            <input
                type="radio"
                value="transfer"
                v-model="metodePembayaran"
            >

            <div class="payment-item-content">

                <strong>
                    Transfer Bank
                </strong>

                <span>
                    Bayar melalui transfer bank
                </span>

            </div>

        </label>

    </section>


    <section class="payment-summary-card">

        <h2>
            Rincian Pembayaran
        </h2>

        <div class="summary-row">

            <span>
                Harga Produk
            </span>

            <strong>
                {{ formatRupiah(totalHarga) }}
            </strong>

        </div>


        <div class="summary-row">

            <span>
                Ongkos Kirim
            </span>

            <strong>
                {{ formatRupiah(ongkir) }}
            </strong>

        </div>


        <div class="summary-line"></div>


        <div class="summary-total">

            <span>
                Total Pembayaran
            </span>

            <strong>
                {{ formatRupiah(totalBayar) }}
            </strong>

        </div>


        <button
            type="button"
            class="checkout-button"
            :disabled="
                selectedIds.length === 0 ||
                !props.alamat
            "
            @click="checkout"
        >

            <span>
                {{ formatRupiah(totalBayar) }}
            </span>

            <span>
                Checkout
            </span>

        </button>

    </section>

</div>

    </div>
</template>


<style scoped>
* {
    box-sizing: border-box;
}

.cart-page {
    min-height: 100vh;

    padding-bottom: 30px;

    background: #f5f8f7;

    color: #304c4b;
}

.cart-header {
    position: sticky;

    top: 0;

    z-index: 20;

    height: 72px;

    display: grid;

    grid-template-columns: 60px 1fr 60px;

    align-items: center;

    padding: 0 6%;

    background: rgba(255, 255, 255, .96);

    border-bottom: 1px solid #e3ece9;

    backdrop-filter: blur(10px);
}

.back-button {
    width: 38px;
    height: 38px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: none;

    background: transparent;

    color: #476f6d;

    cursor: pointer;

    text-decoration: none;

    border-radius: 50%;
}

.back-button:hover {
    background: #edf4f2;
}

.header-title {
    text-align: center;
}

.header-title h1 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 24px;

    font-weight: normal;

    color: #304c4b;
}

.header-title span {
    display: block;

    margin-top: 3px;

    font-size: 9px;

    color: #91a3a1;
}

.edit-button {
    border: none;

    background: transparent;

    color: #668b89;

    font-size: 10px;

    cursor: pointer;
}

.cart-container {
    width: min(1000px, calc(100% - 40px));

    margin: 25px auto;
}

.cart-content {
    display: flex;

    flex-direction: column;

    gap: 12px;
}
.address-section {
    margin-bottom: 15px;
    padding: 18px;

    background: #ffffff;

    border: 1px solid #e1ebe8;
    border-radius: 12px;
}

.address-heading {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 14px;
}

.address-icon {
    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #edf4f2;

    font-size: 17px;
}

.address-heading h2 {
    margin: 0 0 3px;

    font-family: Georgia, serif;
    font-size: 17px;
    font-weight: normal;

    color: #304c4b;
}

.address-heading p {
    margin: 0;

    font-size: 9px;
    color: #91a3a1;
}

.address-filled {
    position: relative;

    padding: 14px;

    border-radius: 9px;

    background: #f7faf9;
}

.address-main {
    display: flex;
    align-items: center;
    gap: 12px;

    margin-bottom: 7px;
}

.address-main strong {
    font-size: 11px;
    color: #526b69;
}

.address-main span {
    font-size: 10px;
    color: #91a3a1;
}

.address-filled p {
    max-width: 80%;

    margin: 0;

    color: #718381;

    font-size: 10px;
    line-height: 1.7;
}

.address-edit {
    position: absolute;

    top: 14px;
    right: 14px;

    color: #568381;

    font-size: 9px;
    text-decoration: none;
}

.address-empty {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    padding: 14px;

    border-radius: 9px;

    background: #f7faf9;
}

.address-empty strong {
    display: block;

    margin-bottom: 4px;

    font-size: 11px;
    color: #526b69;
}

.address-empty p {
    margin: 0;

    color: #99a8a6;

    font-size: 9px;
}

.address-add {
    flex-shrink: 0;

    padding: 9px 13px;

    border-radius: 7px;

    background: #5d8986;

    color: #ffffff;

    font-size: 9px;
    text-decoration: none;
}

.address-add:hover {
    background: #477c79;
}

.cart-toolbar {
    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 13px 16px;

    background: #ffffff;

    border: 1px solid #e1ebe8;

    border-radius: 12px;
}

.select-all,
.item-check,
.bottom-select {
    border: none;

    background: transparent;

    padding: 0;

    cursor: pointer;
}

.select-all,
.bottom-select {
    display: flex;

    align-items: center;

    gap: 9px;

    color: #5f7472;

    font-size: 10px;
}

.cart-user {
    color: #9aa9a7;

    font-size: 9px;
}

.checkbox {
    width: 20px;
    height: 20px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: 1.5px solid #c7d5d2;

    border-radius: 50%;

    background: white;

    color: white;

    font-size: 11px;

    transition: .2s;
}

.checkbox.checked {
    border-color: #5e8b87;

    background: #5e8b87;
}

.cart-list {
    display: flex;

    flex-direction: column;

    gap: 10px;
}

.cart-item {
    display: grid;

    grid-template-columns:
        25px
        110px
        1fr
        auto;

    align-items: center;

    gap: 16px;

    padding: 15px;

    background: white;

    border: 1px solid #e1ebe8;

    border-radius: 13px;

    transition: .2s;
}

.cart-item:hover {
    border-color: #cddfdb;
}

.item-check {
    display: flex;

    align-items: center;
    justify-content: center;
}

.cart-image {
    width: 110px;
    height: 110px;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 11px;

    background: #f2f6f5;

    flex-shrink: 0;
}

.cart-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.cart-image span {
    font-size: 8px;

    letter-spacing: 2px;

    color: #9aadaa;
}

.cart-info {
    min-width: 0;
}

.cart-category {
    display: block;

    margin-bottom: 7px;

    color: #92aaa7;

    font-size: 7px;

    letter-spacing: 2px;
}

.cart-info h2 {
    margin: 0 0 9px;

    font-family: Georgia, serif;

    font-size: 19px;

    font-weight: normal;

    color: #304c4b;
}

.cart-price {
    display: block;

    margin-bottom: 6px;

    color: #4a807d;

    font-size: 13px;
}

.stock-info {
    color: #9aa8a6;

    font-size: 9px;
}

.cart-actions {
    display: flex;

    align-items: flex-end;

    flex-direction: column;

    gap: 10px;
}

.quantity-control {
    display: flex;

    align-items: center;

    overflow: hidden;

    border: 1px solid #dce7e5;

    border-radius: 8px;

    background: #f7faf9;
}

.quantity-control button {
    width: 34px;
    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: none;

    background: transparent;

    color: #668b89;

    cursor: pointer;
}

.quantity-control button:hover:not(:disabled) {
    background: #edf4f2;
}

.quantity-control button:disabled {
    color: #ccd7d5;

    cursor: not-allowed;
}

.quantity-control span {
    width: 34px;

    text-align: center;

    color: #526b69;

    font-size: 11px;
}

.item-subtotal {
    color: #477c79;

    font-size: 13px;
}

.delete-button {
    width: 32px;
    height: 32px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: none;

    border-radius: 7px;

    background: transparent;

    color: #b48686;

    cursor: pointer;
}

.delete-button:hover {
    background: #fff5f5;
}

.empty-cart {
    padding: 80px 25px;

    text-align: center;

    background: white;

    border: 1px solid #dce7e5;

    border-radius: 14px;
}

.empty-icon {
    margin-bottom: 15px;

    font-size: 40px;
}

.empty-cart h2 {
    margin: 0 0 8px;

    font-family: Georgia, serif;

    font-size: 22px;

    font-weight: normal;
}

.empty-cart p {
    margin: 0 0 22px;

    color: #899c9a;

    font-size: 11px;
}

.shop-button {
    display: inline-block;

    padding: 11px 20px;

    border-radius: 8px;

    background: #477c79;

    color: white;

    text-decoration: none;

    font-size: 10px;
}

.checkout-panel {
    width: min(1000px, calc(100% - 40px));

    margin: 20px auto 40px;

    display: grid;

    grid-template-columns: 1.35fr 1fr;

    gap: 15px;

    align-items: start;
}

.payment-card {
    background: #ffffff;

    border: 1px solid #e1ebe8;

    border-radius: 14px;

    overflow: hidden;
}

.payment-card-header {
    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 18px 20px;

    border-bottom: 1px solid #edf2f0;
}

.payment-card-header h2 {
    margin: 0;

    color: #304c4b;

    font-size: 14px;

    font-weight: 600;
}

.see-all-payment {
    display: flex;

    align-items: center;

    gap: 4px;

    color: #5d8986;

    font-size: 10px;

    text-decoration: none;
}

.see-all-payment span {
    font-size: 17px;

    line-height: 1;
}

.payment-item {
    position: relative;

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 16px 20px;

    border-bottom: 1px solid #edf2f0;

    background: #ffffff;

    cursor: pointer;

    transition: .2s ease;
}

.payment-item:last-child {
    border-bottom: none;
}

.payment-item.selected {
    background: #f7fbfa;
}

.payment-item input {
    width: 18px;
    height: 18px;

    margin: 0;

    flex-shrink: 0;

    accent-color: #5d8986;
}

.payment-item-content {
    display: flex;

    flex-direction: column;

    gap: 4px;
}

.payment-item-content strong {
    color: #405d5a;

    font-size: 12px;
}

.payment-item-content span {
    color: #97a6a4;

    font-size: 9px;
}

.payment-check {
    margin-left: auto;

    color: #5d8986;

    font-size: 15px;

    font-weight: 600;
}
.payment-summary-card {
    padding: 20px;

    background: #ffffff;

    border: 1px solid #e1ebe8;

    border-radius: 14px;
}

.payment-summary-card h2 {
    margin: 0 0 18px;

    color: #304c4b;

    font-size: 14px;

    font-weight: 600;
}

.summary-row {
    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 12px;
}

.summary-row span {
    color: #899997;

    font-size: 10px;
}

.summary-row strong {
    color: #526b69;

    font-size: 11px;
}

.summary-line {
    margin: 15px 0;

    border-top: 1px solid #e6eeec;
}

.summary-total {
    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 20px;
}

.summary-total span {
    color: #304c4b;

    font-size: 12px;

    font-weight: 600;
}

.summary-total strong {
    color: #4f817d;

    font-size: 16px;
}

.checkout-button {
    width: 100%;

    min-height: 48px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    border: none;

    border-radius: 10px;

    background: #5d8986;

    color: #ffffff;

    cursor: pointer;

    transition: .2s ease;
}

.checkout-button span:first-child {
    font-size: 13px;

    font-weight: 600;
}

.checkout-button span:last-child {
    font-size: 10px;
}

.checkout-button:hover:not(:disabled) {
    background: #477c79;
}

.checkout-button:disabled {
    background: #cbd7d5;

    cursor: not-allowed;
}

@media (max-width: 700px) {

    .cart-header {
        height: 64px;

        grid-template-columns: 45px 1fr 45px;

        padding: 0 15px;
    }

    .header-title h1 {
        font-size: 20px;
    }

    .cart-container {
        width: calc(100% - 20px);

        margin: 12px auto;
    }

    .cart-toolbar {
        padding: 11px 12px;
    }

    .cart-item {
        grid-template-columns:
            22px
            82px
            1fr;

        gap: 11px;

        padding: 12px;
    }

    .cart-image {
        width: 82px;
        height: 82px;
    }

    .cart-info h2 {
        font-size: 16px;
    }

    .cart-price {
        font-size: 12px;
    }

    .cart-actions {
        grid-column: 3;

        flex-direction: row;

        align-items: center;

        justify-content: space-between;

        width: 100%;
    }

    .item-subtotal {
        margin-left: auto;
    }

    .checkout-panel {
    width: calc(100% - 20px);

    grid-template-columns: 1fr;

    margin: 15px auto 25px;

    gap: 12px;
}

.payment-card-header {
    padding: 16px;
}

.payment-item {
    padding: 14px 16px;
}

.payment-summary-card {
    padding: 16px;
}

.summary-total strong {
    font-size: 18px;
}
}
</style>