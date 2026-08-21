<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

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
        href="/pelanggan/keranjang"
        class="back-link"
    >
        ←
    </Link>

    <div class="address-title">
        <div>
            <h1>Alamat Pengiriman</h1>

            <p>
                {{
                    isEditing
                        ? 'Lengkapi atau ubah alamat pengiriman.'
                        : 'Alamat pengiriman akun kamu.'
                }}
            </p>
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

    <div
    v-if="props.alamat && !isEditing"
    class="saved-address-card"
>
    <div class="saved-address-top">
        <div>
            <span class="saved-label">
                ALAMAT UTAMA
            </span>

            <h2>
                {{ props.alamat.nama_penerima }}
            </h2>
        </div>

        <span class="saved-phone">
            {{ props.alamat.no_hp }}
        </span>
    </div>

    <p>
        {{ props.alamat.alamat_lengkap }},
        {{ props.alamat.desa }},
        {{ props.alamat.kecamatan }},
        {{ props.alamat.kabupaten_kota }},
        {{ props.alamat.provinsi }},
        {{ props.alamat.kode_pos }}
    </p>
    </div>

        <main class="address-container">

            <form
                v-if="isEditing"
                class="address-card"
                @submit.prevent="submit"
            >

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

                <div class="form-group">
                    <label>Kabupaten</label>

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

                <div class="form-group">
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

        </main>

    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.address-page {
    min-height: 100vh;
    padding: 30px 6% 60px;
    background: #f5f8f7;
    color: #304c4b;
}

.address-header {
    width: min(900px, 100%);
    margin: 0 auto 25px;

    display: flex;
    align-items: flex-start;
    gap: 25px;
}
.address-title {
    flex: 1;

    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 20px;
}

.edit-address-button {
    padding: 8px 14px;

    border: 1px solid #dce7e5;
    border-radius: 8px;

    background: #ffffff;
    color: #568381;

    font-size: 10px;

    cursor: pointer;

    transition: .2s ease;
}

.edit-address-button:hover {
    background: #edf4f2;
}

.saved-address-card {
    width: min(900px, 100%);

    margin: 0 auto 20px;

    padding: 22px;

    background: #ffffff;

    border: 1px solid #dce7e5;
    border-radius: 14px;

    box-shadow: 0 10px 30px rgba(70, 95, 90, .05);
}

.saved-address-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin-bottom: 10px;
}

.saved-label {
    display: block;

    margin-bottom: 5px;

    color: #8da3a1;

    font-size: 7px;
    letter-spacing: 2px;
}

.saved-address-top h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 19px;
    font-weight: normal;

    color: #304c4b;
}

.saved-phone {
    color: #6f8885;

    font-size: 10px;
}

.saved-address-card p {
    margin: 0;

    color: #718381;

    font-size: 11px;
    line-height: 1.8;
}
.back-link {
    color: #477c79;
    text-decoration: none;
    font-size: 11px;
    white-space: nowrap;
}

.address-header h1 {
    margin: 0 0 6px;
    font-family: Georgia, serif;
    font-size: 28px;
    font-weight: normal;
}

.address-header p {
    margin: 0;
    font-size: 11px;
    color: #91a3a1;
}

.address-container {
    width: min(900px, 100%);
    margin: 0 auto;
}

.address-card {
    padding: 28px;

    background: #ffffff;

    border: 1px solid #dce7e5;
    border-radius: 14px;

    box-shadow: 0 10px 30px rgba(70, 95, 90, .05);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;

    color: #526b69;
    font-size: 10px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;

    height: 42px;

    padding: 0 12px;

    border: 1px solid #dce7e5;
    border-radius: 8px;

    outline: none;

    background: #f9fbfa;
    color: #526b69;

    font-size: 11px;

    transition: .2s ease;
}

.form-group textarea {
    height: auto;
    padding: 11px 12px;
    resize: vertical;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #8fb5b0;
    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(143, 181, 176, .10);
}
.form-group select:disabled {
    background: #f0f4f3;

    color: #a1afad;

    cursor: not-allowed;
}

.error {
    display: block;
    margin-top: 5px;
    color: #c77d7d;
    font-size: 9px;
}

.save-button {
    width: 100%;

    height: 44px;

    border: none;
    border-radius: 9px;

    background: #5d8986;
    color: white;

    font-size: 11px;

    cursor: pointer;
}

.save-button:hover {
    background: #477c79;
}

.save-button:disabled {
    opacity: .6;
    cursor: not-allowed;
}

@media (max-width: 650px) {

    .address-page {
        padding: 20px 15px 40px;
    }

    .address-header {
        gap: 12px;
    }

    .address-header h1 {
        font-size: 23px;
    }

    .address-card {
        padding: 20px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
}
</style>