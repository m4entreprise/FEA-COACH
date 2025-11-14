<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    requests: Array,
});

const selectedRequest = ref(null);
const showModal = ref(false);
const action = ref('');

const form = useForm({
    admin_notes: '',
});

const openApproveModal = (request) => {
    selectedRequest.value = request;
    action.value = 'approve';
    form.admin_notes = '';
    showModal.value = true;
};

const openRejectModal = (request) => {
    selectedRequest.value = request;
    action.value = 'reject';
    form.admin_notes = '';
    showModal.value = true;
};

const submitAction = () => {
    const route = action.value === 'approve' 
        ? `admin.promo-requests.approve` 
        : `admin.promo-requests.reject`;
    
    form.post(window.route(route, selectedRequest.value.id), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        }
    });
};

const getStatusColor = (status) => {
    return {
        'pending': 'bg-yellow-500/20 text-yellow-400 border-yellow-400/30',
        'approved': 'bg-green-500/20 text-green-400 border-green-400/30',
        'rejected': 'bg-red-500/20 text-red-400 border-red-400/30',
    }[status] || 'bg-gray-500/20 text-gray-400 border-gray-400/30';
};

const getStatusLabel = (status) => {
    return {
        'pending': 'En attente',
        'approved': 'Approuvé',
        'rejected': 'Rejeté',
    }[status] || status;
};
</script>

<template>
    <Head title="Demandes de code promo" />
    
    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Demandes de code promo FEA
                            </h2>
                            <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-medium">
                                {{ requests.length }} demandes
                            </span>
                        </div>

                        <!-- Empty State -->
                        <div v-if="requests.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune demande</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Les demandes de code promo apparaîtront ici.
                            </p>
                        </div>

                        <!-- Requests List -->
                        <div v-else class="space-y-4">
                            <div 
                                v-for="request in requests" 
                                :key="request.id"
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ request.first_name }} {{ request.last_name }}
                                            </h3>
                                            <span 
                                                class="px-2 py-1 rounded-full text-xs font-medium border"
                                                :class="getStatusColor(request.status)"
                                            >
                                                {{ getStatusLabel(request.status) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                            📧 {{ request.email }}
                                        </p>
                                        
                                        <p v-if="request.message" class="text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded mt-3">
                                            <span class="font-medium">Message :</span> {{ request.message }}
                                        </p>
                                        
                                        <div v-if="request.promo_code" class="mt-3 p-3 bg-green-500/10 border border-green-400/30 rounded">
                                            <p class="text-sm text-green-700 dark:text-green-300">
                                                <span class="font-medium">Code promo généré :</span>
                                                <span class="font-mono ml-2">{{ request.promo_code }}</span>
                                            </p>
                                        </div>
                                        
                                        <div v-if="request.admin_notes" class="mt-3 p-3 bg-gray-100 dark:bg-gray-600 rounded">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-medium">Notes admin :</span> {{ request.admin_notes }}
                                            </p>
                                        </div>
                                        
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                                            Demandé le {{ new Date(request.created_at).toLocaleDateString('fr-FR', { 
                                                year: 'numeric', 
                                                month: 'long', 
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) }}
                                        </p>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div v-if="request.status === 'pending'" class="flex flex-col gap-2 ml-4">
                                        <button
                                            @click="openApproveModal(request)"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition"
                                        >
                                            ✓ Approuver
                                        </button>
                                        <button
                                            @click="openRejectModal(request)"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition"
                                        >
                                            ✗ Rejeter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModal = false">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ action === 'approve' ? 'Approuver la demande' : 'Rejeter la demande' }}
                    </h3>
                    
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        {{ selectedRequest?.first_name }} {{ selectedRequest?.last_name }} ({{ selectedRequest?.email }})
                    </p>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes administratives {{ action === 'reject' ? '(requis)' : '(optionnel)' }}
                        </label>
                        <textarea
                            v-model="form.admin_notes"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                            :placeholder="action === 'approve' ? 'Notes optionnelles...' : 'Raison du rejet...'"
                        ></textarea>
                    </div>
                    
                    <div class="flex gap-3">
                        <button
                            @click="showModal = false"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                        >
                            Annuler
                        </button>
                        <button
                            @click="submitAction"
                            :disabled="form.processing || (action === 'reject' && !form.admin_notes)"
                            :class="action === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                            class="flex-1 px-4 py-2 text-white rounded-lg transition disabled:opacity-50"
                        >
                            {{ form.processing ? 'Traitement...' : (action === 'approve' ? 'Approuver' : 'Rejeter') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
