<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import { toast } from 'vue-sonner';
import Modal from '@/Components/Modal.vue';
import {
    Calendar,
    Plus,
    Edit,
    Trash2,
    Clock,
    Check,
} from 'lucide-vue-next';

const props = defineProps({
    slots: Array,
});

const showModal = ref(false);
const editingSlot = ref(null);

const form = useForm({
    day_of_week: 1,
    start_time: '09:00',
    end_time: '17:00',
    is_active: true,
});

const daysOfWeek = [
    { value: 1, label: 'Lundi' },
    { value: 2, label: 'Mardi' },
    { value: 3, label: 'Mercredi' },
    { value: 4, label: 'Jeudi' },
    { value: 5, label: 'Vendredi' },
    { value: 6, label: 'Samedi' },
    { value: 0, label: 'Dimanche' },
];

const groupedSlots = computed(() => {
    const grouped = {};
    daysOfWeek.forEach(day => {
        grouped[day.value] = props.slots?.filter(slot => slot.day_of_week === day.value) || [];
    });
    return grouped;
});

const dashboardBackUrl = computed(() => {
    if (typeof window === 'undefined') return route('dashboard');
    const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
    return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
    router.visit(dashboardBackUrl.value);
};

const openCreateModal = () => {
    editingSlot.value = null;
    form.reset();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (slot) => {
    editingSlot.value = slot;
    form.day_of_week = slot.day_of_week;
    form.start_time = slot.start_time;
    form.end_time = slot.end_time;
    form.is_active = slot.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingSlot.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingSlot.value) {
        form.patch(route('dashboard.availability.update', editingSlot.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success('Créneau mis à jour');
            },
            onError: () => {
                toast.error('Erreur lors de la mise à jour');
            },
        });
    } else {
        form.post(route('dashboard.availability.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success('Créneau créé');
            },
            onError: () => {
                toast.error('Erreur lors de la création');
            },
        });
    }
};

const deleteSlot = (slot) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) {
        router.delete(route('dashboard.availability.destroy', slot.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Créneau supprimé');
            },
            onError: () => {
                toast.error('Erreur lors de la suppression');
            },
        });
    }
};

const getDayLabel = (dayValue) => {
    return daysOfWeek.find(d => d.value === dayValue)?.label || '';
};
</script>

<template>
    <Head title="Mes Disponibilités" />

    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <button
                        type="button"
                        @click="goBack"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 transition-colors"
                    >
                        <span class="text-lg">&larr;</span>
                    </button>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                            <Calendar class="h-5 w-5 text-blue-300" />
                            Mes Disponibilités
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            Définissez vos créneaux de disponibilité hebdomadaires
                        </p>
                    </div>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Ajouter un créneau
                </button>
            </div>

            <!-- Calendar view -->
            <div class="space-y-4" v-auto-animate>
                <div
                    v-for="day in daysOfWeek"
                    :key="day.value"
                    class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-slate-50">{{ day.label }}</h3>
                        <span class="text-xs text-slate-400">
                            {{ groupedSlots[day.value]?.length || 0 }} créneau(x)
                        </span>
                    </div>

                    <div v-if="groupedSlots[day.value]?.length > 0" class="space-y-3" v-auto-animate>
                        <div
                            v-for="slot in groupedSlots[day.value]"
                            :key="slot.id"
                            class="flex items-center justify-between p-4 rounded-lg border border-slate-700 bg-slate-800/50"
                        >
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-400 flex items-center justify-center">
                                    <Clock class="h-4 w-4 text-white" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-200">
                                        {{ slot.start_time }} - {{ slot.end_time }}
                                    </p>
                                    <span
                                        v-if="slot.is_active"
                                        class="inline-flex items-center gap-1 mt-1 text-xs text-emerald-400"
                                    >
                                        <Check class="h-3 w-3" />
                                        Actif
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1 mt-1 text-xs text-slate-400">
                                        Inactif
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    @click="openEditModal(slot)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-600 transition-colors"
                                >
                                    <Edit class="h-3 w-3" />
                                    Modifier
                                </button>
                                <button
                                    @click="deleteSlot(slot)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-rose-950/40 px-3 py-1.5 text-xs font-medium text-rose-200 border border-rose-500/40 hover:bg-rose-950/60 transition-colors"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-6">
                        <p class="text-sm text-slate-500">Aucun créneau défini pour ce jour</p>
                    </div>
                </div>
            </div>

            <!-- Empty state (if no slots at all) -->
            <div v-if="!slots || slots.length === 0" class="rounded-2xl border border-slate-800 bg-slate-900/70 p-12 shadow-xl text-center">
                <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-600 flex items-center justify-center mb-4">
                    <Calendar class="h-8 w-8 text-slate-300" />
                </div>
                <h3 class="text-lg font-semibold text-slate-50 mb-2">Aucune disponibilité</h3>
                <p class="text-sm text-slate-400 mb-6 max-w-md mx-auto">
                    Définissez vos créneaux de disponibilité pour permettre à vos clients de réserver en ligne.
                </p>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Définir mes disponibilités
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
        <div class="p-6 bg-slate-900">
            <h2 class="text-xl font-bold text-slate-50 mb-6">
                {{ editingSlot ? 'Modifier le créneau' : 'Ajouter un créneau' }}
            </h2>

            <form @submit.prevent="submitForm" class="space-y-5">
                <div>
                    <label for="day_of_week" class="block text-sm font-medium text-slate-300 mb-2">
                        Jour de la semaine *
                    </label>
                    <select
                        id="day_of_week"
                        v-model.number="form.day_of_week"
                        required
                        class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                    >
                        <option v-for="day in daysOfWeek" :key="day.value" :value="day.value">
                            {{ day.label }}
                        </option>
                    </select>
                    <p v-if="form.errors.day_of_week" class="mt-1 text-xs text-rose-400">{{ form.errors.day_of_week }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-slate-300 mb-2">
                            Heure de début *
                        </label>
                        <input
                            id="start_time"
                            v-model="form.start_time"
                            type="time"
                            required
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                        />
                        <p v-if="form.errors.start_time" class="mt-1 text-xs text-rose-400">{{ form.errors.start_time }}</p>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-slate-300 mb-2">
                            Heure de fin *
                        </label>
                        <input
                            id="end_time"
                            v-model="form.end_time"
                            type="time"
                            required
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                        />
                        <p v-if="form.errors.end_time" class="mt-1 text-xs text-rose-400">{{ form.errors.end_time }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input
                        id="is_active"
                        v-model="form.is_active"
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-blue-600 focus:ring-2 focus:ring-blue-500/20"
                    />
                    <label for="is_active" class="text-sm text-slate-300">
                        Créneau actif
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button
                        type="button"
                        @click="closeModal"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-slate-200 border border-slate-700 hover:bg-slate-700 transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition-colors"
                    >
                        {{ editingSlot ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
