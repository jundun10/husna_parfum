<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import {
    CircleCheckBig,
    ArrowLeft,
    Clock3,
    CircleX,
} from 'lucide-vue-next';

const props = defineProps({
    pesanan: {
        type: Object,
        required: true,
    },

    snap_token: {
        type: String,
        default: null,
    },
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);
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

/*
|--------------------------------------------------------------------------
| STATUS PEMBAYARAN
|--------------------------------------------------------------------------
*/

const paymentState = ref(
    isCod.value
        ? 'success'
        : props.pesanan.status_pembayaran === 'sudah_bayar'
            ? 'success'
            : 'pending'
);

/*
|--------------------------------------------------------------------------
| COUNTDOWN
|--------------------------------------------------------------------------
|
| Snap default expiry = 24 jam.
|
*/

const DURASI_PEMBAYARAN = 24 * 60 * 60;

const waktuMulai = new Date(
    props.pesanan.created_at
).getTime();

const waktuBerakhir =
    waktuMulai + DURASI_PEMBAYARAN * 1000;

const sisaWaktu = ref(
    Math.max(
        0,
        Math.floor(
            (waktuBerakhir - Date.now()) / 1000
        )
    )
);

let countdownInterval = null;

const formatCountdown = computed(() => {
    const total = sisaWaktu.value;

    const jam = Math.floor(total / 3600);
    const menit = Math.floor((total % 3600) / 60);
    const detik = total % 60;

    return [
        String(jam).padStart(2, '0'),
        String(menit).padStart(2, '0'),
        String(detik).padStart(2, '0'),
    ].join(':');
});

const mulaiCountdown = () => {
    if (paymentState.value !== 'pending') {
        return;
    }

    countdownInterval = setInterval(() => {
        if (sisaWaktu.value <= 0) {
            sisaWaktu.value = 0;

            paymentState.value = 'failed';

            clearInterval(countdownInterval);
            return;
        }

        sisaWaktu.value--;
    }, 1000);
};

/*
|--------------------------------------------------------------------------
| MIDTRANS SNAP
|--------------------------------------------------------------------------
*/

const bukaMidtrans = () => {
    if (
        !props.snap_token ||
        !window.snap ||
        paymentState.value !== 'pending'
    ) {
        return;
    }

    window.snap.pay(props.snap_token, {
        onSuccess: (result) => {
            console.log('Pembayaran berhasil:', result);

            paymentState.value = 'success';

            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        },

        onPending: (result) => {
            console.log('Pembayaran masih pending:', result);

            paymentState.value = 'pending';
        },

        onError: (result) => {
            console.error('Pembayaran gagal:', result);

            paymentState.value = 'failed';

            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        },

        onClose: () => {
            console.log(
                'Popup Midtrans ditutup sebelum pembayaran selesai.'
            );
        },
    });
};

onMounted(() => {
    if (isCod.value) {
        return;
    }

    mulaiCountdown();

    if (!props.snap_token) {
        return;
    }

    const script = document.createElement('script');

    script.src =
        'https://app.sandbox.midtrans.com/snap/snap.js';

    script.setAttribute(
        'data-client-key',
        import.meta.env.VITE_MIDTRANS_CLIENT_KEY
    );

    script.async = true;

    script.onload = () => {
        bukaMidtrans();
    };

    document.head.appendChild(script);
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
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
                <ArrowLeft :size="20" />
            </Link>

            <span class="header-eyebrow"></span>

        </header>


        <main class="success-container">

            <div class="success-card">

    <template v-if="isCod">

        <div class="status-icon status-icon--success">
            <CircleCheckBig
                :size="40"
                :stroke-width="2"
            />
        </div>

        <h1>
            Pesanan Berhasil
        </h1>

        <p class="success-message">
            Pesanan Anda berhasil dibuat.
            Pembayaran dilakukan saat pesanan diterima.
            Kami akan memproses pesanan Anda.
        </p>

    </template>

    <template v-else-if="paymentState === 'pending'">

        <div class="status-icon status-icon--pending">
            <Clock3
                :size="40"
                :stroke-width="2"
            />
        </div>

        <h1>
            Menunggu Pembayaran
        </h1>

        <p class="success-message">
            Pesanan berhasil dibuat.
            Silakan selesaikan pembayaran sebelum batas waktu berakhir.
        </p>

        <div class="countdown-box">

            <div class="countdown-label">
                <Clock3 :size="13" :stroke-width="2" />
                <span>Batas waktu pembayaran</span>
            </div>

            <strong>
                {{ formatCountdown }}
            </strong>

        </div>

        <button
            v-if="props.snap_token"
            type="button"
            class="pay-button"
            @click="bukaMidtrans"
        >
            Bayar Sekarang
        </button>

    </template>


    <!-- TRANSFER - BERHASIL -->
    <template v-else-if="paymentState === 'success'">

        <div class="status-icon status-icon--success">
            <CircleCheckBig
                :size="40"
                :stroke-width="2"
            />
        </div>

        <h1>
            Pembayaran Berhasil
        </h1>

        <p class="success-message">
            Pembayaran Anda telah berhasil diterima.
            Pesanan akan segera diproses.
        </p>

    </template>


    <!-- TRANSFER - GAGAL -->
    <template v-else-if="paymentState === 'failed'">

        <div class="status-icon status-icon--failed">
            <CircleX
                :size="40"
                :stroke-width="2"
            />
        </div>

        <h1>
            Pembayaran Gagal
        </h1>

        <p class="success-message">
            Batas waktu pembayaran telah habis
            atau transaksi tidak dapat diselesaikan.
        </p>

    </template>


    <div class="order-info">

        <div class="order-row">

            <span>
                Metode Pembayaran
            </span>

            <strong>
                {{ metodePembayaranLabel }}
            </strong>

        </div>

        <div class="order-divider"></div>

        <div class="order-row order-row--total">

            <span>
                Total Pesanan
            </span>

            <strong>
                {{ formatRupiah(props.pesanan.total_harga) }}
            </strong>

        </div>

    </div>


    <Link
        href="/Pelanggan"
        class="order-button"
    >
        Lihat Pesanan
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
    --hf-green: #3f6e69;
    --hf-green-dark: #2f5652;
    --hf-sage: #cddbd6;
    --hf-sage-soft: #eef4f2;
    --hf-bg: #f7f9f8;
    --hf-ink: #2c3f3d;
    --hf-ink-soft: #5f7472;
    --hf-ink-faint: #98a6a4;
    --hf-border: #e4ece9;
    --hf-amber: #b8862f;
    --hf-amber-bg: #f7f0e2;
    --hf-red: #b5504b;
    --hf-red-bg: #fbeeee;

    min-height: 100vh;

    background: #ffffff;
    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}


.success-header {
    height: 72px;

    display: flex;
    align-items: center;
    gap: 12px;

    padding: 0 6%;

    border-bottom: 1px solid var(--hf-border);
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

    color: var(--hf-green);

    text-decoration: none;

    transition: background .15s ease, border-color .15s ease;
}

.back-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.header-eyebrow {
    font-size: 10px;
    letter-spacing: .12em;
    text-transform: uppercase;

    color: var(--hf-ink-faint);
}


.success-container {
    min-height: calc(100vh - 72px);

    display: flex;
    align-items: flex-start;
    justify-content: center;

    padding: 64px 20px;
}


.success-card {
    width: min(460px, 100%);

    text-align: center;

    animation: fadeInUp .45s ease both;
}

@keyframes fadeInUp {

    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}


.status-icon {
    width: 80px;
    height: 80px;

    margin: 0 auto 26px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;
}

.status-icon--success {
    background: var(--hf-sage-soft);
    color: var(--hf-green);

    box-shadow: 0 8px 22px rgba(63, 110, 105, .10);
}

.status-icon--pending {
    background: var(--hf-amber-bg);
    color: var(--hf-amber);

    box-shadow: 0 8px 22px rgba(184, 134, 47, .10);

    animation: pulseClock 2.2s ease-in-out infinite;
}

.status-icon--failed {
    background: var(--hf-red-bg);
    color: var(--hf-red);

    box-shadow: 0 8px 22px rgba(181, 80, 75, .08);
}


.success-card h1 {
    margin: 0 0 12px;

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 26px;
    font-weight: 400;
    line-height: 1.25;

    color: var(--hf-ink);
}


.success-message {
    max-width: 380px;

    margin: 0 auto 30px;

    color: var(--hf-ink-soft);

    font-size: 13px;
    line-height: 1.7;
}


.order-info {
    max-width: 400px;

    margin: 0 auto;
    padding: 16px 18px;

    background: var(--hf-bg);
    border: 1px solid var(--hf-border);
    border-radius: 10px;
}

.order-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-row span {
    color: var(--hf-ink-faint);
    font-size: 11px;
}

.order-row strong {
    color: var(--hf-ink);
    font-size: 12px;
    font-weight: 600;
}

.order-divider {
    height: 1px;

    margin: 12px 0;

    background: var(--hf-border);
}

.order-row--total strong {
    color: var(--hf-green-dark);
    font-size: 17px;
    font-weight: 700;
}

.order-row--total span {
    color: var(--hf-ink-soft);
    font-size: 11.5px;
    font-weight: 600;
}


.order-button {
    width: 100%;
    max-width: 400px;
    height: 50px;

    margin: 20px auto 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid var(--hf-border);
    border-radius: 9px;

    background: #ffffff;
    color: var(--hf-ink);

    text-align: center;
    text-decoration: none;

    font-size: 13px;
    font-weight: 600;

    transition: background .15s ease, border-color .15s ease;
}


.order-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}


.countdown-box {
    width: min(400px, 100%);

    margin: 24px auto 0;

    padding: 16px 18px;

    border-radius: 10px;

    background: var(--hf-sage-soft);
    border: 1px solid var(--hf-border);

    text-align: center;
}

.countdown-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    margin-bottom: 8px;

    color: var(--hf-ink-soft);
    font-size: 10.5px;
}

.countdown-box strong {
    display: block;

    color: var(--hf-green-dark);

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 2px;
    font-variant-numeric: tabular-nums;
}

.pay-button {
    width: min(400px, 100%);
    height: 50px;

    margin: 14px auto 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 9px;

    background: var(--hf-green);
    color: #ffffff;

    font-size: 13px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease, transform .1s ease;
}

.pay-button:hover {
    background: var(--hf-green-dark);
}

.pay-button:active {
    transform: translateY(1px);
}

@keyframes pulseClock {

    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.04);
    }

    100% {
        transform: scale(1);
    }

}

@media (max-width: 600px) {

    .success-header {
        padding: 0 16px;
    }

    .success-container {
        padding: 44px 18px;
    }

    .status-icon {
        width: 70px;
        height: 70px;

        margin-bottom: 22px;
    }

    .success-card h1 {
        font-size: 22px;
    }

    .success-message {
        font-size: 12.5px;
    }

    .countdown-box strong {
        font-size: 22px;
    }

    .order-info,
    .order-button,
    .countdown-box,
    .pay-button {
        max-width: 100%;
    }

}

</style>