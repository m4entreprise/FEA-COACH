<script setup>
import axios from 'axios';
import { MonitorPlay, Maximize2, Minimize2, RefreshCw } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    payload: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: 'Aperçu en direct',
    },
    subtitle: {
        type: String,
        default: 'Prévisualisez votre site en temps réel.',
    },
});

const previewHtml = ref('');
const previewLoading = ref(false);
const previewError = ref(null);
let previewTimeoutId = null;
const isFullscreen = ref(false);

const payloadKey = computed(() => JSON.stringify(props.payload || {}));

const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
};

watch(isFullscreen, (active) => {
    document.body.classList.toggle('overflow-hidden', active);
});

const fetchPreview = async () => {
    previewLoading.value = true;
    previewError.value = null;

    try {
        const { data } = await axios.post(route('setup.preview'), props.payload, {
            headers: { Accept: 'application/json' },
            withCredentials: true,
        });

        previewHtml.value = data.html;
    } catch (error) {
        previewError.value =
            error.response?.data?.message || 'Impossible de générer l’aperçu pour le moment.';
        previewHtml.value = '';
    } finally {
        previewLoading.value = false;
    }
};

const schedulePreview = () => {
    if (previewTimeoutId) {
        clearTimeout(previewTimeoutId);
    }

    previewTimeoutId = window.setTimeout(() => {
        fetchPreview();
    }, 600);
};

watch(
    payloadKey,
    () => {
        schedulePreview();
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    if (previewTimeoutId) {
        clearTimeout(previewTimeoutId);
    }
});
</script>

<template>
    <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-sky-500 flex items-center justify-center shadow-lg flex-shrink-0">
                    <MonitorPlay class="h-5 w-5 text-white" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Preview</p>
                    <h3 class="text-base font-semibold text-slate-50">{{ title }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ subtitle }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800 transition-colors"
                    @click="fetchPreview"
                    :disabled="previewLoading"
                >
                    <RefreshCw class="h-4 w-4" />
                    Actualiser
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800 transition-colors"
                    @click="toggleFullscreen"
                >
                    <Maximize2 v-if="!isFullscreen" class="h-4 w-4" />
                    <Minimize2 v-else class="h-4 w-4" />
                    {{ isFullscreen ? 'Réduire' : 'Plein écran' }}
                </button>
            </div>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-800 bg-slate-950/50 overflow-hidden relative">
            <div v-if="previewError" class="p-6 text-sm text-rose-200">
                <p class="font-semibold mb-2">Erreur d’aperçu</p>
                <p class="text-xs text-rose-200/80">{{ previewError }}</p>
            </div>

            <div
                v-else-if="previewLoading && !previewHtml"
                class="absolute inset-0 flex items-center justify-center text-slate-300 text-sm"
            >
                Chargement de l’aperçu...
            </div>

            <iframe
                v-show="previewHtml"
                :key="payloadKey + String(isFullscreen)"
                class="w-full h-[34rem] bg-white overflow-x-hidden"
                sandbox="allow-same-origin allow-forms allow-scripts"
                :srcdoc="previewHtml"
            ></iframe>
        </div>

        <teleport to="body">
            <div
                v-if="isFullscreen"
                class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-xl flex flex-col"
            >
                <div class="flex flex-wrap items-start justify-between gap-4 px-6 py-4 border-b border-slate-800 text-slate-200">
                    <div class="flex items-center gap-3">
                        <MonitorPlay class="h-5 w-5 text-indigo-300" />
                        <div>
                            <p class="text-sm font-semibold">Aperçu plein écran</p>
                            <p class="text-xs text-slate-400">{{ title }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-300">
                        <span v-if="previewLoading" class="animate-pulse text-yellow-300">Mise à jour…</span>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-full border border-slate-600 px-3 py-1 hover:text-white hover:border-slate-400"
                            @click="toggleFullscreen"
                        >
                            Fermer
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-hidden p-4">
                    <iframe
                        v-if="previewHtml"
                        class="w-full h-full rounded-2xl bg-white shadow-2xl overflow-x-hidden"
                        sandbox="allow-same-origin allow-forms allow-scripts"
                        :srcdoc="previewHtml"
                    ></iframe>
                    <div
                        v-else-if="previewError"
                        class="flex h-full items-center justify-center text-center text-red-300 text-sm px-10"
                    >
                        {{ previewError }}
                    </div>
                    <div
                        v-else
                        class="flex h-full items-center justify-center text-center text-slate-300 text-sm px-10"
                    >
                        Chargement de l’aperçu...
                    </div>
                </div>
            </div>
        </teleport>
    </section>
</template>
