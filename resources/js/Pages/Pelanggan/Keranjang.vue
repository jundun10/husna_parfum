<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ArrowLeft, Minus, Plus, } from 'lucide-vue-next';

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
const hitungSubtotalItem = (item) => {
    const hargaPerMl = Number(item.parfum?.harga_per_ml ?? 0);
    const ukuranMl = Number(item.ukuran_ml ?? 0);
    const jumlah = Number(
        jumlahMap.value[item.id] ?? item.jumlah ?? 0
    );

    return hargaPerMl * ukuranMl * jumlah;
};
const totalHarga = computed(() => {
    return props.items
        .filter(item => selectedIds.value.includes(item.id))
        .reduce((total, item) => {
            const jumlah = jumlahMap.value[item.id] ?? item.jumlah;
            const hargaPerMl = Number(item.parfum?.harga_per_ml || 0);
            const ukuranMl = Number(item.ukuran_ml || 0);

            return total + (
                hargaPerMl * ukuranMl * jumlah
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
        '/pelanggan/checkout',
        {
            item_ids: selectedIds.value,
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
                <ArrowLeft :size="20" :stroke-width="1.7" />
            </Link>

            <div class="header-title">
                <h1>Keranjang Saya</h1>

                <span>
                    {{ props.items.length }} produk
                </span>
            </div>

            <span class="header-spacer"></span>

        </header>

        <main class="cart-container">

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
                                Foto
                            </span>

                        </div>

                        <div class="cart-info">

                            <h2>
                                {{ item.parfum?.nama }}
                            </h2>

                            <div class="cart-info-tags">

                                <span class="size-info">
                                    {{ item.ukuran_ml }} ml
                                </span>

                                <span class="tag-dot">•</span>

                                <span class="stock-info">
                                    Stok {{ item.parfum?.stok ?? 0 }}
                                </span>

                            </div>

                            <strong class="cart-price">
                                {{ formatRupiah(item.parfum?.harga_per_ml || 0) }}
                                <small>/ ml</small>
                            </strong>

                        </div>

                        <div class="cart-actions">

                            <div class="quantity-control">

                                <button
                                    type="button"
                                    @click="kurangiJumlah(item)"
                                    :disabled="
                                        jumlahMap[item.id] <= 1
                                    "
                                    aria-label="Kurangi jumlah"
                                >
                                    <Minus :size="13" :stroke-width="1.8" />
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
                                    aria-label="Tambah jumlah"
                                >
                                    <Plus :size="13" :stroke-width="1.8" />
                                </button>

                            </div>

                           <strong class="item-subtotal">
                                {{ formatRupiah(hitungSubtotalItem(item)) }}
                            </strong>

                            <button
                                type="button"
                                class="delete-button"
                                title="Hapus"
                                @click="hapusItem(item)"
                            >
                                Hapus
                            </button>

                        </div>

                    </article>

                </div>

                <div class="cart-footer">

                    <div class="cart-footer-info">
                        <span>
                            {{ jumlahDipilih }} produk dipilih
                        </span>
                    </div>

                    <div class="cart-footer-action">

                        <div class="cart-footer-total">
                            <span>Total</span>

                            <strong>
                                {{ formatRupiah(totalBayar) }}
                            </strong>
                        </div>

                        <button
                            type="button"
                            class="checkout-button"
                            :disabled="selectedIds.length === 0"
                            @click="checkout"
                        >
                            Checkout
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>
</template>


<style scoped>

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap');

* {
    box-sizing: border-box;
}

.cart-page {
    --forest: #23423a;
    --forest-deep: #1a332c;
    --sage: #7d9d8a;
    --sage-soft: #e9f0e8;
    --ivory: #faf9f4;
    --line: #e1e6dd;
    --text: #2c3a35;
    --text-muted: #75847b;

    min-height: 100vh;

    padding-bottom: 40px;

    background: var(--ivory);

    color: var(--text);

    font-family: 'Inter', Arial, sans-serif;
}

.cart-page h1,
.cart-page h2 {
    font-family: 'Cormorant Garamond', Georgia, serif;
}

.cart-header {
    position: sticky;
    top: 0;
    z-index: 20;

    height: 68px;

    display: grid;
    grid-template-columns: 40px 1fr 40px;

    align-items: center;

    padding: 0 6%;

    background: rgba(250, 249, 244, .96);

    border-bottom: 1px solid var(--line);

    backdrop-filter: blur(8px);
}

.back-button {
    width: 36px;
    height: 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    background: transparent;

    color: var(--forest);

    cursor: pointer;
    text-decoration: none;

    border-radius: 50%;

    transition: background .2s ease;
}

.back-button:hover {
    background: var(--sage-soft);
}

.header-title {
    text-align: center;
}

.header-title h1 {
    margin: 0;

    font-size: 21px;
    font-weight: 500;

    color: var(--forest-deep);
}

.header-title span {
    display: block;

    margin-top: 2px;

    font-size: 10px;

    color: var(--text-muted);
}

.header-spacer {
    width: 36px;
}


.cart-container {
    width: min(880px, calc(100% - 40px));

    margin: 30px auto;
}

.cart-content {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.cart-toolbar {
    display: flex;

    align-items: center;
    justify-content: space-between;

    padding: 14px 18px;

    background: #ffffff;

    border: 1px solid var(--line);
    border-radius: 8px;
}

.select-all {
    border: none;
    background: transparent;

    padding: 0;

    display: flex;
    align-items: center;
    gap: 10px;

    color: var(--text);

    font-size: 11px;

    cursor: pointer;
}

.cart-user {
    color: var(--text-muted);

    font-size: 10px;
}

.checkbox {
    width: 18px;
    height: 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1.5px solid #c7d5d2;
    border-radius: 4px;

    background: #ffffff;
    color: #ffffff;

    font-size: 10px;

    transition: border-color .2s ease, background .2s ease;
}

.checkbox.checked {
    border-color: var(--forest);
    background: var(--forest);
}

.cart-list {
    background: #ffffff;

    border: 1px solid var(--line);
    border-radius: 8px;

    overflow: hidden;
}

.item-check {
    border: none;
    background: transparent;

    padding: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;
}

.cart-item {
    display: grid;

    grid-template-columns: 24px 92px 1fr auto;

    align-items: center;

    gap: 18px;

    padding: 20px;

    border-bottom: 1px solid var(--line);

    transition: background .2s ease;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item:hover {
    background: var(--ivory);
}

.cart-image {
    width: 92px;
    height: 92px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 6px;

    background: var(--sage-soft);

    flex-shrink: 0;
}

.cart-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.cart-image span {
    font-size: 9px;
    letter-spacing: 1px;

    color: var(--text-muted);
}

.cart-info {
    min-width: 0;
}

.cart-info h2 {
    margin: 0 0 7px;

    font-size: 18px;
    font-weight: 500;
    line-height: 1.3;

    color: var(--forest-deep);

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cart-info-tags {
    display: flex;
    align-items: center;
    gap: 7px;

    margin-bottom: 8px;

    font-size: 10.5px;

    color: var(--text-muted);
}

.tag-dot {
    font-size: 8px;
}

.cart-price {
    display: block;

    color: var(--forest);

    font-size: 13px;
    font-weight: 600;
}

.cart-price small {
    font-weight: 400;

    color: var(--text-muted);
}

.cart-actions {
    display: flex;

    flex-direction: column;
    align-items: flex-end;

    gap: 10px;
}

.quantity-control {
    display: flex;
    align-items: center;

    overflow: hidden;

    border: 1px solid var(--line);
    border-radius: 6px;
}

.quantity-control button {
    width: 30px;
    height: 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    background: transparent;

    color: var(--forest);

    cursor: pointer;

    transition: background .15s ease;
}

.quantity-control button:hover:not(:disabled) {
    background: var(--sage-soft);
}

.quantity-control button:disabled {
    color: var(--line);

    cursor: not-allowed;
}

.quantity-control span {
    width: 30px;

    text-align: center;

    color: var(--text);

    font-size: 11px;
    font-weight: 500;
}

.item-subtotal {
    color: var(--forest);

    font-size: 13.5px;
    font-weight: 600;
}

.delete-button {
    padding: 0;

    border: none;
    background: transparent;

    color: var(--text-muted);

    font-size: 10.5px;
    font-weight: 500;

    cursor: pointer;
    transition: color .2s ease;
}

.delete-button:hover {
    color: #b5675f;
}

.empty-cart {
    padding: 90px 25px;

    text-align: center;

    background: #ffffff;

    border: 1px solid var(--line);
    border-radius: 8px;
}

.empty-icon {
    margin-bottom: 16px;

    font-size: 34px;

    opacity: .85;
}

.empty-cart h2 {
    margin: 0 0 8px;

    font-size: 22px;
    font-weight: 500;

    color: var(--forest-deep);
}

.empty-cart p {
    margin: 0 0 24px;

    color: var(--text-muted);

    font-size: 11.5px;
}

.shop-button {
    display: inline-block;

    padding: 12px 24px;

    border: 1px solid var(--forest);
    border-radius: 6px;

    background: var(--forest);

    color: #ffffff;

    text-decoration: none;

    font-size: 11px;
    font-weight: 500;

    transition: background .2s ease;
}

.shop-button:hover {
    background: var(--forest-deep);
}

.cart-footer {
    position: sticky;
    bottom: 14px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-top: 6px;
    padding: 16px 20px;

    background: #ffffff;

    border: 1px solid var(--line);
    border-radius: 8px;

    box-shadow: 0 6px 20px rgba(35, 66, 58, .08);
}

.cart-footer-info span {
    color: var(--text-muted);

    font-size: 10.5px;
}

.cart-footer-action {
    display: flex;
    align-items: center;
    gap: 20px;
}

.cart-footer-total {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.cart-footer-total span {
    margin-bottom: 2px;

    color: var(--text-muted);

    font-size: 9.5px;
}

.cart-footer-total strong {
    color: var(--forest-deep);

    font-size: 16px;
    font-weight: 600;
}

.checkout-button {
    min-width: 150px;
    height: 44px;

    border: none;
    border-radius: 6px;

    background: var(--forest);
    color: #ffffff;

    font-family: 'Inter', Arial, sans-serif;
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: .2px;

    cursor: pointer;

    transition: background .2s ease;
}

.checkout-button:hover:not(:disabled) {
    background: var(--forest-deep);
}

.checkout-button:disabled {
    background: #c7d3ce;

    cursor: not-allowed;
}



.back-button:focus-visible,
.select-all:focus-visible,
.item-check:focus-visible,
.quantity-control button:focus-visible,
.delete-button:focus-visible,
.checkout-button:focus-visible,
.shop-button:focus-visible {
    outline: 2px solid var(--sage);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .cart-item,
    .checkbox,
    .quantity-control button,
    .checkout-button,
    .back-button {
        transition: none !important;
    }
}

@media (max-width: 700px) {

    .cart-header {
        height: 60px;

        grid-template-columns: 34px 1fr 34px;

        padding: 0 16px;
    }

    .header-title h1 {
        font-size: 18px;
    }

    .cart-container {
        width: calc(100% - 24px);

        margin: 16px auto;
    }

    .cart-toolbar {
        padding: 12px 14px;
    }

    .cart-item {
        grid-template-columns: 20px 76px 1fr;

        gap: 12px;

        padding: 14px;
    }

    .cart-image {
        width: 76px;
        height: 76px;
    }

    .cart-info h2 {
        font-size: 15px;
    }

    .cart-actions {
        grid-column: 3;

        flex-direction: row;

        align-items: center;
        justify-content: space-between;

        width: 100%;

        margin-top: 12px;
    }

    .item-subtotal {
        margin-left: auto;
        margin-right: 10px;
    }

    .cart-footer {
        flex-direction: column;
        align-items: stretch;

        gap: 12px;

        bottom: 10px;
    }

    .cart-footer-action {
        justify-content: space-between;
    }

    .cart-footer-total {
        align-items: flex-start;
    }

    .checkout-button {
        min-width: 130px;
    }

}

</style>