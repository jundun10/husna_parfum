<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

const regencies = ref([]);
const districts = ref([]);
const villages = ref([]);

const loadingRegencies = ref(false);
const loadingDistricts = ref(false);
const loadingVillages = ref(false);
const props = defineProps({
    
    alamat: {
        type: Object,
        default: null,
    },

    authUser: {
        type: Object,
        default: null,
    },

    provinces: {
        type: Array,
        default: () => [],
    },
});
const isEditing = ref(!props.alamat);


const form = useForm({
    nama_penerima: props.alamat?.nama_penerima ?? props.authUser?.name ?? '',
    no_hp: props.alamat?.no_hp ?? '',

    provinsi: props.alamat?.provinsi ?? '',
    kabupaten_kota: props.alamat?.kabupaten_kota ?? '',
    kecamatan: props.alamat?.kecamatan ?? '',
    desa: props.alamat?.desa ?? '',

    alamat_lengkap: props.alamat?.alamat_lengkap ?? '',
    kode_pos: props.alamat?.kode_pos ?? '',
});


watch(
    () => form.provinsi,
    async (provinceCode) => {
        regencies.value = [];
        districts.value = [];
        villages.value = [];

        form.kabupaten_kota = '';
        form.kecamatan = '';
        form.desa = '';

        if (!provinceCode) return;

        loadingRegencies.value = true;

        try {
            const response = await fetch(
            `/api/wilayah/regencies/${provinceCode}`
        );

        const result = await response.json();

regencies.value = result;
        } catch (error) {
            console.error('Gagal mengambil kabupaten:', error);
        } finally {
            loadingRegencies.value = false;
        }
    }
);
watch(
    () => form.kabupaten_kota,
    async (regencyCode) => {
        districts.value = [];
        villages.value = [];

        form.kecamatan = '';
        form.desa = '';

        if (!regencyCode) return;

        loadingDistricts.value = true;

        try {
            const response = await fetch(
                `/api/wilayah/districts/${regencyCode}`
            );

            const result = await response.json();

            districts.value = result;
        } catch (error) {
            console.error('Gagal mengambil kecamatan:', error);
            districts.value = [];
        } finally {
            loadingDistricts.value = false;
        }
    }
);
watch(
    () => form.kecamatan,
    async (districtCode) => {
        villages.value = [];

        form.desa = '';

        if (!districtCode) return;

        loadingVillages.value = true;

        try {
            const response = await fetch(
                `/api/wilayah/villages/${districtCode}`
            );

            const result = await response.json();

            villages.value = result;
        } catch (error) {
            console.error('Gagal mengambil desa:', error);
            villages.value = [];
        } finally {
            loadingVillages.value = false;
        }
    }
);
const loadExistingAddress = async () => {
    if (!props.alamat) return;

    try {
        if (form.provinsi) {
            loadingRegencies.value = true;

            const regencyResponse = await fetch(
                `/api/wilayah/regencies/${form.provinsi}`
            );

            regencies.value = await regencyResponse.json();

            loadingRegencies.value = false;
        }

        if (form.kabupaten_kota) {
            loadingDistricts.value = true;

            const districtResponse = await fetch(
                `/api/wilayah/districts/${form.kabupaten_kota}`
            );

            districts.value = await districtResponse.json();

            loadingDistricts.value = false;
        }

        if (form.kecamatan) {
            loadingVillages.value = true;

            const villageResponse = await fetch(
                `/api/wilayah/villages/${form.kecamatan}`
            );

            villages.value = await villageResponse.json();

            loadingVillages.value = false;
        }
    } catch (error) {
        console.error('Gagal memuat alamat lama:', error);

        loadingRegencies.value = false;
        loadingDistricts.value = false;
        loadingVillages.value = false;
    }
};

onMounted(() => {
    if (props.alamat) {
        loadExistingAddress();
    }
});
const submit = () => {
    form.post('/pelanggan/alamat');
};
</script>

<template>
    <Head title="Alamat Pengiriman" />

    <div class="address-page">

        <header class="address-header">

            <Link
                href="/pelanggan/checkout"
                class="back-button"
            >
                <ArrowLeft :size="19" />
            </Link>

            <div class="address-title">
                <div class="address-title-text">
                    <span class="header-eyebrow"></span>
                    <h1>Alamat Pengiriman</h1>
                </div>

                <button
                    v-if="props.alamat && !isEditing"
                    type="button"
                    class="edit-address-button"
                    @click="isEditing = true"
                >
                    Edit
                </button>
            </div>

        </header>

        <div class="address-container">

            <div
                v-if="props.alamat && !isEditing"
                class="saved-address-card"
            >
                <div class="saved-address-top">
                    <div>
                        <span class="saved-label">
                            Alamat Utama
                        </span>

                        <h2>
                            {{ props.alamat.nama_penerima }}
                        </h2>
                    </div>

                    <span class="saved-phone">
                        {{ props.alamat.no_hp }}
                    </span>
                </div>

                <div class="saved-divider"></div>

                <p>
                    {{ props.alamat.alamat_lengkap }},
                    {{ props.alamat.desa }},
                    {{ props.alamat.kecamatan }},
                    {{ props.alamat.kabupaten_kota }},
                    {{ props.alamat.provinsi }},
                    {{ props.alamat.kode_pos }}
                </p>
            </div>

            <form
                v-if="isEditing"
                class="address-card"
                @submit.prevent="submit"
            >

                <div class="form-section-label">
                    Data Penerima
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Nama Penerima</label>

                        <input
                            v-model="form.nama_penerima"
                            type="text"
                            placeholder="Nama penerima"
                        >

                        <span
                            v-if="form.errors.nama_penerima"
                            class="error"
                        >
                            {{ form.errors.nama_penerima }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Nomor HP</label>

                        <input
                            v-model="form.no_hp"
                            type="text"
                            placeholder="08"
                        >

                        <span
                            v-if="form.errors.no_hp"
                            class="error"
                        >
                            {{ form.errors.no_hp }}
                        </span>
                    </div>

                </div>

                <div class="form-section-label">
                    Wilayah
                </div>

                <div class="form-group">
                    <label>Provinsi</label>

                    <select v-model="form.provinsi">
                        <option value="">
                            Pilih Provinsi
                        </option>

                        <option
                            v-for="province in props.provinces"
                            :key="province.code"
                            :value="province.code"
                        >
                            {{ province.name }}
                        </option>
                    </select>

                    <span
                        v-if="form.errors.provinsi"
                        class="error"
                    >
                        {{ form.errors.provinsi }}
                    </span>
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Kabupaten / Kota</label>

                        <select
                            v-model="form.kabupaten_kota"
                            :disabled="
                                !form.provinsi ||
                                loadingRegencies
                            "
                        >
                            <option value="">
                                {{
                                    loadingRegencies
                                        ? 'Memuat...'
                                        : 'Pilih Kabupaten / Kota'
                                }}
                            </option>

                            <option
                                v-for="regency in regencies"
                                :key="regency.code"
                                :value="regency.code"
                            >
                                {{ regency.name }}
                            </option>
                        </select>

                        <span
                            v-if="form.errors.kabupaten_kota"
                            class="error"
                        >
                            {{ form.errors.kabupaten_kota }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Kecamatan</label>

                        <select
                            v-model="form.kecamatan"
                            :disabled="
                                !form.kabupaten_kota ||
                                loadingDistricts
                            "
                        >
                            <option value="">
                                {{
                                    loadingDistricts
                                        ? 'Memuat...'
                                        : 'Pilih Kecamatan'
                                }}
                            </option>

                            <option
                                v-for="district in districts"
                                :key="district.code"
                                :value="district.code"
                            >
                                {{ district.name }}
                            </option>
                        </select>

                        <span
                            v-if="form.errors.kecamatan"
                            class="error"
                        >
                            {{ form.errors.kecamatan }}
                        </span>
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Desa / Kelurahan</label>

                        <select
                            v-model="form.desa"
                            :disabled="
                                !form.kecamatan ||
                                loadingVillages
                            "
                        >
                            <option value="">
                                {{
                                    loadingVillages
                                        ? 'Memuat...'
                                        : 'Pilih Desa / Kelurahan'
                                }}
                            </option>

                            <option
                                v-for="village in villages"
                                :key="village.code"
                                :value="village.code"
                            >
                                {{ village.name }}
                            </option>
                        </select>

                        <span
                            v-if="form.errors.desa"
                            class="error"
                        >
                            {{ form.errors.desa }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Kode Pos</label>

                        <input
                            v-model="form.kode_pos"
                            type="text"
                            placeholder="Kode pos"
                        >

                        <span
                            v-if="form.errors.kode_pos"
                            class="error"
                        >
                            {{ form.errors.kode_pos }}
                        </span>
                    </div>

                </div>

                <div class="form-section-label">
                    Detail Alamat
                </div>

                <div class="form-group form-group--full">
                    <label>Alamat Lengkap</label>

                    <textarea
                        v-model="form.alamat_lengkap"
                        rows="5"
                        placeholder="Nama jalan, nomor rumah, RT/RW, patokan, dan sebagainya"
                    ></textarea>

                    <span
                        v-if="form.errors.alamat_lengkap"
                        class="error"
                    >
                        {{ form.errors.alamat_lengkap }}
                    </span>
                </div>

                <button
                    type="submit"
                    class="save-button"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Alamat' }}
                </button>

            </form>

        </div>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.address-page {
    --hf-green: #3f6e69;
    --hf-green-dark: #2f5652;
    --hf-sage: #cddbd6;
    --hf-sage-soft: #eef4f2;
    --hf-bg: #f7f9f8;
    --hf-ink: #2c3f3d;
    --hf-ink-soft: #5f7472;
    --hf-ink-faint: #98a6a4;
    --hf-border: #e4ece9;
    --hf-red: #b5504b;

    min-height: 100vh;
    padding: 0 0 60px;

    background: var(--hf-bg);
    color: var(--hf-ink);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.address-header {
    height: 76px;

    display: flex;
    align-items: center;
    gap: 16px;

    padding: 0 6%;

    margin-bottom: 28px;

    background: rgba(255, 255, 255, .97);
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

    background: #ffffff;
    color: var(--hf-green);

    cursor: pointer;
    text-decoration: none;

    transition: background .15s ease, border-color .15s ease;
}

.back-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.address-title {
    flex: 1;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 16px;
}

.address-title-text {
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

.address-header h1 {
    margin: 0;

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 22px;
    font-weight: 400;

    color: var(--hf-ink);
}

.edit-address-button {
    flex-shrink: 0;

    padding: 9px 16px;

    border: 1px solid var(--hf-border);
    border-radius: 8px;

    background: #ffffff;
    color: var(--hf-green);

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease, border-color .15s ease;
}

.edit-address-button:hover {
    background: var(--hf-sage-soft);
    border-color: var(--hf-sage);
}

.address-container {
    width: min(640px, 100%);
    margin: 0 auto;
    padding: 0 6%;
}

.saved-address-card {
    padding: 22px;

    margin-bottom: 20px;

    background: #ffffff;

    border: 1px solid var(--hf-border);
    border-radius: 12px;
}

.saved-address-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 16px;
}

.saved-label {
    display: block;

    margin-bottom: 6px;

    color: var(--hf-ink-faint);

    font-size: 10px;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.saved-address-top h2 {
    margin: 0;

    font-family: Georgia, 'Times New Roman', serif;
    font-size: 18px;
    font-weight: 400;

    color: var(--hf-ink);
}

.saved-phone {
    flex-shrink: 0;

    color: var(--hf-ink-soft);
    font-size: 11.5px;
}

.saved-divider {
    height: 1px;

    margin: 14px 0;

    background: var(--hf-border);
}

.saved-address-card p {
    margin: 0;

    color: var(--hf-ink-soft);

    font-size: 11.5px;
    line-height: 1.8;
}

.address-card {
    padding: 26px;

    background: #ffffff;

    border: 1px solid var(--hf-border);
    border-radius: 12px;
}

.form-section-label {
    margin: 0 0 14px;

    color: var(--hf-ink-faint);

    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.address-card > .form-section-label:not(:first-child) {
    margin-top: 24px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group--full {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 7px;

    color: var(--hf-ink-soft);
    font-size: 10.5px;
    font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;

    height: 44px;

    padding: 0 13px;

    border: 1px solid var(--hf-border);
    border-radius: 8px;

    outline: none;

    background: var(--hf-bg);
    color: var(--hf-ink);

    font-size: 12px;
    font-family: inherit;

    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
}

.form-group textarea {
    height: auto;
    padding: 12px 13px;
    line-height: 1.6;
    resize: vertical;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: var(--hf-ink-faint);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--hf-green);
    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(63, 110, 105, .10);
}

.form-group select:disabled {
    background: #eef1f0;

    color: var(--hf-ink-faint);

    cursor: not-allowed;
}

.error {
    display: block;
    margin-top: 6px;

    color: var(--hf-red);
    font-size: 10px;
}

.save-button {
    width: 100%;
    height: 48px;

    margin-top: 6px;

    border: none;
    border-radius: 9px;

    background: var(--hf-green);
    color: #ffffff;

    font-size: 12.5px;
    font-weight: 600;

    cursor: pointer;

    transition: background .15s ease, transform .1s ease;
}

.save-button:hover:not(:disabled) {
    background: var(--hf-green-dark);
}

.save-button:active:not(:disabled) {
    transform: translateY(1px);
}

.save-button:disabled {
    background: #cbd7d5;
    cursor: not-allowed;
}

@media (max-width: 650px) {

    .address-header {
        height: 66px;
        padding: 0 16px;
        margin-bottom: 20px;
    }

    .address-header h1 {
        font-size: 19px;
    }

    .address-container {
        padding: 0 16px;
    }

    .address-card,
    .saved-address-card {
        padding: 18px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .saved-address-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
}
</style>