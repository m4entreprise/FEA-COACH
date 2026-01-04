<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { User, TrendingUp, MessageSquare, FileText, ArrowLeft, Send, Paperclip, Download, Plus, Edit2, Trash2, Upload, AlertTriangle } from 'lucide-vue-next';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  client: Object,
  documentTypes: Object,
});

const activeTab = ref('profile');

// Watch activeTab to mark messages as read when opening messages tab
const markMessagesAsRead = () => {
  if (activeTab.value === 'messages' && unreadClientMessages.value > 0) {
    router.post(route('dashboard.clients.messages.markRead', props.client.id), {}, {
      preserveScroll: true,
      only: ['client'],
    });
  }
};

// Watch for tab changes
const previousTab = ref('profile');
const onTabChange = (newTab) => {
  activeTab.value = newTab;
  if (newTab === 'messages' && previousTab.value !== 'messages') {
    markMessagesAsRead();
  }
  previousTab.value = newTab;
};
const tabs = [
  { id: 'profile', label: 'Profil & Données', icon: User },
  { id: 'analytics', label: 'Évolution', icon: TrendingUp },
  { id: 'messages', label: 'Messagerie', icon: MessageSquare },
  { id: 'documents', label: 'Documents', icon: FileText },
];

const getInitials = (firstName, lastName) => {
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
};

const getAvatarColor = () => {
  const colors = [
    'from-purple-500 to-purple-600',
    'from-blue-500 to-blue-600',
    'from-green-500 to-green-600',
    'from-yellow-500 to-yellow-600',
  ];
  return colors[0];
};

const latestMeasurement = computed(() => {
  if (!props.client.measurements || props.client.measurements.length === 0) return null;
  return props.client.measurements[0];
});

const calculatedBmi = computed(() => {
  if (!latestMeasurement.value?.weight || !latestMeasurement.value?.height) return null;
  const weight = parseFloat(latestMeasurement.value.weight);
  const height = parseFloat(latestMeasurement.value.height);
  if (weight <= 0 || height <= 0) return null;
  const heightInMeters = height / 100;
  return (weight / (heightInMeters * heightInMeters)).toFixed(1);
});

// Helper function to calculate BMI for any measurement
const calculateBmi = (measurement) => {
  if (!measurement?.weight || !measurement?.height) return null;
  const weight = parseFloat(measurement.weight);
  const height = parseFloat(measurement.height);
  if (weight <= 0 || height <= 0) return null;
  const heightInMeters = height / 100;
  return (weight / (heightInMeters * heightInMeters)).toFixed(1);
};

const shareUrl = computed(() => {
  return `${window.location.origin}/p/${props.client.share_token}`;
});

const copyShareLink = () => {
  navigator.clipboard.writeText(shareUrl.value);
  alert('Lien copié dans le presse-papier !');
};

const viewClientDashboard = () => {
  router.post(route('dashboard.clients.accessDashboard', props.client.id), {}, {
    onSuccess: (page) => {
      window.open(shareUrl.value, '_blank');
    },
  });
};

const unreadClientMessages = computed(() => {
  return props.client.messages?.filter(m => m.sender_type === 'client' && !m.is_read).length || 0;
});

const goBack = () => {
  router.visit(route('dashboard.clients.index'));
};

// Messaging
const messageForm = useForm({
  message: '',
  attachment: null,
});

const attachmentInput = ref(null);
const selectedFile = ref(null);

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
    messageForm.attachment = file;
  }
};

const removeFile = () => {
  selectedFile.value = null;
  messageForm.attachment = null;
  if (attachmentInput.value) {
    attachmentInput.value.value = '';
  }
};

const sendMessage = () => {
  messageForm.post(route('dashboard.clients.messages.send', props.client.id), {
    preserveScroll: true,
    onSuccess: () => {
      messageForm.reset();
      removeFile();
    },
  });
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const formatMessageDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const hours = Math.floor(diff / (1000 * 60 * 60));
  
  if (hours < 24) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }
  return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

// Notes
const showNoteModal = ref(false);
const editingNote = ref(null);
const noteForm = useForm({
  content: '',
});

const openNoteModal = (note = null) => {
  editingNote.value = note;
  noteForm.content = note ? note.content : '';
  showNoteModal.value = true;
};

const closeNoteModal = () => {
  showNoteModal.value = false;
  editingNote.value = null;
  noteForm.reset();
};

const submitNote = () => {
  if (editingNote.value) {
    noteForm.patch(route('dashboard.clients.notes.update', editingNote.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeNoteModal();
        router.reload({ only: ['client'] });
      },
    });
  } else {
    noteForm.post(route('dashboard.clients.notes.store', props.client.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeNoteModal();
        router.reload({ only: ['client'] });
      },
    });
  }
};

const deleteNote = (note) => {
  openActionModal('delete-note', note);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const documentsByType = (type) => {
  return props.client.documents?.filter(doc => doc.type === type).sort((a, b) => b.version - a.version) || [];
};

// Measurements
const showMeasurementModal = ref(false);
const editingMeasurement = ref(null);
const measurementForm = useForm({
  weight: '',
  height: '',
  chest: '',
  waist: '',
  hips: '',
});

const openMeasurementModal = (measurement = null) => {
  editingMeasurement.value = measurement;
  if (measurement) {
    measurementForm.weight = measurement.weight || '';
    measurementForm.height = measurement.height || '';
    measurementForm.chest = measurement.chest || '';
    measurementForm.waist = measurement.waist || '';
    measurementForm.hips = measurement.hips || '';
  } else {
    measurementForm.reset();
  }
  showMeasurementModal.value = true;
};

const closeMeasurementModal = () => {
  showMeasurementModal.value = false;
  editingMeasurement.value = null;
  measurementForm.reset();
};

const submitMeasurement = () => {
  // Calculate BMI if weight and height are provided
  const weight = parseFloat(measurementForm.weight);
  const height = parseFloat(measurementForm.height);
  
  if (weight > 0 && height > 0) {
    const heightInMeters = height / 100;
    const bmi = weight / (heightInMeters * heightInMeters);
    measurementForm.bmi = Math.round(bmi * 10) / 10;
  }
  
  if (editingMeasurement.value) {
    measurementForm.patch(route('dashboard.clients.measurements.update', [props.client.id, editingMeasurement.value.id]), {
      preserveScroll: true,
      onSuccess: () => {
        closeMeasurementModal();
        router.reload({ only: ['client'] });
      },
    });
  } else {
    measurementForm.post(route('dashboard.clients.measurements.store', props.client.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeMeasurementModal();
        router.reload({ only: ['client'] });
      },
    });
  }
};

const deleteMeasurement = (measurement) => {
  openActionModal('delete-measurement', measurement);
};

// Photos
const showPhotoModal = ref(false);
const selectedMeasurement = ref(null);

const viewPhotos = (measurement) => {
  selectedMeasurement.value = measurement;
  showPhotoModal.value = true;
};

const closePhotoModal = () => {
  showPhotoModal.value = false;
  selectedMeasurement.value = null;
};

const getPhotoUrl = (measurement, type) => {
  if (!measurement) return null;
  const photoField = `photo_${type}`;
  if (!measurement[photoField]) return null;
  return route('clients.dashboard.photo', [props.client.share_token, measurement.id, type]);
};

// Documents
const showDocumentModal = ref(false);
const documentForm = useForm({
  type: '',
  title: '',
  description: '',
  document: null,
});
const documentFileInput = ref(null);
const selectedDocumentFile = ref(null);

const openDocumentModal = () => {
  documentForm.reset();
  selectedDocumentFile.value = null;
  showDocumentModal.value = true;
};

const closeDocumentModal = () => {
  showDocumentModal.value = false;
  documentForm.reset();
  selectedDocumentFile.value = null;
  if (documentFileInput.value) {
    documentFileInput.value.value = '';
  }
};

const handleDocumentFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedDocumentFile.value = file;
    documentForm.document = file;
  }
};

const submitDocument = () => {
  documentForm.post(route('dashboard.clients.documents.store', props.client.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeDocumentModal();
      router.reload({ only: ['client'] });
    },
  });
};

const deleteDocument = (document) => {
  openActionModal('delete-document', document);
};

const actionModal = ref({
  show: false,
  type: null,
  payload: null,
  loading: false,
});

const actionConfigs = {
  'delete-note': {
    title: 'Supprimer la note',
    description: 'Cette note sera définitivement supprimée du dossier client.',
    confirmLabel: 'Supprimer la note',
    iconClasses: 'bg-rose-500/15 text-rose-300 border border-rose-500/40',
    confirmClasses: 'bg-rose-600 hover:bg-rose-500 focus-visible:ring-rose-400',
    icon: Trash2,
  },
  'delete-measurement': {
    title: 'Supprimer le relevé',
    description: 'Toutes les données de ce relevé seront perdues.',
    confirmLabel: 'Supprimer le relevé',
    iconClasses: 'bg-amber-500/15 text-amber-300 border border-amber-500/40',
    confirmClasses: 'bg-amber-500 hover:bg-amber-400 text-slate-950 focus-visible:ring-amber-300',
    icon: AlertTriangle,
  },
  'delete-document': {
    title: 'Supprimer le document',
    description: 'Le document ne sera plus accessible pour vous ou votre client.',
    confirmLabel: 'Supprimer le document',
    iconClasses: 'bg-slate-500/15 text-slate-300 border border-slate-500/40',
    confirmClasses: 'bg-slate-700 hover:bg-slate-600 focus-visible:ring-slate-500',
    icon: Trash2,
  },
};

const currentActionConfig = computed(() => {
  if (!actionModal.value.type) return null;
  return actionConfigs[actionModal.value.type];
});

const openActionModal = (type, payload) => {
  actionModal.value = {
    show: true,
    type,
    payload,
    loading: false,
  };
};

const closeActionModal = () => {
  actionModal.value.show = false;
  actionModal.value.type = null;
  actionModal.value.payload = null;
  actionModal.value.loading = false;
};

const handleConfirmAction = () => {
  if (!actionModal.value.type || !actionModal.value.payload) return;

  actionModal.value.loading = true;

  const baseOptions = {
    preserveScroll: true,
    onSuccess: () => {
      actionModal.value.loading = false;
      router.reload({ only: ['client'] });
      closeActionModal();
    },
    onFinish: () => {
      actionModal.value.loading = false;
    },
  };

  if (actionModal.value.type === 'delete-note') {
    router.delete(route('dashboard.clients.notes.destroy', actionModal.value.payload.id), baseOptions);
  } else if (actionModal.value.type === 'delete-measurement') {
    router.delete(route('dashboard.clients.measurements.destroy', [props.client.id, actionModal.value.payload.id]), baseOptions);
  } else if (actionModal.value.type === 'delete-document') {
    router.delete(route('dashboard.clients.documents.destroy', actionModal.value.payload.id), baseOptions);
  }
};
</script>

<template>
  <Head :title="`Gérer ${client.first_name} ${client.last_name}`" />

  <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
          <button
            @click="goBack"
            class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-slate-200 mb-4 transition-colors"
          >
            <ArrowLeft class="h-4 w-4" />
            <span>Retour à la liste des clients</span>
          </button>

          <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-6 shadow-xl">
            <div class="flex items-center gap-4">
              <div
                :class="[
                  getAvatarColor(),
                  'flex h-16 w-16 items-center justify-center rounded-full text-lg font-bold text-white shadow-lg bg-gradient-to-br'
                ]"
              >
                {{ getInitials(client.first_name, client.last_name) }}
              </div>
              <div class="flex-1">
                <h1 class="text-2xl font-bold text-slate-50">
                  {{ client.first_name }} {{ client.last_name }}
                </h1>
                <p class="text-sm text-slate-400">{{ client.email || 'Pas d\'email' }}</p>
                <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 border border-indigo-500/30 rounded-full">
                  <span class="text-xs text-indigo-300">Code élève:</span>
                  <span class="text-sm font-bold text-indigo-100 tracking-wider">{{ client.share_code }}</span>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <button
                  @click="viewClientDashboard"
                  class="flex items-center gap-2 px-4 py-2 bg-blue-500/20 border border-blue-500/30 rounded-full text-sm font-semibold text-blue-300 hover:bg-blue-500/30 transition-colors"
                >
                  <User class="h-4 w-4" />
                  <span>Voir le dashboard élève</span>
                </button>
                <button
                  @click="copyShareLink"
                  class="flex items-center gap-2 px-4 py-2 bg-emerald-500/20 border border-emerald-500/30 rounded-full text-sm font-semibold text-emerald-300 hover:bg-emerald-500/30 transition-colors"
                >
                  <span>🔗</span>
                  <span>Partager le lien</span>
                </button>
                <div v-if="unreadClientMessages > 0" class="flex items-center gap-2 px-4 py-2 bg-indigo-500/20 border border-indigo-500/30 rounded-full">
                  <MessageSquare class="h-4 w-4 text-indigo-400" />
                  <span class="text-sm font-semibold text-indigo-300">{{ unreadClientMessages }} nouveau{{ unreadClientMessages > 1 ? 'x' : '' }} message{{ unreadClientMessages > 1 ? 's' : '' }}</span>
                </div>
              </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
              <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                <p class="text-xs uppercase text-slate-400 mb-1">Poids actuel</p>
                <p class="text-2xl font-bold text-slate-50">
                  {{ latestMeasurement?.weight ? latestMeasurement.weight + ' kg' : '—' }}
                </p>
              </div>
              <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                <p class="text-xs uppercase text-slate-400 mb-1">IMC</p>
                <p class="text-2xl font-bold text-slate-50">
                  {{ calculatedBmi || latestMeasurement?.bmi?.toFixed(1) || '—' }}
                </p>
              </div>
              <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                <p class="text-xs uppercase text-slate-400 mb-1">Relevés</p>
                <p class="text-2xl font-bold text-slate-50">
                  {{ client.measurements?.length || 0 }}
                </p>
              </div>
              <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                <p class="text-xs uppercase text-slate-400 mb-1">Messages</p>
                <p class="text-2xl font-bold text-slate-50">
                  {{ client.messages?.length || 0 }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
          <div class="border-b border-slate-800">
            <nav class="flex gap-2 p-2">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="onTabChange(tab.id)"
                :class="[
                  'flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all',
                  activeTab === tab.id
                    ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg'
                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'
                ]"
              >
                <component :is="tab.icon" class="h-4 w-4" />
                <span>{{ tab.label }}</span>
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- Profile Tab -->
            <div v-show="activeTab === 'profile'" class="space-y-6">
              <!-- Personal Info -->
              <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Informations personnelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-slate-400 mb-1">Email</p>
                    <p class="text-slate-100">{{ client.email || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Téléphone</p>
                    <p class="text-slate-100">{{ client.phone || '—' }}</p>
                  </div>
                  <div class="md:col-span-2">
                    <p class="text-slate-400 mb-1">Adresse</p>
                    <p class="text-slate-100">{{ client.address || '—' }}</p>
                  </div>
                </div>
              </div>

              <!-- Physical Data -->
              <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Données physiques actuelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div>
                    <p class="text-slate-400 mb-1">Poids</p>
                    <p class="text-slate-100 text-xl font-bold">{{ latestMeasurement?.weight ? latestMeasurement.weight + ' kg' : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Taille</p>
                    <p class="text-slate-100 text-xl font-bold">{{ latestMeasurement?.height ? latestMeasurement.height + ' cm' : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">IMC</p>
                    <p class="text-slate-100 text-xl font-bold">{{ latestMeasurement?.bmi ? latestMeasurement.bmi.toFixed(1) : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Tour de poitrine</p>
                    <p class="text-slate-100">{{ latestMeasurement?.chest ? latestMeasurement.chest + ' cm' : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Tour de taille</p>
                    <p class="text-slate-100">{{ latestMeasurement?.waist ? latestMeasurement.waist + ' cm' : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Tour de hanches</p>
                    <p class="text-slate-100">{{ latestMeasurement?.hips ? latestMeasurement.hips + ' cm' : '—' }}</p>
                  </div>
                </div>
                <p v-if="latestMeasurement" class="text-xs text-slate-500 mt-4">
                  Dernière mise à jour : {{ new Date(latestMeasurement.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
              </div>

              <!-- Health & Nutrition -->
              <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Santé & Physiologie</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-slate-400 mb-1">Problèmes de santé</p>
                    <p class="text-slate-100">{{ client.health_issues || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Médicaments</p>
                    <p class="text-slate-100">{{ client.medications || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Blessures et douleurs</p>
                    <p class="text-slate-100">{{ client.injuries || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Niveau de stress</p>
                    <p class="text-slate-100">{{ client.stress_level ? client.stress_level + '/10' : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Qualité du sommeil</p>
                    <p class="text-slate-100">{{ client.sleep_quality || '—' }}</p>
                  </div>
                  <div v-if="client.menstrual_tracking">
                    <p class="text-slate-400 mb-1">Dernières règles</p>
                    <p class="text-slate-100">{{ client.last_period ? new Date(client.last_period).toLocaleDateString('fr-FR') : '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Allergies alimentaires</p>
                    <p class="text-slate-100">{{ client.food_allergies || client.allergies || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Aliments non aimés</p>
                    <p class="text-slate-100">{{ client.food_dislikes || client.dislikes || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Régime alimentaire</p>
                    <p class="text-slate-100">{{ client.dietary_preference || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Compléments alimentaires</p>
                    <p class="text-slate-100">{{ client.supplements || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Budget courses</p>
                    <p class="text-slate-100">{{ client.grocery_budget || '—' }}</p>
                  </div>
                  <div v-if="client.kitchen_equipment">
                    <p class="text-slate-400 mb-1">Équipement cuisine</p>
                    <p class="text-slate-100">{{ Array.isArray(client.kitchen_equipment) ? client.kitchen_equipment.join(', ') : client.kitchen_equipment }}</p>
                  </div>
                </div>
              </div>

              <!-- Sports & Psychology -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                  <h3 class="text-lg font-semibold text-slate-200 mb-4">Contexte sportif & Logistique</h3>
                  <div class="space-y-3 text-sm">
                    <div>
                      <p class="text-slate-400 mb-1">Niveau sportif</p>
                      <p class="text-slate-100">{{ client.sports_level || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Sports pratiqués</p>
                      <p class="text-slate-100">{{ client.sports_practiced || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Fréquence sportive</p>
                      <p class="text-slate-100">{{ client.sports_frequency || client.training_frequency || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Durée par séance</p>
                      <p class="text-slate-100">{{ client.session_duration || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Activité quotidienne</p>
                      <p class="text-slate-100">{{ client.daily_activity || '—' }}</p>
                    </div>
                    <div v-if="client.available_equipment">
                      <p class="text-slate-400 mb-1">Matériel disponible</p>
                      <p class="text-slate-100">{{ Array.isArray(client.available_equipment) ? client.available_equipment.join(', ') : client.available_equipment }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                  <h3 class="text-lg font-semibold text-slate-200 mb-4">Psychologie & Objectifs</h3>
                  <div class="space-y-3 text-sm">
                    <div>
                      <p class="text-slate-400 mb-1">Objectif principal</p>
                      <p class="text-slate-100">{{ client.main_goal || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Motivation profonde</p>
                      <p class="text-slate-100">{{ client.deep_motivation || client.motivation || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Obstacles</p>
                      <p class="text-slate-100">{{ client.obstacles || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Style de coaching</p>
                      <p class="text-slate-100">{{ client.coaching_style || '—' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- General Comments -->
              <div v-if="client.general_comments" class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Commentaires généraux</h3>
                <p class="text-sm text-slate-300 whitespace-pre-line">{{ client.general_comments }}</p>
              </div>
            </div>

            <!-- Analytics Tab -->
            <div v-show="activeTab === 'analytics'" class="space-y-6">
              <!-- Stats Overview -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-xl p-4 border border-purple-500/30">
                  <p class="text-xs uppercase text-purple-300 mb-1">Relevés totaux</p>
                  <p class="text-3xl font-bold text-white">{{ client.measurements?.length || 0 }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl p-4 border border-blue-500/30">
                  <p class="text-xs uppercase text-blue-300 mb-1">Poids actuel</p>
                  <p class="text-3xl font-bold text-white">
                    {{ latestMeasurement?.weight ? latestMeasurement.weight + ' kg' : '—' }}
                  </p>
                </div>
                <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl p-4 border border-green-500/30">
                  <p class="text-xs uppercase text-green-300 mb-1">IMC actuel</p>
                  <p class="text-3xl font-bold text-white">
                    {{ calculateBmi(latestMeasurement) || latestMeasurement?.bmi?.toFixed(1) || '—' }}
                  </p>
                </div>
                <div class="bg-gradient-to-br from-pink-500/20 to-pink-600/20 rounded-xl p-4 border border-pink-500/30">
                  <p class="text-xs uppercase text-pink-300 mb-1">Photos</p>
                  <p class="text-3xl font-bold text-white">
                    {{ client.measurements?.filter(m => m.photo_front || m.photo_side || m.photo_back).length || 0 }}
                  </p>
                </div>
              </div>

              <!-- Measurements List -->
              <div class="bg-slate-800/30 rounded-xl border border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-slate-200">Historique des relevés</h3>
                  <button
                    @click="openMeasurementModal()"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-semibold hover:from-purple-600 hover:to-pink-600 transition-all"
                  >
                    <Plus class="h-4 w-4" />
                    <span>Ajouter un relevé</span>
                  </button>
                </div>

                <div v-if="client.measurements && client.measurements.length > 0" class="space-y-3">
                  <div
                    v-for="measurement in client.measurements"
                    :key="measurement.id"
                    class="bg-slate-700/30 rounded-lg p-4 border border-slate-600"
                  >
                    <div class="flex items-start justify-between mb-3">
                      <div>
                        <p class="text-sm font-semibold text-slate-200">
                          {{ new Date(measurement.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                        </p>
                        <p class="text-xs text-slate-400">
                          {{ new Date(measurement.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}
                        </p>
                      </div>
                      <div class="flex items-center gap-2">
                        <button
                          v-if="measurement.photo_front || measurement.photo_side || measurement.photo_back"
                          @click="viewPhotos(measurement)"
                          class="px-2 py-1 bg-indigo-500/20 border border-indigo-500/30 rounded text-xs text-indigo-300 hover:bg-indigo-500/30 transition-colors"
                        >
                          📸 Voir photos
                        </button>
                        <button
                          @click="openMeasurementModal(measurement)"
                          class="flex items-center gap-1 px-2 py-1 rounded text-xs text-slate-300 hover:bg-slate-600/50 transition-colors"
                        >
                          <Edit2 class="h-3 w-3" />
                        </button>
                        <button
                          @click="deleteMeasurement(measurement)"
                          class="flex items-center gap-1 px-2 py-1 rounded text-xs text-red-400 hover:bg-red-500/20 transition-colors"
                        >
                          <Trash2 class="h-3 w-3" />
                        </button>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                      <div>
                        <p class="text-slate-400 text-xs mb-0.5">Poids</p>
                        <p class="text-slate-100 font-semibold">{{ measurement.weight ? measurement.weight + ' kg' : '—' }}</p>
                      </div>
                      <div>
                        <p class="text-slate-400 text-xs mb-0.5">Taille</p>
                        <p class="text-slate-100 font-semibold">{{ measurement.height ? measurement.height + ' cm' : '—' }}</p>
                      </div>
                      <div>
                        <p class="text-slate-400 text-xs mb-0.5">IMC</p>
                        <p class="text-slate-100 font-semibold">{{ calculateBmi(measurement) || (measurement.bmi ? measurement.bmi.toFixed(1) : '—') }}</p>
                      </div>
                      <div>
                        <p class="text-slate-400 text-xs mb-0.5">Poitrine</p>
                        <p class="text-slate-100 font-semibold">{{ measurement.chest ? measurement.chest + ' cm' : '—' }}</p>
                      </div>
                      <div>
                        <p class="text-slate-400 text-xs mb-0.5">Taille</p>
                        <p class="text-slate-100 font-semibold">{{ measurement.waist ? measurement.waist + ' cm' : '—' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-12 text-slate-400">
                  <TrendingUp class="h-12 w-12 mx-auto mb-4 opacity-50" />
                  <p class="text-sm">Aucun relevé pour le moment</p>
                  <p class="text-xs mt-2">Le client peut ajouter ses métriques depuis son profil</p>
                </div>
              </div>

              <!-- Info Note -->
              <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                <p class="text-sm text-blue-200">
                  💡 <strong>Astuce :</strong> Le client peut ajouter et modifier ses métriques depuis son profil. 
                  Les graphiques d'évolution sont visibles dans l'onglet "Mon Évolution" de son dashboard.
                </p>
              </div>
            </div>

            <!-- Messages Tab -->
            <div v-show="activeTab === 'messages'" class="space-y-4">
              <!-- Messages List -->
              <div class="bg-slate-800/30 rounded-xl border border-slate-700 overflow-hidden">
                <div class="h-[500px] overflow-y-auto p-4 space-y-3">
                  <div
                    v-for="message in client.messages"
                    :key="message.id"
                    :class="[
                      'flex',
                      message.sender_type === 'coach' ? 'justify-end' : 'justify-start'
                    ]"
                  >
                    <div
                      :class="[
                        'max-w-[70%] rounded-2xl px-4 py-3',
                        message.sender_type === 'coach'
                          ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white'
                          : 'bg-slate-700/50 text-slate-100'
                      ]"
                    >
                      <p class="text-sm whitespace-pre-line">{{ message.message }}</p>
                      
                      <a
                        v-if="message.attachment_path"
                        :href="route('dashboard.clients.messages.download', [client.id, message.id])"
                        class="mt-2 flex items-center gap-2 p-2 rounded-lg bg-black/20 hover:bg-black/30 transition-colors text-xs"
                      >
                        <Paperclip class="h-3.5 w-3.5" />
                        <span class="flex-1 truncate">{{ message.attachment_name }}</span>
                        <span class="text-xs opacity-75">{{ formatFileSize(message.attachment_size) }}</span>
                        <Download class="h-3.5 w-3.5" />
                      </a>
                      
                      <p class="text-xs opacity-75 mt-2">
                        {{ formatMessageDate(message.created_at) }}
                      </p>
                    </div>
                  </div>
                  
                  <div v-if="!client.messages || client.messages.length === 0" class="text-center py-12 text-slate-400">
                    <MessageSquare class="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p class="text-sm">Aucun message pour le moment</p>
                    <p class="text-xs mt-2">Envoyez un message pour commencer la conversation</p>
                  </div>
                </div>
              </div>

              <!-- Message Form -->
              <form @submit.prevent="sendMessage" class="bg-slate-800/30 rounded-xl border border-slate-700 p-4">
                <div class="space-y-3">
                  <textarea
                    v-model="messageForm.message"
                    rows="3"
                    placeholder="Écrivez votre message..."
                    class="w-full rounded-lg border-slate-600 bg-slate-900/50 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 text-sm"
                    required
                  ></textarea>
                  
                  <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                      <label class="cursor-pointer">
                        <input
                          ref="attachmentInput"
                          type="file"
                          @change="handleFileSelect"
                          class="hidden"
                          accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                        />
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-700/50 hover:bg-slate-700 transition-colors text-sm text-slate-300">
                          <Paperclip class="h-4 w-4" />
                          <span>Joindre un fichier</span>
                        </div>
                      </label>
                      
                      <div v-if="selectedFile" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-700/50 text-sm text-slate-300">
                        <span class="truncate max-w-[200px]">{{ selectedFile.name }}</span>
                        <span class="text-xs opacity-75">{{ formatFileSize(selectedFile.size) }}</span>
                        <button
                          type="button"
                          @click="removeFile"
                          class="text-red-400 hover:text-red-300"
                        >
                          ✕
                        </button>
                      </div>
                    </div>
                    
                    <button
                      type="submit"
                      :disabled="messageForm.processing || !messageForm.message.trim()"
                      class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold hover:from-purple-600 hover:to-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                    >
                      <Send class="h-4 w-4" />
                      <span>{{ messageForm.processing ? 'Envoi...' : 'Envoyer' }}</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <!-- Documents Tab -->
            <div v-show="activeTab === 'documents'" class="space-y-6">
              <!-- Notes du Coach -->
              <div class="bg-slate-800/30 rounded-xl border border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-slate-200">Notes du coach</h3>
                  <button
                    @click="openNoteModal()"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-semibold hover:from-purple-600 hover:to-pink-600 transition-all"
                  >
                    <Plus class="h-4 w-4" />
                    <span>Nouvelle note</span>
                  </button>
                </div>

                <div v-if="client.notes && client.notes.length > 0" class="space-y-3">
                  <div
                    v-for="note in client.notes"
                    :key="note.id"
                    class="bg-slate-700/30 rounded-lg p-4 border border-slate-600"
                  >
                    <p class="text-sm text-slate-200 whitespace-pre-line mb-3">{{ note.content }}</p>
                    <div class="flex items-center justify-between">
                      <p class="text-xs text-slate-400">{{ formatDate(note.created_at) }}</p>
                      <div class="flex items-center gap-2">
                        <button
                          @click="openNoteModal(note)"
                          class="flex items-center gap-1 px-2 py-1 rounded text-xs text-slate-300 hover:bg-slate-600/50 transition-colors"
                        >
                          <Edit2 class="h-3 w-3" />
                          <span>Modifier</span>
                        </button>
                        <button
                          @click="deleteNote(note)"
                          class="flex items-center gap-1 px-2 py-1 rounded text-xs text-red-400 hover:bg-red-500/20 transition-colors"
                        >
                          <Trash2 class="h-3 w-3" />
                          <span>Supprimer</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-8 text-slate-400">
                  <p class="text-sm">Aucune note pour le moment</p>
                </div>
              </div>

              <!-- Documents Partagés -->
              <div class="bg-slate-800/30 rounded-xl border border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-slate-200">Documents partagés</h3>
                  <button
                    @click="openDocumentModal"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-semibold hover:from-purple-600 hover:to-pink-600 transition-all"
                  >
                    <Upload class="h-4 w-4" />
                    <span>Gérer les documents</span>
                  </button>
                </div>

                <div class="space-y-4">
                  <div
                    v-for="[typeKey, typeLabel] in Object.entries(documentTypes)"
                    :key="typeKey"
                    class="bg-slate-700/30 rounded-lg p-4 border border-slate-600"
                  >
                    <div class="flex items-center justify-between mb-3">
                      <h4 class="text-sm font-semibold text-slate-200">{{ typeLabel }}</h4>
                      <span class="text-xs text-slate-400">
                        {{ documentsByType(typeKey).length }} document{{ documentsByType(typeKey).length > 1 ? 's' : '' }}
                      </span>
                    </div>

                    <div v-if="documentsByType(typeKey).length > 0" class="space-y-2">
                      <div
                        v-for="doc in documentsByType(typeKey).slice(0, 3)"
                        :key="doc.id"
                        class="flex items-center justify-between text-xs p-2 rounded bg-slate-800/50"
                      >
                        <div class="flex-1">
                          <p class="text-slate-200 font-medium">v{{ doc.version }} · {{ doc.title || doc.filename }}</p>
                          <p class="text-slate-400 text-[10px]">{{ formatDate(doc.created_at) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                          <a
                            :href="route('dashboard.clients.documents.download', doc.id)"
                            class="text-purple-400 hover:text-purple-300"
                          >
                            <Download class="h-4 w-4" />
                          </a>
                          <button
                            @click="deleteDocument(doc)"
                            class="text-red-400 hover:text-red-300"
                          >
                            <Trash2 class="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                    <p v-else class="text-xs text-slate-500">Aucun document partagé</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Note Modal -->
    <Modal :show="showNoteModal" @close="closeNoteModal" max-width="lg">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
          <h2 class="text-lg font-semibold text-slate-100">
            {{ editingNote ? 'Modifier la note' : 'Nouvelle note' }}
          </h2>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-200 text-xl"
            @click="closeNoteModal"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="submitNote" class="p-6 space-y-4">
          <div>
            <InputLabel for="note_content" value="Contenu de la note" class="text-slate-200" />
            <textarea
              id="note_content"
              v-model="noteForm.content"
              rows="6"
              class="mt-1 block w-full rounded-lg border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500"
              placeholder="Écrivez votre note..."
              required
            ></textarea>
            <InputError class="mt-1" :message="noteForm.errors.content" />
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 transition-colors"
              @click="closeNoteModal"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-sm font-semibold text-white hover:from-purple-600 hover:to-pink-600 disabled:opacity-50 transition-all"
              :disabled="noteForm.processing"
            >
              {{ noteForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Measurement Modal -->
    <Modal :show="showMeasurementModal" @close="closeMeasurementModal" max-width="2xl">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
          <h2 class="text-lg font-semibold text-slate-100">
            {{ editingMeasurement ? 'Modifier le relevé' : 'Nouveau relevé' }}
          </h2>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-200 text-xl"
            @click="closeMeasurementModal"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="submitMeasurement" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="weight" value="Poids (kg)" class="text-slate-200" />
              <TextInput
                id="weight"
                v-model="measurementForm.weight"
                type="number"
                step="0.1"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="measurementForm.errors.weight" />
            </div>

            <div>
              <InputLabel for="height" value="Taille (cm)" class="text-slate-200" />
              <TextInput
                id="height"
                v-model="measurementForm.height"
                type="number"
                step="0.1"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="measurementForm.errors.height" />
            </div>

            <div>
              <InputLabel for="chest" value="Tour de poitrine (cm)" class="text-slate-200" />
              <TextInput
                id="chest"
                v-model="measurementForm.chest"
                type="number"
                step="0.1"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="measurementForm.errors.chest" />
            </div>

            <div>
              <InputLabel for="waist" value="Tour de taille (cm)" class="text-slate-200" />
              <TextInput
                id="waist"
                v-model="measurementForm.waist"
                type="number"
                step="0.1"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="measurementForm.errors.waist" />
            </div>

            <div>
              <InputLabel for="hips" value="Tour de hanches (cm)" class="text-slate-200" />
              <TextInput
                id="hips"
                v-model="measurementForm.hips"
                type="number"
                step="0.1"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="measurementForm.errors.hips" />
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 transition-colors"
              @click="closeMeasurementModal"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-sm font-semibold text-white hover:from-purple-600 hover:to-pink-600 disabled:opacity-50 transition-all"
              :disabled="measurementForm.processing"
            >
              {{ measurementForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Photo Modal -->
    <Modal :show="showPhotoModal" @close="closePhotoModal" max-width="6xl">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
          <h2 class="text-lg font-semibold text-slate-100">
            Photos du {{ selectedMeasurement ? new Date(selectedMeasurement.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) : '' }}
          </h2>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-200 text-xl"
            @click="closePhotoModal"
          >
            ✕
          </button>
        </div>

        <div class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-if="selectedMeasurement?.photo_front" class="space-y-2">
              <p class="text-sm font-semibold text-slate-300 text-center">Vue de face</p>
              <img
                :src="getPhotoUrl(selectedMeasurement, 'front')"
                alt="Photo de face"
                class="w-full rounded-lg border border-slate-700"
              />
            </div>
            <div v-if="selectedMeasurement?.photo_side" class="space-y-2">
              <p class="text-sm font-semibold text-slate-300 text-center">Vue de profil</p>
              <img
                :src="getPhotoUrl(selectedMeasurement, 'side')"
                alt="Photo de profil"
                class="w-full rounded-lg border border-slate-700"
              />
            </div>
            <div v-if="selectedMeasurement?.photo_back" class="space-y-2">
              <p class="text-sm font-semibold text-slate-300 text-center">Vue de dos</p>
              <img
                :src="getPhotoUrl(selectedMeasurement, 'back')"
                alt="Photo de dos"
                class="w-full rounded-lg border border-slate-700"
              />
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="button"
              class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 transition-colors"
              @click="closePhotoModal"
            >
              Fermer
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Document Upload Modal -->
    <Modal :show="showDocumentModal" @close="closeDocumentModal" max-width="2xl">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
          <h2 class="text-lg font-semibold text-slate-100">Ajouter un document</h2>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-200 text-xl"
            @click="closeDocumentModal"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="submitDocument" class="p-6 space-y-4">
          <div>
            <InputLabel for="doc_type" value="Type de document *" class="text-slate-200" />
            <select
              id="doc_type"
              v-model="documentForm.type"
              class="mt-1 block w-full rounded-lg border-slate-700 bg-slate-950 text-slate-100 focus;border-purple-500 focus:ring-purple-500"
              required
            >
              <option value="">-- Sélectionnez un type --</option>
              <option v-for="(label, key) in documentTypes" :key="key" :value="key">
                {{ label }}
              </option>
            </select>
            <InputError class="mt-1" :message="documentForm.errors.type" />
          </div>

          <div>
            <InputLabel for="doc_title" value="Titre / Note (optionnel)" class="text-slate-200" />
            <TextInput
              id="doc_title"
              v-model="documentForm.title"
              type="text"
              class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              placeholder="Ex: Programme semaine 1, Plan nutritionnel Mars..."
            />
            <InputError class="mt-1" :message="documentForm.errors.title" />
          </div>

          <div>
            <InputLabel for="doc_file" value="Fichier *" class="text-slate-200" />
            <input
              ref="documentFileInput"
              id="doc_file"
              type="file"
              @change="handleDocumentFileSelect"
              class="mt-1 block w-full text-sm text-slate-400
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:text-sm file:font-semibold
                file:bg-purple-500 file:text-white
                hover:file:bg-purple-600
                file:cursor-pointer"
              accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
              required
            />
            <p class="mt-1 text-xs text-slate-500">PDF, Word, Excel, Images (max 10 MB)</p>
            <InputError class="mt-1" :message="documentForm.errors.document" />
          </div>

          <div v-if="selectedDocumentFile" class="p-3 bg-slate-800/50 rounded-lg border border-slate-700">
            <p class="text-sm text-slate-300">
              📄 {{ selectedDocumentFile.name }}
              <span class="text-xs text-slate-500">({{ formatFileSize(selectedDocumentFile.size) }})</span>
            </p>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 transition-colors"
              @click="closeDocumentModal"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-sm font-semibold text-white hover:from-purple-600 hover:to-pink-600 disabled:opacity-50 transition-all"
              :disabled="documentForm.processing"
            >
              {{ documentForm.processing ? 'Upload en cours...' : 'Ajouter le document' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <Modal :show="actionModal.show" @close="closeActionModal" max-width="lg">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 sm:p-8 space-y-6">
          <div class="flex items-start gap-4">
            <div
              v-if="currentActionConfig"
              :class="currentActionConfig.iconClasses"
              class="h-12 w-12 rounded-2xl flex items-center justify-center"
            >
              <component :is="currentActionConfig.icon" class="h-6 w-6" />
            </div>
            <div>
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-1">
                Confirmation
              </p>
              <h3 class="text-xl font-semibold text-slate-50">
                {{ currentActionConfig?.title }}
              </h3>
              <p class="text-sm text-slate-400 mt-2 leading-relaxed">
                {{ currentActionConfig?.description }}
              </p>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              type="button"
              class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 transition-all"
              :disabled="actionModal.loading"
              @click="closeActionModal"
            >
              Annuler
            </button>
            <button
              type="button"
              class="rounded-xl px-4 py-2 text-sm font-semibold text-white transition-all inline-flex items-center gap-2"
              :class="currentActionConfig?.confirmClasses"
              :disabled="actionModal.loading"
              @click="handleConfirmAction"
            >
              <svg
                v-if="actionModal.loading"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              {{ actionModal.loading ? 'Traitement...' : currentActionConfig?.confirmLabel }}
            </button>
          </div>
        </div>
      </div>
    </Modal>
</template>
