<template>
    <Transition name="loading-fade">
        <div v-if="loading" class="loading-overlay">
            <div class="loading-spinner"></div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const loading = ref(false);

let startHandler;
let finishHandler;

onMounted(() => {
    startHandler = () => {
        loading.value = true;
    };

    finishHandler = () => {
        loading.value = false;
    };

    router.on('start', startHandler);
    router.on('finish', finishHandler);
});

onUnmounted(() => {
    router.on('start', startHandler);
    router.on('finish', finishHandler);
});
</script>

<style scoped>
.loading-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(255, 255, 255, .35);
    backdrop-filter: blur(2px);
}

.loading-spinner {
    width: 42px;
    height: 42px;

    border: 4px solid #dfecea;
    border-top-color: #5d8986;
    border-radius: 50%;

    animation: spin .8s linear infinite;
}

.loading-fade-enter-active,
.loading-fade-leave-active {
    transition: opacity .15s ease;
}

.loading-fade-enter-from,
.loading-fade-leave-to {
    opacity: 0;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>