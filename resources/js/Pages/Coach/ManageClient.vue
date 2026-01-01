<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { User, TrendingUp, MessageSquare, FileText, ArrowLeft, Send, Paperclip, Download, Plus, Edit2, Trash2, Upload } from 'lucide-vue-next';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  client: Object,
  documentTypes: Object,
});

const activeTab = ref('profile');
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

const unreadClientMessages = computed(() => {
  return props.client.messages?.filter(m => m.sender_type === 'client' && !m.is_read).length || 0;
});

const goBack = () => {
  router.visit(route('dashboard.clients.index', { beta: 1 }));
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
  if (!confirm('Supprimer cette note ?')) return;
  
  router.delete(route('dashboard.clients.notes.destroy', note.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['client'] });
    },
  });
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
</script>

<template>
  <AuthenticatedLayout>
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
              </div>
              <div v-if="unreadClientMessages > 0" class="flex items-center gap-2 px-4 py-2 bg-indigo-500/20 border border-indigo-500/30 rounded-full">
                <MessageSquare class="h-4 w-4 text-indigo-400" />
                <span class="text-sm font-semibold text-indigo-300">{{ unreadClientMessages }} nouveau{{ unreadClientMessages > 1 ? 'x' : '' }} message{{ unreadClientMessages > 1 ? 's' : '' }}</span>
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
                  {{ latestMeasurement?.bmi ? latestMeasurement.bmi.toFixed(1) : '—' }}
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
                @click="activeTab = tab.id"
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
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Santé & Nutrition</h3>
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
                    <p class="text-slate-400 mb-1">Allergies alimentaires</p>
                    <p class="text-slate-100">{{ client.food_allergies || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Aliments non aimés</p>
                    <p class="text-slate-100">{{ client.food_dislikes || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400 mb-1">Régime alimentaire</p>
                    <p class="text-slate-100">{{ client.dietary_preference || '—' }}</p>
                  </div>
                </div>
              </div>

              <!-- Sports & Psychology -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                  <h3 class="text-lg font-semibold text-slate-200 mb-4">Contexte sportif</h3>
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
                      <p class="text-slate-100">{{ client.sports_frequency || '—' }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700">
                  <h3 class="text-lg font-semibold text-slate-200 mb-4">Psychologie</h3>
                  <div class="space-y-3 text-sm">
                    <div>
                      <p class="text-slate-400 mb-1">Motivation</p>
                      <p class="text-slate-100">{{ client.motivation || '—' }}</p>
                    </div>
                    <div>
                      <p class="text-slate-400 mb-1">Obstacles</p>
                      <p class="text-slate-100">{{ client.obstacles || '—' }}</p>
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
                    {{ latestMeasurement?.bmi ? latestMeasurement.bmi.toFixed(1) : '—' }}
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
                    @click="router.visit(route('clients.dashboard.profile', client.share_token))"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-semibold hover:from-purple-600 hover:to-pink-600 transition-all"
                  >
                    <Plus class="h-4 w-4" />
                    <span>Voir le profil client</span>
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
                        <span v-if="measurement.photo_front || measurement.photo_side || measurement.photo_back" class="px-2 py-1 bg-indigo-500/20 border border-indigo-500/30 rounded text-xs text-indigo-300">
                          📸 Photos
                        </span>
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
                        <p class="text-slate-100 font-semibold">{{ measurement.bmi ? measurement.bmi.toFixed(1) : '—' }}</p>
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
                  <a
                    :href="route('dashboard.clients.index', { beta: 1 })"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-700/50 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition-all"
                  >
                    <Upload class="h-4 w-4" />
                    <span>Gérer les documents</span>
                  </a>
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
                        <a
                          :href="route('dashboard.clients.documents.download', doc.id)"
                          class="text-purple-400 hover:text-purple-300"
                        >
                          <Download class="h-4 w-4" />
                        </a>
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
    <div
      v-if="showNoteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
      @click="closeNoteModal"
    >
      <div
        class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        @click.stop
      >
        <div class="flex items-center justify-between mb-4">
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

        <form @submit.prevent="submitNote" class="space-y-4">
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
    </div>
  </AuthenticatedLayout>
</template>
