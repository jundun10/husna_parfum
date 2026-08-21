<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { CircleCheckBig, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    pesanan: {
        type: Object,
        required: true,
    },
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};
const isCod = computed(() => {
    return props.pesanan.metode_pembayaran === 'cod';
});

const metodePembayaranLabel = computed(() => {
    if (props.pesanan.metode_pembayaran === 'cod') {
        return 'COD';
    }

    if (props.pesanan.metode_pembayaran === 'transfer') {
        return 'Transfer Bank';
    }

    return props.pesanan.metode_pembayaran;
});
</script>

<template>

    <Head title="Pesanan Berhasil" />

    <div class="success-page">

        <header class="success-header">

            <Link
                href="/Pelanggan"
                class="back-button"
            >
                <ArrowLeft :size="22" />
            </Link>

        </header>


        <main class="success-container">

            <div class="success-card">

                <div class="success-icon">
                    <CircleCheckBig
                        :size="48"
                        :stroke-width="2"
                    />
                </div>


                <h1 v-if="isCod">
                    Pesanan Berhasil
                </h1>

                <h1 v-else>
                    Pembayaran Berhasil
                </h1>


                <p
                    v-if="isCod"
                    class="success-message"
                >
                    Pesanan Anda berhasil dibuat.
                    Pembayaran dilakukan saat pesanan diterima.
                    Kami akan memproses pesanan anda.
                </p>

                <p
                    v-else
                    class="success-message"
                                >
                    Pembayaran Anda berhasil.
                    Pesanan akan segera di proses.
                </p>
                <div class="order-summary">

                    <span>
                        Metode Pembayaran
                    </span>

                    <strong>
                        {{ metodePembayaranLabel }}
                    </strong>

                </div>

                <div class="order-total">

                    <span>
                        Total Pesanan
                    </span>

                    <strong>
                        {{
                            formatRupiah(
                                props.pesanan.total_harga
                            )
                        }}
                    </strong>

                </div>


                <Link
                    href="/Pelanggan"
                    class="order-button"
                >
                    Lihat pesanan
                </Link>

            </div>

        </main>

    </div>

</template>


<style scoped>

* {
    box-sizing: border-box;
}

.success-page {
    min-height: 100vh;

    background: #ffffff;

    color: #304c4b;
}


.success-header {
    height: 70px;

    display: flex;
    align-items: center;

    padding: 0 30px;

    border-bottom: 1px solid #edf1f0;
}


.back-button {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    color: #405555;

    text-decoration: none;

    transition: .2s;
}

.back-button:hover {
    background: #f3f7f6;
}


.success-container {
    min-height: calc(100vh - 70px);

    display: flex;
    align-items: flex-start;
    justify-content: center;

    padding: 70px 20px;
}


.success-card {
    width: min(560px, 100%);

    text-align: center;
}


.success-icon {
    width: 92px;
    height: 92px;

    margin: 0 auto 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #16d99b;

    color: #ffffff;

    box-shadow:
        0 12px 30px
        rgba(22, 217, 155, .18);
}


.success-card h1 {
    margin: 0 0 14px;

    font-family: Arial, sans-serif;

    font-size: 34px;

    font-weight: 700;

    line-height: 1.2;

    color: #111111;
}


.success-message {
    max-width: 480px;

    margin: 0 auto 28px;

    color: #777777;

    font-size: 16px;

    line-height: 1.6;
}


.order-summary,
.order-total {
    max-width: 430px;

    margin: 0 auto;

    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 15px 18px;

    background: #f8faf9;

    border-radius: 10px;
}


.order-summary {
    margin-bottom: 8px;
}


.order-summary span,
.order-total span {
    color: #8a9795;

    font-size: 11px;
}


.order-summary strong {
    color: #52706d;

    font-size: 12px;
}


.order-total strong {
    color: #4f817d;

    font-size: 17px;
}


.order-button {
    width: 100%;
    max-width: 430px;
    height: 54px;

    margin: 25px auto 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 10px;

    background: #f3f3f3;
    color: #111111;

    text-align: center;
    text-decoration: none;

    font-size: 15px;
    font-weight: 600;

    transition: .2s ease;
}


.order-button:hover {
    background: #e8e8e8;
}


@media (max-width: 600px) {

    .success-header {
        padding: 0 18px;
    }

    .success-container {
        padding: 45px 20px;
    }

    .success-icon {
        width: 84px;
        height: 84px;

        margin-bottom: 25px;
    }

    .success-card h1 {
        font-size: 27px;
    }

    .success-message {
        font-size: 14px;
    }

}

</style>