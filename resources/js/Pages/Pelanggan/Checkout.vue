<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, CreditCard, Package } from 'lucide-vue-next';
import { ref, computed } from 'vue';

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

const metodePembayaran = ref('cod');

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);
};

const hitungSubtotal = (item) => {
    const hargaPerMl = Number(item.parfum?.harga_per_ml ?? 0);
    const ukuranMl = Number(item.ukuran_ml ?? 0);
    const jumlah = Number(item.jumlah ?? 0);

    return hargaPerMl * ukuranMl * jumlah;
};

const totalProduk = computed(() => {
    return props.items.reduce((total, item) => {
        return total + hitungSubtotal(item);
    }, 0);
});

const ongkir = computed(() => {
    return props.items.length > 0 ? 25000 : 0;
});

const totalBayar = computed(() => {
    return totalProduk.value + ongkir.value;
});

const kembaliKePelanggan = () => {
    router.visit('/Pelanggan');
};

const buatPesanan = () => {
    if (!props.alamat) {
        return;
    }

    router.post(
        '/pelanggan/keranjang/checkout',
        {
            item_ids: props.items.map(item => item.id),
            metode_pembayaran: metodePembayaran.value,
        },
        {
            preserveScroll: true,
        }
    );
};
</script>

<template>
    <Head title="Checkout" />

    <div class="checkout-page">

        <header class="checkout-header">
            <button
                type="button"
                class="back-button"
                @click="kembaliKePelanggan"
            >
                <ArrowLeft :size="20" />
            </button>

            <div>
                <h1>Checkout</h1>
            </div>
        </header>

        <main class="container py-4 py-md-5">

            <div class="row">

                <div class="col-12">

                    <section class="checkout-card mb-4">

                        <div class="section-header">
                            <div class="section-icon">
                                <MapPin :size="18" />
                            </div>

                            <div>
                                <h2>Alamat Pengiriman</h2>
                            </div>
                        </div>

                        <div
                            v-if="alamat"
                            class="address-box"
                        >
                            <div class="d-flex justify-content-between align-items-start gap-3">

                                <div>
                                    <strong class="address-name">
                                        {{ alamat.nama_penerima }}
                                    </strong>

                                    <span class="address-phone">
                                        {{ alamat.no_hp }}
                                    </span>
                                </div>

                                <Link
                                    href="/pelanggan/alamat"
                                    class="change-link"
                                >
                                    Ubah
                                </Link>

                            </div>

                            <p>
                                {{ alamat.alamat_lengkap }},
                                Desa {{ alamat.desa }},
                                Kecamatan {{ alamat.kecamatan }},
                                {{ alamat.kabupaten_kota }},
                                Provinsi {{ alamat.provinsi }},
                                {{ alamat.kode_pos }}
                            </p>
                        </div>

                        <div
                            v-else
                            class="empty-address"
                        >
                            <div>
                                <strong>Belum ada alamat pengiriman</strong>
                                <p>
                                    Tambahkan alamat terlebih dahulu sebelum membuat pesanan.
                                </p>
                            </div>

                            <Link
                                href="/pelanggan/alamat"
                                class="btn btn-sm add-address-button"
                            >
                                Tambah Alamat
                            </Link>
                        </div>

                    </section>


                    <section class="checkout-card mb-4">

                        <div class="section-header">
                            <div class="section-icon">
                                <Package :size="18" />
                            </div>

                            <div>
                                <h2>Produk Dipesan</h2>
                                <p>{{ items.length }} produk</p>
                            </div>
                        </div>

                        <div class="product-list">

                            <article
                                v-for="item in items"
                                :key="item.id"
                                class="product-item"
                            >

                                <div class="product-image">

                                    <img
                                        v-if="item.parfum?.foto"
                                        :src="`/storage/${item.parfum.foto}`"
                                        :alt="item.parfum.nama"
                                    >

                                    <span v-else>
                                        FOTO
                                    </span>

                                </div>

                                <div class="product-info">

                                    <strong>
                                        {{ item.parfum?.nama }}
                                    </strong>

                                    <span class="product-size">
                                        {{ item.ukuran_ml }} ml
                                    </span>

                                    <span class="product-price-ml">
                                        {{ formatRupiah(item.parfum?.harga_per_ml) }} / ml
                                    </span>

                                    <span class="product-qty">
                                        {{ item.jumlah }} botol
                                    </span>

                                </div>

                                <strong class="product-subtotal">
                                    {{ formatRupiah(hitungSubtotal(item)) }}
                                </strong>

                            </article>

                        </div>

                    </section>


                    <section class="checkout-card">

                        <div class="section-header">
                            <div class="section-icon">
                                <CreditCard :size="18" />
                            </div>

                            <div>
                                <h2>Metode Pembayaran</h2>
                            </div>
                        </div>

                        <label
                            class="payment-option"
                            :class="{
                                active: metodePembayaran === 'cod'
                            }"
                        >
                            <input
                                v-model="metodePembayaran"
                                type="radio"
                                value="cod"
                            >

                            <div class="payment-content">
                                <strong>COD</strong>
                                
                            </div>
                        </label>

                        <label
                            class="payment-option"
                            :class="{
                                active: metodePembayaran === 'transfer'
                            }"
                        >
                            <input
                                v-model="metodePembayaran"
                                type="radio"
                                value="transfer"
                            >

                            <div class="payment-content">
                                <strong>Transfer Bank</strong>
                            </div>
                        </label>

                    </section>

                    <section class="checkout-card summary-card">

                <h2>Rincian Pembayaran</h2>

                <div class="summary-row">
                    <span>Harga Produk</span>

                    <strong>
                        {{ formatRupiah(totalProduk) }}
                    </strong>
                </div>

                <div class="summary-row">
                    <span>Ongkos Kirim</span>

                    <strong>
                        {{ formatRupiah(ongkir) }}
                    </strong>
                </div>

                <div class="summary-total">
                    <span>Total Pembayaran</span>

                    <strong>
                        {{ formatRupiah(totalBayar) }}
                    </strong>
                </div>

                </section>
            </div>

            </div>

        </main>
        <div class="checkout-action-bar">

    <div class="checkout-action-inner">

        <div class="action-total">
            <span>Total</span>

            <strong>
                {{ formatRupiah(totalBayar) }}
            </strong>
        </div>

        <button
            type="button"
            class="checkout-button"
            :disabled="!alamat || items.length === 0"
            @click="buatPesanan"
        >
            Buat Pesanan
        </button>

    </div>

</div>

    </div>
</template>

<style scoped>

.checkout-page {
    min-height: 100vh;
    padding-bottom: 110px;

    background: #f5f8f7;
    color: #304c4b;
}

.checkout-header {
    height: 72px;

    display: flex;
    align-items: center;
    gap: 15px;

    padding: 0 6%;

    background: rgba(255, 255, 255, .96);
    border-bottom: 1px solid #e3ece9;
}

.checkout-header h1 {
    margin: 0;

    font-family: Georgia, serif;
    font-size: 25px;
    font-weight: normal;

    color: #304c4b;
}

.checkout-header p {
    margin: 3px 0 0;

    color: #91a3a1;
    font-size: 9px;
}

.back-button {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 50%;

    background: transparent;
    color: #477c79;

    cursor: pointer;
}

.back-button:hover {
    background: #edf4f2;
}

.checkout-card {
    padding: 20px;

    background: #ffffff;
    border: 1px solid #e1ebe8;
    border-radius: 14px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 11px;

    margin-bottom: 17px;
}

.section-icon {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #edf4f2;
    color: #5d8986;

    flex-shrink: 0;
}

.section-header h2 {
    margin: 0 0 3px;

    color: #304c4b;
    font-size: 14px;
    font-weight: 600;
}

.section-header p {
    margin: 0;

    color: #97a6a4;
    font-size: 9px;
}

.address-box {
    padding: 15px;

    border-radius: 10px;
    background: #f7faf9;
}

.address-name {
    display: block;

    color: #526b69;
    font-size: 11px;
}

.address-phone {
    display: block;

    margin-top: 3px;

    color: #91a3a1;
    font-size: 9px;
}

.address-box p {
    margin: 9px 0 0;

    max-width: 85%;

    color: #718381;
    font-size: 9px;
    line-height: 1.7;
}

.change-link {
    color: #568381;
    font-size: 9px;
    text-decoration: none;
}

.empty-address {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    padding: 14px;

    border-radius: 10px;
    background: #f7faf9;
}

.empty-address strong {
    display: block;

    margin-bottom: 3px;

    color: #526b69;
    font-size: 11px;
}

.empty-address p {
    margin: 0;

    color: #99a8a6;
    font-size: 9px;
}

.add-address-button {
    flex-shrink: 0;

    background: #5d8986;
    border: none;
    color: #ffffff;
}

.add-address-button:hover {
    background: #477c79;
    color: #ffffff;
}

.product-list {
    display: flex;
    flex-direction: column;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 12px 0;

    border-bottom: 1px solid #edf2f0;
}

.product-item:last-child {
    border-bottom: none;
}

.product-image {
    width: 65px;
    height: 65px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;
    flex-shrink: 0;

    border-radius: 10px;
    background: #f1f6f5;

    color: #9aadaa;
    font-size: 7px;
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

    color: #475e5c;
    font-size: 11px;
}

.product-info span {
    display: block;
}

.product-size {
    color: #5d8986;
    font-size: 9px;
}

.product-price-ml {
    margin-top: 2px;

    color: #97a6a4;
    font-size: 8px;
}

.product-qty {
    margin-top: 2px;

    color: #99a8a6;
    font-size: 8px;
}

.product-subtotal {
    color: #4f817d;
    font-size: 11px;
    white-space: nowrap;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 11px;

    padding: 13px 14px;

    margin-bottom: 8px;

    border: 1px solid #e2ebe9;
    border-radius: 10px;

    background: #ffffff;

    cursor: pointer;
    transition: .2s ease;
}

.payment-option:last-child {
    margin-bottom: 0;
}

.payment-option.active {
    background: #f3f9f8;
    border-color: #78a29e;
}

.payment-option input {
    margin: 0;
    accent-color: #5d8986;
}

.payment-content strong {
    display: block;

    margin-bottom: 3px;

    color: #405d5a;
    font-size: 11px;
}

.payment-content span {
    color: #97a6a4;
    font-size: 9px;
}

.action-total span {
    margin-bottom: 3px;
    color: #899997;
    font-size: 9px;
}
.checkout-action-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;

    z-index: 1000;

    background: rgba(255, 255, 255, .98);

    border-top: 1px solid #dfe9e7;

    box-shadow:
        0 -4px 18px rgba(60, 90, 85, .07);

    backdrop-filter: blur(8px);
}

.checkout-action-inner {
    width: min(1000px, calc(100% - 40px));
    min-height: 72px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: flex-end;

    gap: 22px;
}

.action-total {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.action-total span {
    margin-bottom: 2px;

    color: #899997;
    font-size: 9px;
}

.action-total strong {
    color: #4f817d;

    font-size: 18px;
    font-weight: 700;
}

.checkout-action-inner .checkout-button {
    width: 190px;
    height: 48px;

    border: none;
    border-radius: 10px;

    background: #5d8986;
    color: #ffffff;

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;
    transition: .2s ease;
}

.checkout-action-inner .checkout-button:hover:not(:disabled) {
    background: #477c79;
}

.checkout-action-inner .checkout-button:disabled {
    background: #cbd7d5;
    cursor: not-allowed;
}

@media (max-width: 600px) {

    .checkout-action-inner {
        width: calc(100% - 20px);
        min-height: 70px;
        gap: 12px;
    }

    .action-total strong {
        font-size: 15px;
    }

    .checkout-action-inner .checkout-button {
        width: 150px;
        height: 44px;
    }

}

.summary-card h2 {
    margin: 0 0 20px;

    color: #304c4b;
    font-size: 15px;
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

.summary-total {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-top: 12px;
    margin-bottom: 0;
}

.summary-total span {
    color: #899997;
    font-size: 10px;
    font-weight: normal;
}

.summary-total strong {
    color: #526b69;
    font-size: 11px;
    font-weight: 700;
}

.checkout-button {
    width: 100%;
    height: 48px;

    border: none;
    border-radius: 10px;

    background: #5d8986;
    color: #ffffff;

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;
    transition: .2s ease;
}

.checkout-button:hover:not(:disabled) {
    background: #477c79;
}

.checkout-button:disabled {
    background: #cbd7d5;
    cursor: not-allowed;
}

@media (max-width: 991px) {
    .summary-card {
        position: static;
    }
}

@media (max-width: 600px) {

    .checkout-header {
        height: 64px;
        padding: 0 15px;
    }

    .checkout-header h1 {
        font-size: 21px;
    }

    .checkout-card {
        padding: 16px;
    }

    .product-image {
        width: 58px;
        height: 58px;
    }

    .product-subtotal {
        font-size: 10px;
    }

    .address-box p {
        max-width: 100%;
    }

    .empty-address {
        align-items: flex-start;
        flex-direction: column;
    }

}

</style>