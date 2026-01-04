<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch, onBeforeUnmount } from 'vue';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { Toaster, toast } from 'vue-sonner';
import { VueDraggable } from 'vue-draggable-plus';
import {
    Clock,
    Euro,
    GripVertical,
    Plus,
    Activity,
    Check,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    services: Array,
});

const showModal = ref(false);
const editingService = ref(null);
const draggingId = ref(null);
const reorderSaving = ref(false);
const reorderError = ref(null);
const servicesList = ref([]);
const imageInput = ref(null);
const imagePreview = ref(null);

const dashboardBackUrl = computed(() => {
    const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
    return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
    router.visit(dashboardBackUrl.value);
};

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }
});

watch(
    () => props.services,
    (value) => {
        servicesList.value = [...(value || [])].sort(
            (a, b) => (a.order ?? 0) - (b.order ?? 0),
        );
    },
    { immediate: true },
);

const form = useForm({
    name: '',
    description: '',
    duration_minutes: 60,
    price: '',
    currency: 'EUR',
    is_active: true,
    booking_enabled: true,
    is_featured: false,
    max_advance_booking_days: 60,
    min_advance_booking_hours: 24,
    order: 0,
    image: null,
    remove_image: false,
});

const pendingEditorContent = ref('');

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            blockquote: false,
            codeBlock: false,
            heading: false,
            horizontalRule: false,
            orderedList: false,
            strike: false,
        }),
    ],
    content: '',
    editorProps: {
        attributes: {
            class: 'focus:outline-none text-sm leading-relaxed text-slate-100 caret-emerald-400',
            spellcheck: 'false',
        },
    },
    onUpdate: ({ editor }) => {
        form.description = editor.getHTML();
    },
});

const syncEditorContent = (value) => {
    pendingEditorContent.value = value || '';
    if (editor.value) {
        editor.value.commands.setContent(
            pendingEditorContent.value.trim() ? pendingEditorContent.value : '<p></p>',
            false,
        );
    }
};

watch(
    editor,
    (instance) => {
        if (instance) {
            instance.commands.setContent(
                pendingEditorContent.value.trim() ? pendingEditorContent.value : '<p></p>',
                false,
            );
        }
    },
    { immediate: true },
);

const openCreateModal = () => {
    editingService.value = null;
    form.reset();
    form.clearErrors();
    form.duration_minutes = 60;
    form.currency = 'EUR';
    form.is_active = true;
    form.booking_enabled = true;
    form.is_featured = false;
    form.max_advance_booking_days = 60;
    form.min_advance_booking_hours = 24;
    form.order = servicesList.value.length;
    form.image = null;
    form.remove_image = false;
    imagePreview.value = null;
    form.description = '';
    syncEditorContent('');
    if (imageInput.value) {
        imageInput.value.value = null;
    }
    showModal.value = true;
};

const openEditModal = (service) => {
    editingService.value = service;
    form.name = service.name;
    form.description = service.description || '';
    form.duration_minutes = service.duration_minutes;
    form.price = service.price ?? '';
    form.currency = service.currency || 'EUR';
    form.is_active = service.is_active;
    form.booking_enabled = service.booking_enabled ?? true;
    form.is_featured = service.is_featured ?? false;
    form.max_advance_booking_days = service.max_advance_booking_days ?? 60;
    form.min_advance_booking_hours = service.min_advance_booking_hours ?? 24;
    form.order = service.order ?? 0;
    form.image = null;
    form.remove_image = false;
    syncEditorContent(form.description);
    imagePreview.value = service.image_url ?? null;
    if (imageInput.value) {
        imageInput.value.value = null;
    }
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingService.value = null;
    form.reset();
    form.clearErrors();
    form.image = null;
    form.remove_image = false;
    form.description = '';
    syncEditorContent('');
    imagePreview.value = null;
    if (imageInput.value) {
        imageInput.value.value = null;
    }
};

const resetTransform = () => {
    form.transform((data) => data);
};

const submit = () => {
    if (editingService.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PATCH',
        }));
        form.post(route('dashboard.services.update', editingService.value.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success('Service mis à jour avec succès');
            },
            onError: () => {
                toast.error('Impossible de mettre à jour le service', {
                    description: 'Vérifiez les champs requis puis réessayez.',
                });
            },
            onFinish: resetTransform,
        });
    } else {
        resetTransform();
        form.order = servicesList.value.length;
        form.post(route('dashboard.services.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success('Service créé avec succès');
            },
            onError: () => {
                toast.error('Création impossible', {
                    description: 'Corrigez les champs requis puis réessayez.',
                });
            },
        });
    }
};

const handleImageChange = (event) => {
    const file = event.target?.files?.[0];
    if (!file) {
        return;
    }

    form.image = file;
    form.remove_image = false;
    imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
    form.image = null;
    if (editingService.value) {
        form.remove_image = true;
    }
    imagePreview.value = null;
    if (imageInput.value) {
        imageInput.value.value = null;
    }
};

const deleteService = (service) => {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer "${service.name}" ?`)) return;

    router.delete(route('dashboard.services.destroy', service.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Service supprimé'),
        onError: () =>
            toast.error('Suppression impossible', {
                description: 'Réessayez dans un instant.',
            }),
    });
};

const onDragStart = (event) => {
    const id = Number(event?.item?.dataset?.id);
    draggingId.value = Number.isNaN(id) ? null : id;
};

const onDragFinish = () => {
    draggingId.value = null;
    handleReorder();
};

const handleReorder = () => {
    servicesList.value = servicesList.value.map((service, index) => ({
        ...service,
        order: index,
    }));
    saveOrder();
};

const saveOrder = async () => {
    reorderSaving.value = true;
    reorderError.value = null;

    try {
        await axios.post(
            route('dashboard.services.reorder'),
            {
                services: servicesList.value.map((service, index) => ({
                    id: service.id,
                    order: index,
                })),
            },
            {
                headers: { Accept: 'application/json' },
            },
        );
        toast.success('Ordre mis à jour');
    } catch (error) {
        reorderError.value =
            error.response?.data?.message || 'Impossible d’enregistrer le nouvel ordre.';
        toast.error('Synchronisation impossible', {
            description: reorderError.value,
        });
    } finally {
        reorderSaving.value = false;
    }
};
</script>

<template>
    <Head title="Mes services" />

    <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
        <Toaster rich-colors theme="dark" position="top-right" close-button />
        <header
            class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl"
        >
            <div class="flex flex-col">
                <p class="text-xs uppercase tracking-wide text-slate-400">
                    Panel coach
                </p>
                <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
                    <span>Services & séances</span>
                </h1>
            </div>

            <button
                type="button"
                @click="goBack"
                class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs font-medium text-slate-100 hover:border-slate-500 hover:bg-slate-800"
            >
                <span class="text-xs">←</span>
                <span>Retour panel</span>
            </button>
        </header>

        <main
            class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8"
        >
            <div class="max-w-6xl mx-auto space-y-6">
                <section
                    class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
                >
                    <div>
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <Activity class="h-4 w-4 text-emerald-400" />
                            Vos prestations
                        </h2>
                        <p class="text-sm text-slate-400">
                            Les services listés ici sont proposés lors de la réservation directe.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
                        @click="openCreateModal"
                    >
                        <Plus class="h-3.5 w-3.5" />
                        <span>Nouveau service</span>
                    </button>
                </section>

                <section
                    class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
                >
                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
                            <span>{{ servicesList.length }} service(s) affichés</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-400">Glissez-déposez pour trier vos séances.</span>
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-[11px]"
                                :class="[
                                    reorderSaving
                                        ? 'border-yellow-400/40 text-yellow-200 bg-yellow-400/10'
                                        : reorderError
                                            ? 'border-rose-500/40 text-rose-200 bg-rose-500/10'
                                            : 'border-slate-700 text-slate-300 bg-slate-800/60',
                                ]"
                            >
                                <span v-if="reorderSaving">Enregistrement…</span>
                                <span v-else-if="reorderError">{{ reorderError }}</span>
                                <span v-else>Ordre synchronisé</span>
                            </span>
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <VueDraggable
                        v-if="servicesList.length"
                        v-model="servicesList"
                        item-key="id"
                        handle=".service-drag-handle"
                        tag="div"
                        class="grid gap-5 md:grid-cols-2 lg:grid-cols-3"
                        v-auto-animate
                        ghost-class="drag-ghost"
                        chosen-class="drag-chosen"
                        :animation="220"
                        @start="onDragStart"
                        @end="onDragFinish"
                    >
                        <template #item="{ element: service }">
                            <article
                                :key="service.id"
                                :data-id="service.id"
                                class="rounded-2xl border bg-slate-900/80 p-5 shadow-xl flex flex-col gap-3 transition"
                                :class="[
                                    draggingId === service.id
                                        ? 'border-indigo-500/70 bg-slate-900'
                                        : 'border-slate-800 hover:border-slate-700',
                                ]"
                            >
                                <div class="flex items-start gap-4">
                                    <button
                                        type="button"
                                        class="service-drag-handle h-10 w-10 rounded-xl border border-slate-800 bg-slate-950 flex items-center justify-center text-slate-400 hover:text-slate-100"
                                    >
                                        <GripVertical class="h-4 w-4" />
                                    </button>
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="space-y-1">
                                                <p class="text-xs uppercase tracking-wide text-slate-500">
                                                    Service
                                                </p>
                                                <div class="flex items-center flex-wrap gap-2">
                                                    <h3 class="text-sm font-semibold text-slate-50">
                                                        {{ service.name }}
                                                    </h3>
                                                    <span
                                                        v-if="service.is_featured"
                                                        class="inline-flex items-center rounded-full border border-amber-400/50 bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-200"
                                                    >
                                                        Offre mise en avant
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end gap-1 text-right">
                                                <span
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold"
                                                    :class="
                                                        service.is_active
                                                            ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                                                            : 'border-slate-700 bg-slate-800 text-slate-300'
                                                    "
                                                >
                                                    <Check v-if="service.is_active" class="h-3 w-3 mr-1" />
                                                    <X v-else class="h-3 w-3 mr-1" />
                                                    {{ service.is_active ? 'Actif' : 'Masqué' }}
                                                </span>
                                                <span class="text-[11px] text-slate-500">
                                                    Position : {{ (service.order ?? 0) + 1 }}
                                                </span>
                                            </div>
                                        </div>
                                        <div
                                            class="relative w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-950"
                                        >
                                            <img
                                                v-if="service.image_url"
                                                :src="service.image_url"
                                                :alt="`Illustration ${service.name}`"
                                                class="h-40 w-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="h-40 w-full flex flex-col items-center justify-center text-slate-500 text-xs gap-2 bg-slate-900"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l6-6 4 4 5.5-5.5M15 7.5h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                                                </svg>
                                                <span>Pas d’image pour l’instant</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 text-sm">
                                            <div class="inline-flex items-center gap-2 text-slate-300">
                                                <Clock class="h-4 w-4 text-slate-400" />
                                                <span>{{ service.duration_minutes }} min</span>
                                            </div>
                                            <div class="inline-flex items-center gap-2 text-emerald-400 font-semibold">
                                                <Euro class="h-4 w-4" />
                                                <span>{{ service.price }} {{ service.currency }}</span>
                                            </div>
                                            <span
                                                class="text-[11px] px-2 py-0.5 rounded-full border"
                                                :class="
                                                    service.is_featured
                                                        ? 'border-amber-400/50 bg-amber-500/10 text-amber-100'
                                                        : 'border-slate-700 bg-slate-800 text-slate-300'
                                                "
                                            >
                                                {{ service.is_featured ? 'Offre mise en avant' : 'Offre standard' }}
                                            </span>
                                            <span
                                                class="text-[11px] px-2 py-0.5 rounded-full border"
                                                :class="
                                                    service.booking_enabled
                                                        ? 'border-emerald-400/50 bg-emerald-500/10 text-emerald-100'
                                                        : 'border-slate-700 bg-slate-800 text-slate-300'
                                                "
                                            >
                                                {{ service.booking_enabled ? 'Payable en ligne' : 'Contact uniquement' }}
                                            </span>
                                        </div>
                                        <div
                                            v-if="service.description"
                                            class="prose prose-invert max-w-none text-xs text-slate-300 max-h-24 overflow-hidden [&_*]:text-inherit [&_*]:text-xs [&_*]:leading-relaxed [&_ul]:list-disc [&_ul]:pl-4 [&_li]:my-0.5"
                                            v-html="service.description"
                                        />
                                        <p class="text-[11px] text-slate-500">
                                            Réservable minimum {{ service.min_advance_booking_hours }}h avant ·
                                            jusqu’à {{ service.max_advance_booking_days }} jours d’avance
                                        </p>
                                        <div class="mt-auto flex gap-2 pt-2 text-[11px]">
                                            <button
                                                type="button"
                                                class="flex-1 rounded-full border border-slate-700 px-4 py-1.5 text-slate-200 hover:bg-slate-800"
                                                @click="openEditModal(service)"
                                            >
                                                Modifier
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded-full border border-rose-500/50 bg-rose-500/10 px-4 py-1.5 text-rose-200 hover:bg-rose-500/20"
                                                @click="deleteService(service)"
                                            >
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </VueDraggable>

                    <div
                        v-else
                        class="rounded-2xl border border-slate-800 bg-slate-900/80 p-10 text-center text-slate-100 shadow-xl"
                    >
                        <div class="flex justify-center mb-4">
                            <div
                                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center shadow-lg"
                            >
                                <Activity class="h-7 w-7 text-white" />
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Aucun service</h3>
                        <p class="text-xs text-slate-400 mb-4">
                            Créez vos premières séances pour les proposer à vos clients.
                        </p>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
                            @click="openCreateModal"
                        >
                            <Plus class="h-3.5 w-3.5" />
                            <span>Créer un service</span>
                        </button>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div
        v-if="showModal"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
    >
        <div
            class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
        >
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                        {{ editingService ? 'Modifier' : 'Nouveau service' }}
                    </p>
                    <h2 class="text-sm font-semibold">
                        {{ editingService ? 'Modifier le service' : 'Créer un service' }}
                    </h2>
                </div>
                <button
                    type="button"
                    class="text-slate-400 hover:text-slate-200 text-sm"
                    @click="closeModal"
                >
                    ✕
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel
                        for="service_name"
                        value="Nom du service *"
                        class="text-xs text-slate-200"
                    />
                    <TextInput
                        id="service_name"
                        name="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError class="mt-1 text-[11px]" :message="form.errors.name" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel
                            for="service_duration"
                            value="Durée (minutes) *"
                            class="text-xs text-slate-200"
                        />
                        <TextInput
                            id="service_duration"
                            name="duration_minutes"
                            v-model="form.duration_minutes"
                            type="number"
                            min="15"
                            step="15"
                            class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                            required
                        />
                        <InputError class="mt-1 text-[11px]" :message="form.errors.duration_minutes" />
                    </div>
                    <div>
                        <InputLabel
                            for="service_price"
                            value="Prix"
                            class="text-xs text-slate-200"
                        />
                        <div class="mt-1 flex gap-2">
                            <TextInput
                                id="service_price"
                                name="price"
                                v-model="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="flex-1 bg-slate-950 border-slate-700 text-slate-50"
                            />
                            <select
                                v-model="form.currency"
                                name="currency"
                                class="rounded-md border border-slate-700 bg-slate-950 px-3 text-xs text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                <option value="EUR">EUR</option>
                                <option value="USD">USD</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                        <InputError class="mt-1 text-[11px]" :message="form.errors.price" />
                    </div>
                </div>

                <div>
                    <InputLabel
                        for="service_description"
                        value="Description"
                        class="text-xs text-slate-200"
                    />
                    <div
                        class="mt-1 rounded-xl border border-slate-800 bg-slate-950"
                        id="service_description"
                    >
                        <div
                            class="flex items-center justify-between border-b border-slate-800 px-3 py-2 text-[11px] text-slate-300"
                        >
                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border text-[11px] font-semibold transition disabled:opacity-40"
                                    :class="[
                                        editor && editor.isActive('bold')
                                            ? 'border-emerald-400 bg-emerald-500/10 text-emerald-200'
                                            : 'border-slate-700 bg-slate-900 text-slate-300 hover:text-slate-100',
                                    ]"
                                    @click="editor && editor.chain().focus().toggleBold().run()"
                                    :disabled="!editor"
                                >
                                    B
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border text-[11px] italic transition disabled:opacity-40"
                                    :class="[
                                        editor && editor.isActive('italic')
                                            ? 'border-emerald-400 bg-emerald-500/10 text-emerald-200'
                                            : 'border-slate-700 bg-slate-900 text-slate-300 hover:text-slate-100',
                                    ]"
                                    @click="editor && editor.chain().focus().toggleItalic().run()"
                                    :disabled="!editor"
                                >
                                    I
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border text-[11px] transition disabled:opacity-40"
                                    :class="[
                                        editor && editor.isActive('bulletList')
                                            ? 'border-emerald-400 bg-emerald-500/10 text-emerald-200'
                                            : 'border-slate-700 bg-slate-900 text-slate-300 hover:text-slate-100',
                                    ]"
                                    @click="editor && editor.chain().focus().toggleBulletList().run()"
                                    :disabled="!editor"
                                >
                                    ••
                                </button>
                            </div>
                            <span class="text-[10px] text-slate-500">Texte enrichi</span>
                        </div>
                        <EditorContent
                            :editor="editor"
                            class="tiptap-content min-h-[150px] px-3 py-3 text-xs leading-relaxed text-slate-100 [&_ul]:list-disc [&_ul]:pl-4 [&_li]:text-slate-200 [&_li]:leading-relaxed [&_li]:my-1"
                        />
                    </div>
                    <InputError class="mt-1 text-[11px]" :message="form.errors.description" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel
                            for="service_min_advance"
                            value="Délai minimum (heures)"
                            class="text-xs text-slate-200"
                        />
                        <TextInput
                            id="service_min_advance"
                            name="min_advance_booking_hours"
                            v-model="form.min_advance_booking_hours"
                            type="number"
                            min="0"
                            class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                        />
                        <InputError class="mt-1 text-[11px]" :message="form.errors.min_advance_booking_hours" />
                    </div>
                    <div>
                        <InputLabel
                            for="service_max_advance"
                            value="Réservable jusqu’à (jours)"
                            class="text-xs text-slate-200"
                        />
                        <TextInput
                            id="service_max_advance"
                            name="max_advance_booking_days"
                            v-model="form.max_advance_booking_days"
                            type="number"
                            min="1"
                            class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                        />
                        <InputError class="mt-1 text-[11px]" :message="form.errors.max_advance_booking_days" />
                    </div>
                </div>

                <div class="space-y-2">
                    <InputLabel
                        for="service_image"
                        value="Photo de présentation"
                        class="text-xs text-slate-200"
                    />
                    <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-950/70 p-4 flex flex-col gap-4">
                        <div
                            class="relative aspect-video w-full overflow-hidden rounded-xl border border-slate-800 bg-slate-900"
                        >
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Prévisualisation du service"
                                class="h-full w-full object-cover"
                            />
                            <div
                                v-else
                                class="h-full w-full flex flex-col items-center justify-center text-slate-500 text-xs gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75A2.25 2.25 0 016.75 4.5h10.5A2.25 2.25 0 0119.5 6.75v10.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 17.25V6.75z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15l4.5-4.5 3 3L15 10.5l4.5 4.5" />
                                </svg>
                                <span>Ajoutez une image 1280×720 pour un rendu optimal</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-slate-200 hover:bg-slate-800">
                                <input
                                    ref="imageInput"
                                    id="service_image"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageChange"
                                />
                                <span>Choisir une image</span>
                            </label>
                            <button
                                v-if="imagePreview"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-rose-100 hover:bg-rose-500/20"
                                @click="removeImage"
                            >
                                Retirer
                            </button>
                        </div>
                        <InputError class="text-[11px]" :message="form.errors.image" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <label class="flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-slate-200">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500"
                        />
                        Service actif<br /><span class="text-[11px] text-slate-500">Visible sur le site</span>
                    </label>
                    <label class="flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-slate-200">
                        <input
                            v-model="form.is_featured"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-amber-500 focus:ring-amber-500"
                        />
                        Offre mise en avant<br /><span class="text-[11px] text-slate-500">Badge et focus visuel</span>
                    </label>
                    <label class="flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-slate-200">
                        <input
                            v-model="form.booking_enabled"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500"
                        />
                        Payable en ligne<br /><span class="text-[11px] text-slate-500">Affiche “Payer en ligne”</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 text-xs">
                    <button
                        type="button"
                        class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                        @click="closeModal"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1.5 font-medium text-slate-50 hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
