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
            class="checkout-bar"
        >

            <div class="checkout-inner">

                <button
                    type="button"
                    class="bottom-select"
                    @click="toggleSemua"
                >
                    <span
                        class="checkbox"
                        :class="{ checked: semuaDipilih }"
                    >
                        <span v-if="semuaDipilih">✓</span>
                    </span>

                    <span>
                        Semua
                    </span>
                </button>
                
                <div class="payment-method">

                    <span class="payment-label">
                        Metode Pembayaran
                    </span>

                    <label class="payment-option">
                        <input
                            type="radio"
                            value="cod"
                            v-model="metodePembayaran"
                        >

                        <span>
                            COD
                        </span>
                    </label>

                    <label class="payment-option">
                        <input
                            type="radio"
                            value="transfer"
                            v-model="metodePembayaran"
                        >

                        <span>
                            Transfer
                        </span>
                    </label>

                </div>

                <div class="checkout-summary">

                    <span>
                        {{ jumlahDipilih }} produk
                    </span>

                    <strong>
                        {{ formatRupiah(totalHarga) }}
                    </strong>
                    <span
                        v-if="!props.alamat"
                        class="checkout-warning"
                    >
                        Tambahkan alamat terlebih dahulu
                    </span>

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
                    Checkout
            </button>

            </div>

        </div>

    </div>
</template>


<style scoped>
* {
    box-sizing: border-box;
}

.cart-page {
    min-height: 100vh;

    padding-bottom: 95px;

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

.checkout-bar {
    position: fixed;

    left: 0;
    right: 0;
    bottom: 0;

    z-index: 30;

    padding: 13px 6%;

    background: rgba(255, 255, 255, .96);

    border-top: 1px solid #dce7e5;

    box-shadow: 0 -8px 25px rgba(70, 95, 90, .08);

    backdrop-filter: blur(10px);
}

.checkout-inner {
    width: min(1000px, 100%);

    margin: 0 auto;

    display: grid;

    grid-template-columns: 1fr auto auto;

    align-items: center;

    gap: 20px;
}

.checkout-summary {
    display: flex;

    align-items: flex-end;

    flex-direction: column;

    gap: 3px;
}

.checkout-summary span {
    color: #99a8a6;

    font-size: 9px;
}

.checkout-summary strong {
    color: #304c4b;

    font-size: 16px;
}
.checkout-warning {
    color: #b77b7b;
    font-size: 9px;
}

.checkout-button {
    min-width: 145px;

    height: 44px;

    padding: 0 25px;

    border: none;

    border-radius: 11px;

    background: #5d8986;

    color: white;

    font-size: 11px;

    cursor: pointer;

    transition: .2s;
}

.checkout-button:hover:not(:disabled) {
    background: #477c79;
}

.checkout-button:disabled {
    background: #cbd7d5;

    cursor: not-allowed;
}
.payment-method {
    display: flex;
    align-items: center;
    gap: 14px;
}

.payment-label {
    color: #526b69;
    font-size: 9px;
    white-space: nowrap;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 5px;

    color: #66807e;
    font-size: 9px;

    cursor: pointer;
}

.payment-option input {
    width: 13px;
    height: 13px;

    accent-color: #5d8986;

    cursor: pointer;
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

    .checkout-bar {
        padding: 10px 12px;
    }

    .checkout-inner {
        grid-template-columns: auto 1fr auto;

        gap: 10px;
    }

    .checkout-summary strong {
        font-size: 14px;
    }

    .checkout-button {
        min-width: 115px;

        height: 42px;
    }
}
</style>