<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Modal from '@/Components/Modal.vue';
import {
    CreditCard,
    Plus,
    Edit,
    Trash2,
    ArrowLeft,
    Clock,
    Euro,
    GripVertical,
    Check,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    services: Array,
});

const showModal = ref(false);
const editingService = ref(null);

const form = useForm({
    name: '',
    description: '',
    duration_minutes: 60,
    price: '',
    is_active: true,
});

const openCreateModal = () => {
    editingService.value = null;
    form.reset();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (service) => {
    editingService.value = service;
    form.name = service.name;
    form.description = service.description || '';
    form.duration_minutes = service.duration_minutes;
    form.price = service.price;
    form.is_active = service.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingService.value = null;
    form.reset();
};

const submitForm = () => {
    console.log('Form submit:', form.data());
    console.log('Editing:', editingService.value);
    
    if (editingService.value) {
        form.patch(route('dashboard.services.update', editingService.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success('Service mis à jour avec succès');
            },
            onError: (errors) => {
                console.error('Erreur mise à jour:', errors);
                toast.error('Erreur lors de la mise à jour');
            },
        });
    } else {
        form.post(route('dashboard.services.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success('Service créé avec succès');
            },
            onError: (errors) => {
                console.error('Erreur création:', errors);
                toast.error('Erreur lors de la création');
            },
        });
    }
};

const deleteService = (service) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${service.name}" ?`)) {
        router.delete(route('dashboard.services.destroy', service.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Service supprimé');
            },
            onError: () => {
                toast.error('Erreur lors de la suppression');
            },
        });
    }
};
</script>

<template>
    <Head title="Mes Services" />

    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 transition-colors"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                            <CreditCard class="h-5 w-5 text-emerald-300" />
                            Mes Services
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            Gérez vos types de séances et vos tarifs
                        </p>
                    </div>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-emerald-700 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Créer un service
                </button>
            </div>

            <!-- Services list -->
            <div v-if="services && services.length > 0" class="space-y-4">
                <div
                    v-for="service in services"
                    :key="service.id"
                    class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl"
                >
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center shadow-lg flex-shrink-0">
                                    <CreditCard class="h-5 w-5 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-lg font-semibold text-slate-50">{{ service.name }}</h3>
                                        <span
                                            v-if="service.is_active"
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs font-medium text-emerald-100 border border-emerald-500/40"
                                        >
                                            <Check class="h-3 w-3" />
                                            Actif
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1 rounded-full bg-slate-700/40 px-2 py-0.5 text-xs font-medium text-slate-300 border border-slate-600/40"
                                        >
                                            <X class="h-3 w-3" />
                                            Inactif
                                        </span>
                                    </div>
                                    <p v-if="service.description" class="text-sm text-slate-400 mb-3">
                                        {{ service.description }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-4 text-sm">
                                        <div class="flex items-center gap-2 text-slate-300">
                                            <Clock class="h-4 w-4 text-slate-400" />
                                            <span>{{ service.duration_minutes }} minutes</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-emerald-400 font-semibold">
                                            <Euro class="h-4 w-4" />
                                            <span>{{ service.price }}€</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="openEditModal(service)"
                                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-3 py-2 text-xs font-medium text-slate-200 border border-slate-700 hover:bg-slate-700 transition-colors"
                            >
                                <Edit class="h-3.5 w-3.5" />
                                Modifier
                            </button>
                            <button
                                @click="deleteService(service)"
                                class="inline-flex items-center gap-2 rounded-lg bg-rose-950/40 px-3 py-2 text-xs font-medium text-rose-200 border border-rose-500/40 hover:bg-rose-950/60 transition-colors"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="rounded-2xl border border-slate-800 bg-slate-900/70 p-12 shadow-xl text-center">
                <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-600 flex items-center justify-center mb-4">
                    <CreditCard class="h-8 w-8 text-slate-300" />
                </div>
                <h3 class="text-lg font-semibold text-slate-50 mb-2">Aucun service</h3>
                <p class="text-sm text-slate-400 mb-6 max-w-md mx-auto">
                    Créez votre premier service pour commencer à recevoir des réservations en ligne.
                </p>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-emerald-700 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Créer mon premier service
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="2xl">
        <div class="p-6 bg-slate-900">
            <h2 class="text-xl font-bold text-slate-50 mb-6">
                {{ editingService ? 'Modifier le service' : 'Créer un service' }}
            </h2>

            <form @submit.prevent="submitForm" class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                        Nom du service *
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-colors"
                        placeholder="Ex: Séance découverte"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-2">
                        Description
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-colors"
                        placeholder="Décrivez votre service..."
                    />
                    <p v-if="form.errors.description" class="mt-1 text-xs text-rose-400">{{ form.errors.description }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="duration" class="block text-sm font-medium text-slate-300 mb-2">
                            Durée (minutes) *
                        </label>
                        <input
                            id="duration"
                            v-model.number="form.duration_minutes"
                            type="number"
                            min="15"
                            step="15"
                            required
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-colors"
                        />
                        <p v-if="form.errors.duration_minutes" class="mt-1 text-xs text-rose-400">{{ form.errors.duration_minutes }}</p>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-300 mb-2">
                            Prix (€) *
                        </label>
                        <input
                            id="price"
                            v-model.number="form.price"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-colors"
                        />
                        <p v-if="form.errors.price" class="mt-1 text-xs text-rose-400">{{ form.errors.price }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input
                        id="is_active"
                        v-model="form.is_active"
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-emerald-600 focus:ring-2 focus:ring-emerald-500/20"
                    />
                    <label for="is_active" class="text-sm text-slate-300">
                        Service actif (visible pour les réservations)
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
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50 transition-colors"
                    >
                        {{ editingService ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
