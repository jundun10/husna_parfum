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

    const itemIds = props.items
        .map(item => item.id)
        .filter(id => id !== null && id !== undefined);

    router.post(
        '/pelanggan/keranjang/checkout',
        {
            item_ids: itemIds.length > 0 ? itemIds : null,
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
                <ArrowLeft :size="19" />
            </button>

            <div class="header-titles">
                <span class="header-eyebrow"></span>
                <h1>Checkout</h1>
            </div>
        </header>

        <main class="checkout-main">

            <div class="checkout-layout">

                <div class="checkout-column">

                    <section class="checkout-card">

                        <div class="section-header">
                            <div class="section-icon">
                                <MapPin :size="17" />
                            </div>

                            <div>
                                <h2>Alamat Pengiriman</h2>
                            </div>
                        </div>

                        <div
                            v-if="alamat"
                            class="address-box"
                        >
                            <div class="address-top">

                                <div class="address-identity">
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

                            <p class="address-detail">
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
                            <div class="empty-address-text">
                                <strong>Belum ada alamat pengiriman</strong>
                                <p>
                                    Tambahkan alamat terlebih dahulu sebelum membuat pesanan.
                                </p>
                            </div>

                            <Link
                                href="/pelanggan/alamat"
                                class="btn add-address-button"
                            >
                                Tambah Alamat
                            </Link>
                        </div>

                    </section>


                    <section class="checkout-card">

                        <div class="section-header">
                            <div class="section-icon">
                                <Package :size="17" />
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

                                    <div class="product-meta">
                                        <span class="product-size">
                                            {{ item.ukuran_ml }} ml
                                        </span>

                                        <span class="meta-dot">·</span>

                                        <span class="product-qty">
                                            {{ item.jumlah }} botol
                                        </span>
                                    </div>

                                    <span class="product-price-ml">
                                        {{ formatRupiah(item.parfum?.harga_per_ml) }} / ml
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
                                <CreditCard :size="17" />
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

                            <span class="payment-radio-dot"></span>

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

                            <span class="payment-radio-dot"></span>

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

                        <div class="summary-divider"></div>

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
                    <span>Total Pembayaran</span>

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
    --hf-green: #3f6e69;
    --hf-green-dark: #2f5652;
    --hf-sage: #cddbd6;
    --hf-sage-soft: #eef4f2;
    --hf-bg: #f7f9f8;
    --hf-ink: #2c3f3d;
    --hf-ink-soft: #5f7472;
    --hf-ink-faint: #98a6a4;
    --hf-border: #e4ece9;

    min-height: 100vh;
    padding-bottom: 128px;

    background: var(--hf-bg);
    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.checkout-header {
    height: 76px;

    display: flex;
    align-items: center;
    gap: 16px;

    padding: 0 6%;

    background: rgba(255, 255, 255, .97);
    border-bottom: 1px solid var(--hf-border);

    position: sticky;
    top: 0;
    z-index: 20;
}

.back-button {
    width: 40px;
    height: 40px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid var(--hf-border);
    border-radius: 50%;

    background: #ffffff;
    color: var(--hf-green);

    cursor: pointer;
    transition: background .15s ease, border-color .15s ease;
}

.back-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.header-titles {
    display: flex;
    flex-direction: column;
}

.header-eyebrow {
    font-size: 10px;
    letter-spacing: .12em;
    text-transform: uppercase;

    color: var(--hf-ink-faint);
    margin-bottom: 2px;
}

.checkout-header h1 {
    margin: 0;

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 22px;
    font-weight: 400;

    color: var(--hf-ink);
}

.checkout-main {
    padding: 28px 6% 0;
}

.checkout-layout {
    max-width: 640px;
    margin: 0 auto;
}

.checkout-column {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.checkout-card {
    padding: 22px;

    background: #ffffff;
    border: 1px solid var(--hf-border);
    border-radius: 12px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;

    margin-bottom: 18px;
}

.section-icon {
    width: 36px;
    height: 36px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: var(--hf-sage-soft);
    color: var(--hf-green);
}

.section-header h2 {
    margin: 0;

    color: var(--hf-ink);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: .01em;
}

.section-header p {
    margin: 2px 0 0;

    color: var(--hf-ink-faint);
    font-size: 11px;
}

.address-box {
    padding: 16px 17px;

    border: 1px solid var(--hf-border);
    border-radius: 10px;
    background: var(--hf-bg);
}

.address-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
}

.address-identity {
    display: flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 8px;
}

.address-name {
    color: var(--hf-ink);
    font-size: 13px;
    font-weight: 600;
}

.address-phone {
    color: var(--hf-ink-soft);
    font-size: 11.5px;
}

.change-link {
    flex-shrink: 0;

    color: var(--hf-green);
    font-size: 11.5px;
    font-weight: 600;
    text-decoration: none;

    padding: 4px 2px;
    border-bottom: 1px solid transparent;
    transition: border-color .15s ease;
}

.change-link:hover {
    border-bottom-color: var(--hf-green);
}

.address-detail {
    margin: 10px 0 0;

    color: var(--hf-ink-soft);
    font-size: 11.5px;
    line-height: 1.7;
}

.empty-address {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;

    padding: 16px 17px;

    border: 1px dashed var(--hf-sage);
    border-radius: 10px;
    background: var(--hf-sage-soft);
}

.empty-address-text strong {
    display: block;

    margin-bottom: 4px;

    color: var(--hf-ink);
    font-size: 12.5px;
}

.empty-address-text p {
    margin: 0;

    color: var(--hf-ink-soft);
    font-size: 11px;
    line-height: 1.5;
}

.add-address-button {
    flex-shrink: 0;

    padding: 9px 16px;

    border: none;
    border-radius: 8px;

    background: var(--hf-green);
    color: #ffffff;

    font-size: 11.5px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;

    transition: background .15s ease;
}

.add-address-button:hover {
    background: var(--hf-green-dark);
    color: #ffffff;
}

.product-list {
    display: flex;
    flex-direction: column;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 14px;

    padding: 14px 0;

    border-bottom: 1px solid var(--hf-border);
}

.product-item:first-child {
    padding-top: 0;
}

.product-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.product-image {
    width: 64px;
    height: 64px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    border: 1px solid var(--hf-border);
    border-radius: 9px;
    background: var(--hf-sage-soft);

    color: var(--hf-ink-faint);
    font-size: 8px;
    letter-spacing: .06em;
}

.product-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
    object-position: center;
}

.product-info {
    flex: 1;
    min-width: 0;
}

.product-info > strong {
    display: block;

    margin-bottom: 5px;

    color: var(--hf-ink);
    font-size: 12.5px;
    font-weight: 600;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-meta {
    display: flex;
    align-items: center;
    gap: 6px;

    margin-bottom: 3px;
}

.product-size,
.product-qty {
    color: var(--hf-ink-soft);
    font-size: 11px;
}

.meta-dot {
    color: var(--hf-ink-faint);
    font-size: 11px;
}

.product-price-ml {
    color: var(--hf-ink-faint);
    font-size: 10.5px;
}

.product-subtotal {
    flex-shrink: 0;

    color: var(--hf-green-dark);
    font-size: 12.5px;
    font-weight: 700;
    white-space: nowrap;
}

.payment-option {
    position: relative;

    display: flex;
    align-items: center;
    gap: 12px;

    padding: 14px 15px;

    margin-bottom: 10px;

    border: 1px solid var(--hf-border);
    border-radius: 10px;

    background: #ffffff;

    cursor: pointer;
    transition: border-color .15s ease, background .15s ease;
}

.payment-option:last-child {
    margin-bottom: 0;
}

.payment-option:hover {
    border-color: var(--hf-sage);
}

.payment-option.active {
    background: var(--hf-sage-soft);
    border-color: var(--hf-green);
}

.payment-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.payment-radio-dot {
    width: 18px;
    height: 18px;
    flex-shrink: 0;

    border: 1.5px solid var(--hf-ink-faint);
    border-radius: 50%;

    position: relative;

    transition: border-color .15s ease;
}

.payment-option.active .payment-radio-dot {
    border-color: var(--hf-green);
}

.payment-option.active .payment-radio-dot::after {
    content: '';

    position: absolute;
    top: 50%;
    left: 50%;

    width: 9px;
    height: 9px;

    border-radius: 50%;
    background: var(--hf-green);

    transform: translate(-50%, -50%);
}

.payment-content strong {
    display: block;

    margin-bottom: 3px;

    color: var(--hf-ink);
    font-size: 12.5px;
    font-weight: 600;
}

.payment-content span {
    color: var(--hf-ink-faint);
    font-size: 10.5px;
}

.summary-card h2 {
    margin: 0 0 18px;

    color: var(--hf-ink);
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
    color: var(--hf-ink-soft);
    font-size: 11.5px;
}

.summary-row strong {
    color: var(--hf-ink);
    font-size: 12.5px;
    font-weight: 600;
}

.summary-divider {
    height: 1px;

    margin: 14px 0;

    background: var(--hf-border);
}

.summary-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-total span {
    color: var(--hf-ink);
    font-size: 12.5px;
    font-weight: 600;
}

.summary-total strong {
    color: var(--hf-green-dark);
    font-size: 17px;
    font-weight: 700;
}

.checkout-action-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;

    z-index: 30;

    background: rgba(255, 255, 255, .98);
    border-top: 1px solid var(--hf-border);

    box-shadow: 0 -6px 20px rgba(47, 86, 82, .06);
    backdrop-filter: blur(8px);
}

.checkout-action-inner {
    width: min(640px, calc(100% - 48px));
    min-height: 76px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.action-total {
    display: flex;
    flex-direction: column;
}

.action-total span {
    margin-bottom: 3px;

    color: var(--hf-ink-faint);
    font-size: 10px;
}

.action-total strong {
    color: var(--hf-green-dark);
    font-size: 18px;
    font-weight: 700;
}

.checkout-button {
    min-width: 168px;
    height: 48px;
    flex-shrink: 0;

    border: none;
    border-radius: 9px;

    background: var(--hf-green);
    color: #ffffff;

    font-size: 12.5px;
    font-weight: 600;
    letter-spacing: .01em;

    cursor: pointer;
    transition: background .15s ease, transform .1s ease;
}

.checkout-button:hover:not(:disabled) {
    background: var(--hf-green-dark);
}

.checkout-button:active:not(:disabled) {
    transform: translateY(1px);
}

.checkout-button:disabled {
    background: #d5dedb;
    color: #ffffff;
    cursor: not-allowed;
}

@media (max-width: 600px) {

    .checkout-header {
        height: 66px;
        padding: 0 16px;
        gap: 12px;
    }

    .checkout-header h1 {
        font-size: 19px;
    }

    .checkout-main {
        padding: 20px 16px 0;
    }

    .checkout-column {
        gap: 12px;
    }

    .checkout-card {
        padding: 17px;
    }

    .address-top {
        flex-wrap: wrap;
    }

    .address-detail {
        max-width: 100%;
    }

    .empty-address {
        flex-direction: column;
        align-items: flex-start;
    }

    .add-address-button {
        width: 100%;
        text-align: center;
    }

    .product-image {
        width: 56px;
        height: 56px;
    }

    .product-info > strong {
        white-space: normal;
    }

    .checkout-action-inner {
        width: calc(100% - 24px);
        min-height: 70px;
        gap: 12px;
    }

    .action-total strong {
        font-size: 15px;
    }

    .checkout-button {
        min-width: 0;
        flex: 1;
        height: 46px;
    }

}

</style>