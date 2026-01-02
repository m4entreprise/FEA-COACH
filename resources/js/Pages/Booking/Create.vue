<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { CalendarIcon, ClockIcon, UserIcon, PhoneIcon, EnvelopeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    coach: Object,
    service: Object,
    selectedDate: String,
    selectedTime: String,
    stripePublicKey: String,
});

const currentStep = ref(1);
const availableSlots = ref([]);
const loadingSlots = ref(false);
const selectedDate = ref(props.selectedDate || '');
const selectedSlot = ref(props.selectedTime || '');

const form = useForm({
    booking_date: props.selectedDate || '',
    start_time: props.selectedTime || '',
    client_first_name: '',
    client_last_name: '',
    client_email: '',
    client_phone: '',
    client_notes: '',
});

const minDate = computed(() => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
});

const maxDate = computed(() => {
    const maxDays = props.service.max_advance_booking_days || 60;
    const maxDate = new Date();
    maxDate.setDate(maxDate.getDate() + maxDays);
    return maxDate.toISOString().split('T')[0];
});

const fetchAvailableSlots = async () => {
    if (!selectedDate.value) return;
    
    loadingSlots.value = true;
    try {
        const response = await fetch(
            `/reserver/${props.service.id}/creneaux?date=${selectedDate.value}`
        );
        const data = await response.json();
        availableSlots.value = data.slots || [];
    } catch (error) {
        console.error('Erreur lors du chargement des créneaux:', error);
        availableSlots.value = [];
    } finally {
        loadingSlots.value = false;
    }
};

const selectDate = (date) => {
    selectedDate.value = date;
    selectedSlot.value = '';
    form.booking_date = date;
    fetchAvailableSlots();
};

const selectSlot = (slot) => {
    selectedSlot.value = slot.time;
    form.start_time = slot.time;
};

const goToStep = (step) => {
    if (step === 2 && (!selectedDate.value || !selectedSlot.value)) {
        alert('Veuillez sélectionner une date et un créneau');
        return;
    }
    currentStep.value = step;
};

const submitBooking = async () => {
    if (form.processing) return;

    form.post(route('coach.booking.store', props.service.id), {
        onSuccess: (response) => {
            // Redirection vers Stripe Checkout sera gérée par le backend
            if (response.props.checkout_url) {
                window.location.href = response.props.checkout_url;
            }
        },
        onError: (errors) => {
            console.error('Erreur:', errors);
            alert('Une erreur est survenue. Veuillez réessayer.');
        },
    });
};

onMounted(() => {
    if (props.selectedDate) {
        fetchAvailableSlots();
    }
});
</script>

<template>
    <Head :title="`Réserver ${service.name}`" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="/reserver" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Retour aux services
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-2">
                    {{ service.name }}
                </h1>
                <p class="text-gray-600">avec {{ coach.name }}</p>
            </div>

            <!-- Progress steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div :class="['flex items-center', currentStep >= 1 ? 'text-blue-600' : 'text-gray-400']">
                        <div :class="['rounded-full h-10 w-10 flex items-center justify-center border-2', currentStep >= 1 ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300']">
                            1
                        </div>
                        <span class="ml-2 font-medium hidden sm:inline">Date & Heure</span>
                    </div>
                    <div class="w-16 border-t-2" :class="currentStep >= 2 ? 'border-blue-600' : 'border-gray-300'"></div>
                    <div :class="['flex items-center', currentStep >= 2 ? 'text-blue-600' : 'text-gray-400']">
                        <div :class="['rounded-full h-10 w-10 flex items-center justify-center border-2', currentStep >= 2 ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300']">
                            2
                        </div>
                        <span class="ml-2 font-medium hidden sm:inline">Vos informations</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Step 1: Date & Time Selection -->
                <div v-show="currentStep === 1" class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Choisissez une date et un créneau
                    </h2>

                    <!-- Date Selection -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📅 Sélectionnez une date
                        </label>
                        <input
                            type="date"
                            v-model="selectedDate"
                            @change="selectDate(selectedDate)"
                            :min="minDate"
                            :max="maxDate"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <!-- Available Slots -->
                    <div v-if="selectedDate">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ⏰ Créneaux disponibles
                        </label>
                        
                        <div v-if="loadingSlots" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="mt-2 text-gray-600">Chargement des créneaux...</p>
                        </div>

                        <div v-else-if="availableSlots.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                            <p class="text-gray-600">Aucun créneau disponible pour cette date.</p>
                            <p class="text-sm text-gray-500 mt-2">Essayez une autre date.</p>
                        </div>

                        <div v-else class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                            <button
                                v-for="slot in availableSlots"
                                :key="slot.time"
                                @click="selectSlot(slot)"
                                :class="[
                                    'px-4 py-3 rounded-lg border-2 font-medium transition',
                                    selectedSlot === slot.time
                                        ? 'border-blue-600 bg-blue-600 text-white'
                                        : 'border-gray-300 text-gray-700 hover:border-blue-400 hover:bg-blue-50'
                                ]"
                            >
                                {{ slot.time }}
                            </button>
                        </div>
                    </div>

                    <!-- Service Info -->
                    <div class="mt-8 bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Durée de la séance</p>
                                <p class="font-semibold text-gray-900">{{ service.duration_minutes }} minutes</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Montant</p>
                                <p class="text-2xl font-bold text-blue-600">{{ service.price }}€</p>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="goToStep(2)"
                        :disabled="!selectedDate || !selectedSlot"
                        class="w-full mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Continuer
                    </button>
                </div>

                <!-- Step 2: Client Information -->
                <div v-show="currentStep === 2" class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Vos informations
                    </h2>

                    <!-- Booking Summary -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center">
                                <CalendarIcon class="h-5 w-5 text-blue-600 mr-1" />
                                <span class="text-gray-700">{{ new Date(selectedDate).toLocaleDateString('fr-FR') }}</span>
                            </div>
                            <div class="flex items-center">
                                <ClockIcon class="h-5 w-5 text-blue-600 mr-1" />
                                <span class="text-gray-700">{{ selectedSlot }}</span>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitBooking" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Prénom *
                                </label>
                                <div class="relative">
                                    <UserIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <input
                                        v-model="form.client_first_name"
                                        type="text"
                                        required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Jean"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom *
                                </label>
                                <div class="relative">
                                    <UserIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <input
                                        v-model="form.client_last_name"
                                        type="text"
                                        required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Dupont"
                                    />
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email *
                            </label>
                            <div class="relative">
                                <EnvelopeIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="form.client_email"
                                    type="email"
                                    required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="jean.dupont@example.com"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone *
                            </label>
                            <div class="relative">
                                <PhoneIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="form.client_phone"
                                    type="tel"
                                    required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="06 12 34 56 78"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Message (optionnel)
                            </label>
                            <textarea
                                v-model="form.client_notes"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Indiquez toute information utile pour votre coach..."
                            ></textarea>
                        </div>

                        <div class="border-t pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-gray-900">Total à payer</span>
                                <span class="text-3xl font-bold text-blue-600">{{ service.price }}€</span>
                            </div>

                            <div class="flex space-x-4">
                                <button
                                    type="button"
                                    @click="currentStep = 1"
                                    class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
                                >
                                    Retour
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Traitement...' : 'Payer et confirmer' }}
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 text-center mt-4">
                                🔒 Paiement sécurisé par Stripe
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
