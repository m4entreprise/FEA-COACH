<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import { useAutoAnimate } from '@formkit/auto-animate/vue';
import Modal from '@/Components/Modal.vue';
import {
    LayoutDashboard,
    Globe2,
    Palette,
    FileText,
    Image as ImageIcon,
    HelpCircle,
    CreditCard,
    Scale,
    Users,
    Mail,
    LifeBuoy,
    User,
    Menu,
    ArrowLeftRight,
    GraduationCap,
    Sparkles,
    ExternalLink,
    LogOut,
} from 'lucide-vue-next';

const props = defineProps({
    coach: Object,
    stats: Object,
    recentTransformations: Array,
    customDomain: {
        type: Object,
        default: null,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
    error: String,
    hasCompletedOnboarding: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const [sidebarParent] = useAutoAnimate();
const [contentParent] = useAutoAnimate();

const activeCategory = ref('general');
const sidebarOpen = ref(false);

const coachSiteUrl = computed(() => {
    if (!props.coach) return null;
    const slug = props.coach.slug || props.coach.subdomain;
    if (!slug) return null;
    return route('coach.site', { coach_slug: slug });
});

const safeStats = computed(() => props.stats || {});
const hasCustomDomainOrder = computed(
    () => !!props.customDomain || !!props.coach?.desired_custom_domain,
);
const customDomainStatus = computed(() => props.customDomain?.status ?? null);
const hasCustomContactLock = computed(
    () => !!props.coach?.custom_contact_locked_until,
);

const showDomainModal = ref(false);
const desiredDomain = ref('');

const showContactModal = ref(false);
const contactWebsite = ref(false);
const contactBranding = ref(false);
const contactSocial = ref(false);
const contactMessage = ref('');

const goCategory = (id) => {
    activeCategory.value = id;
    sidebarOpen.value = false;
};

const buyCustomDomain = (requestedDomain = null) => {
    // Create a form and submit it (bypass Inertia for external redirects)
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('dashboard.subscription.custom-domain');
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfInput);

    if (requestedDomain) {
        const domainInput = document.createElement('input');
        domainInput.type = 'hidden';
        domainInput.name = 'desired_domain';
        domainInput.value = requestedDomain;
        form.appendChild(domainInput);
    }
    
    document.body.appendChild(form);
    form.submit();
};

const openDomainModal = () => {
    desiredDomain.value = '';
    showDomainModal.value = true;
};

const submitDomainPurchase = () => {
    const value = desiredDomain.value.trim();
    if (!value) {
        alert('Veuillez indiquer un nom de domaine souhaité.');
        return;
    }

    showDomainModal.value = false;
    buyCustomDomain(value);
};

const openContactModal = () => {
    contactWebsite.value = false;
    contactBranding.value = false;
    contactSocial.value = false;
    contactMessage.value = '';
    showContactModal.value = true;
};

const submitContactRequest = () => {
    const serviceTypes = [];
    if (contactWebsite.value) serviceTypes.push('Site web');
    if (contactBranding.value) serviceTypes.push('Branding');
    if (contactSocial.value) serviceTypes.push('Réseaux sociaux');

    if (!serviceTypes.length) {
        alert('Sélectionnez au moins un service.');
        return;
    }

    router.post(route('dashboard.contact.custom'), {
        service_types: serviceTypes,
        message: contactMessage.value || null,
    }, {
        preserveScroll: true,
        onFinish: () => {
            showContactModal.value = false;
        },
    });
};

const logout = () => {
    router.post(route('logout'));
};

// onMounted(() => {
//     toast('Panel coach', {
//         description: 'Bienvenue sur votre dashboard coach.',
//     });
// });
</script>

<template>
    <Head title="Dashboard coach" />

    <div class="min-h-screen flex bg-slate-950 text-slate-50">
        <!-- Desktop sidebar -->
        <aside class="hidden md:flex w-72 flex-col border-r border-slate-800 bg-slate-900/80 backdrop-blur-xl">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        <span>U</span>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Panel coach</p>
                        <p class="text-sm font-semibold">UNICOACH</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-100 border border-emerald-500/40">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 animate-breathe"></span>
                    En ligne
                </span>
            </div>

            <nav ref="sidebarParent" class="flex-1 px-3 py-4 space-y-2 overflow-y-auto text-sm">
                <button
                    type="button"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                    :class="activeCategory === 'general' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                    @click="goCategory('general')"
                >
                    <LayoutDashboard class="h-4 w-4" />
                    <div class="flex flex-col">
                        <span class="font-semibold">General</span>
                        <span class="text-[11px] text-slate-400">Vue d'ensemble et site public</span>
                    </div>
                </button>

                <button
                    type="button"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                    :class="activeCategory === 'site' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                    @click="goCategory('site')"
                >
                    <Globe2 class="h-4 w-4" />
                    <div class="flex flex-col">
                        <span class="font-semibold">Site vitrine</span>
                        <span class="text-[11px] text-slate-400">Apparence, contenu, galerie, FAQ</span>
                    </div>
                </button>

                <button
                    type="button"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                    :class="activeCategory === 'business' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                    @click="goCategory('business')"
                >
                    <CreditCard class="h-4 w-4" />
                    <div class="flex flex-col">
                        <span class="font-semibold">Business</span>
                        <span class="text-[11px] text-slate-400">Offres, legal, clients, messages</span>
                    </div>
                </button>

                <button
                    type="button"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                    :class="activeCategory === 'account' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                    @click="goCategory('account')"
                >
                    <User class="h-4 w-4" />
                    <div class="flex flex-col">
                        <span class="font-semibold">Compte</span>
                        <span class="text-[11px] text-slate-400">Abonnement, support, profil</span>
                    </div>
                </button>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <button
                    type="button"
                    @click="logout"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800 transition-colors"
                >
                    <LogOut class="h-3.5 w-3.5" />
                    Se déconnecter
                </button>
            </div>
        </aside>

        <!-- Mobile sidebar -->
        <transition name="fade">
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden">
                <div class="flex-1 bg-black/50" @click="sidebarOpen = false" />
                <aside class="w-72 flex flex-col border-l border-slate-800 bg-slate-900/95 backdrop-blur-xl">
                    <div class="px-4 py-4 flex items-center justify-between border-b border-slate-800">
                        <p class="text-sm font-semibold">Navigation</p>
                        <button
                            type="button"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-600"
                            @click="sidebarOpen = false"
                        >
                            <span class="sr-only">Fermer</span>
                            <span class="text-lg">&times;</span>
                        </button>
                    </div>

                    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto text-sm">
                        <button
                            type="button"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                            :class="activeCategory === 'general' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                            @click="goCategory('general')"
                        >
                            <LayoutDashboard class="h-4 w-4" />
                            <span class="font-semibold">General</span>
                        </button>
                        <button
                            type="button"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                            :class="activeCategory === 'site' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                            @click="goCategory('site')"
                        >
                            <Globe2 class="h-4 w-4" />
                            <span class="font-semibold">Site vitrine</span>
                        </button>
                        <button
                            type="button"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                            :class="activeCategory === 'business' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                            @click="goCategory('business')"
                        >
                            <CreditCard class="h-4 w-4" />
                            <span class="font-semibold">Business</span>
                        </button>
                        <button
                            type="button"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
                            :class="activeCategory === 'account' ? 'bg-slate-800 text-slate-50' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
                            @click="goCategory('account')"
                        >
                            <User class="h-4 w-4" />
                            <span class="font-semibold">Compte</span>
                        </button>
                    </nav>

                    <div class="p-4 border-t border-slate-800 text-xs text-slate-400 space-y-2">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-800 transition-colors w-full"
                            @click="sidebarOpen = false"
                        >
                            <ArrowLeftRight class="h-3 w-3" />
                            Dashboard classique
                        </Link>
                    </div>
                </aside>
            </div>
        </transition>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Top bar -->
            <header class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 border border-slate-700 text-slate-200 hover:bg-slate-800 md:hidden"
                        @click="sidebarOpen = true"
                    >
                        <Menu class="h-4 w-4" />
                    </button>
                    <div class="flex flex-col">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Panel coach</p>
                        <h1 class="text-base md:text-lg font-semibold">
                            Bonjour, {{ authUser?.name }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        v-if="coachSiteUrl"
                        :href="coachSiteUrl"
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-2 rounded-full bg-emerald-500/90 px-4 py-2 text-xs font-semibold text-emerald-950 shadow-lg hover:bg-emerald-400 transition-colors"
                    >
                        <Globe2 class="h-4 w-4" />
                        Voir mon site
                    </a>

                    <Link
                        :href="route('dashboard.subscription')"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-800/90 px-3 py-1.5 text-xs font-medium text-slate-100 border border-slate-600 hover:bg-slate-700"
                    >
                        <CreditCard class="h-3.5 w-3.5" />
                        Abonnement
                    </Link>
                </div>
            </header>

            <!-- Content -->
            <main ref="contentParent" class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8">
                <!-- Admin case -->
                <section v-if="isAdmin" class="max-w-5xl mx-auto space-y-6">
                    <div class="rounded-2xl border border-blue-500/40 bg-blue-950/40 p-6 shadow-xl">
                        <h2 class="text-lg font-semibold mb-2 flex items-center gap-2">
                            <LayoutDashboard class="h-5 w-5 text-blue-300" />
                            Dashboard coach uniquement
                        </h2>
                        <p class="text-sm text-blue-100">
                            Cette vue est destinee aux coachs. Pour l'administration, utilisez le dashboard admin.
                        </p>
                        <div class="mt-4 flex gap-3">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-3 py-2 text-xs font-semibold text-blue-50 hover:bg-white/20 border border-white/20"
                            >
                                <LayoutDashboard class="h-4 w-4" />
                                Ouvrir le dashboard classique
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- Error case -->
                <section v-else-if="error" class="max-w-5xl mx-auto space-y-6">
                    <div class="rounded-2xl border border-red-500/40 bg-red-950/40 p-6 shadow-xl">
                        <h2 class="text-lg font-semibold mb-2 flex items-center gap-2">
                            <HelpCircle class="h-5 w-5 text-red-300" />
                            Profil coach manquant
                        </h2>
                        <p class="text-sm text-red-100 mb-2">
                            {{ error }}
                        </p>
                        <p class="text-xs text-red-200">
                            Terminez votre onboarding et vos reglages pour acceder au panel.
                        </p>
                    </div>
                </section>

                <!-- Main categories -->
                <section v-else class="max-w-6xl mx-auto space-y-8">
                    <!-- General -->
                    <div v-if="activeCategory === 'general'" class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                                    <LayoutDashboard class="h-5 w-5 text-purple-300" />
                                    Vue d'ensemble
                                </h2>
                                <p class="text-sm text-slate-400 mt-1">
                                    Apercu rapide de votre site, de votre profil et de votre business.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                            <!-- Profil & site status -->
                            <div class="lg:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl space-y-5">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                                            <LayoutDashboard class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold">Statut general</h3>
                                            <p class="text-xs text-slate-400">
                                                Suivez en un coup d'oeil votre presence en ligne.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                    <div class="space-y-1">
                                        <p class="text-slate-400">Profil coach</p>
                                        <p class="text-sm font-semibold">{{ safeStats.profile_completion ?? 0 }}% complet</p>
                                        <p class="text-[11px] text-slate-500">
                                            Un profil complet inspire confiance a vos prospects.
                                        </p>
                                        <Link
                                            :href="route('dashboard.content')"
                                            class="inline-flex items-center gap-1 mt-1 text-[11px] text-purple-300 hover:text-purple-200"
                                        >
                                            Completer mon contenu
                                            <FileText class="h-3 w-3" />
                                        </Link>
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-slate-400">Site vitrine</p>
                                        <p class="text-sm font-semibold flex items-center gap-1">
                                            <span
                                                class="inline-block h-2.5 w-2.5 rounded-full"
                                                :class="safeStats.is_active ? 'bg-emerald-400 animate-breathe' : 'bg-rose-400'"
                                            ></span>
                                            {{ safeStats.is_active ? 'En ligne' : 'Hors ligne' }}
                                        </p>
                                        <p class="text-[11px] text-slate-500">
                                            Ge rez vos visuels et votre image de marque.
                                        </p>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <Link
                                                :href="route('dashboard.branding')"
                                                class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-2 py-1 text-[11px] border border-slate-700 hover:bg-slate-700"
                                            >
                                                <Palette class="h-3 w-3" />
                                                Branding
                                            </Link>
                                            <Link
                                                :href="route('dashboard.gallery')"
                                                class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-2 py-1 text-[11px] border border-slate-700 hover:bg-slate-700"
                                            >
                                                <ImageIcon class="h-3 w-3" />
                                                Galerie
                                            </Link>
                                        </div>
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-slate-400">Business</p>
                                        <p class="text-sm font-semibold">
                                            {{ safeStats.active_plans ?? 0 }} offre(s) active(s)
                                        </p>
                                        <p class="text-[11px] text-slate-500">
                                            Structurez vos offres et suivez vos clients.
                                        </p>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <Link
                                                :href="route('dashboard.plans')"
                                                class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-2 py-1 text-[11px] border border-slate-700 hover:bg-slate-700"
                                            >
                                                <CreditCard class="h-3 w-3" />
                                                Plans
                                            </Link>
                                            <Link
                                                :href="route('dashboard.clients.index')"
                                                class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-2 py-1 text-[11px] border border-slate-700 hover:bg-slate-700"
                                            >
                                                <Users class="h-3 w-3" />
                                                Clients
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subscription -->
                            <div class="rounded-2xl border border-emerald-500/40 bg-emerald-950/40 p-5 shadow-xl flex flex-col justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center text-emerald-950">
                                        <CreditCard class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-emerald-200">Abonnement</p>
                                        <p class="text-sm font-semibold text-emerald-50">
                                            <span v-if="safeStats.subscription?.is_on_trial">
                                                Periode d'essai en cours
                                            </span>
                                            <span v-else>
                                                {{ safeStats.subscription?.status === 'active' ? 'Abonnement actif' : 'Abonnement inactif' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <p class="text-[11px] text-emerald-100">
                                    Ge rez votre facturation et conservez votre tarif prefere.
                                </p>
                                <Link
                                    :href="route('dashboard.subscription')"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-500 px-3 py-2 text-xs font-semibold text-emerald-950 shadow-md hover:bg-emerald-400"
                                >
                                    Gerer mon abonnement
                                </Link>
                            </div>
                        </div>

                        <!-- Promotional Banners -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <!-- Formation FEA Banner -->
                            <a
                                href="https://fitnesseducation.academy/formations"
                                target="_blank"
                                class="group rounded-xl border border-slate-800/60 bg-gradient-to-br from-slate-900/40 to-slate-900/60 p-4 shadow-lg hover:border-blue-500/40 hover:shadow-xl transition-all"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                        <GraduationCap class="h-4 w-4 text-blue-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-xs font-semibold text-slate-200">Envie de se former ?</p>
                                            <ExternalLink class="h-3 w-3 text-slate-400 group-hover:text-blue-400 transition-colors" />
                                        </div>
                                        <p class="text-[11px] text-slate-400 leading-relaxed">
                                            La <span class="text-blue-300">Fitness Education Academy</span> propose des formations certifiantes EREPS 3 et EREPS 4 NASM.
                                        </p>
                                    </div>
                                </div>
                            </a>

                            <!-- Premium Services Banner -->
                            <div class="rounded-xl border border-slate-800/60 bg-gradient-to-br from-slate-900/40 to-slate-900/60 p-4 shadow-lg space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                                        <Sparkles class="h-4 w-4 text-purple-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-200 mb-1">Envie de plus de personnalisations ?</p>
                                        <p class="text-[11px] text-slate-400 leading-relaxed">
                                            Donnez plus de professionnalisme à votre présence en ligne.
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 pl-11">
                                    <!-- Custom Domain -->
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] text-slate-300 font-medium">Nom de domaine personnalisé</p>
                                            <p class="text-[10px] text-slate-500">65€ HTVA / an</p>
                                            <p
                                                v-if="hasCustomDomainOrder"
                                                class="text-[10px] text-emerald-300 font-semibold mt-1"
                                            >
                                                {{ customDomainStatus === 'active' ? 'Installé' : 'Acheté - en cours' }}
                                            </p>
                                        </div>
                                        <button
                                            v-if="!hasCustomDomainOrder"
                                            type="button"
                                            @click="openDomainModal"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-purple-500/20 border border-purple-500/40 px-3 py-1.5 text-[11px] font-medium text-purple-100 hover:bg-purple-500/30 hover:border-purple-500/60 transition-colors whitespace-nowrap"
                                        >
                                            <CreditCard class="h-3 w-3" />
                                            Acheter
                                        </button>
                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/40 px-3 py-1.5 text-[11px] font-semibold text-emerald-200 whitespace-nowrap"
                                        >
                                            Acheté
                                        </span>
                                    </div>
                                    
                                    <!-- Custom Services -->
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] text-slate-300 font-medium">Site web sur mesure, branding & logo, gestion des réseaux sociaux</p>
                                            <p class="text-[10px] text-slate-500">Devis personnalisé</p>
                                            <p
                                                v-if="hasCustomContactLock"
                                                class="text-[10px] text-emerald-300 font-semibold mt-1"
                                            >
                                                Demande envoyée - nous vous recontactons sous peu
                                            </p>
                                        </div>
                                        <button
                                            v-if="!hasCustomContactLock"
                                            type="button"
                                            @click="openContactModal"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-700/40 border border-slate-600/40 px-3 py-1.5 text-[11px] font-medium text-slate-100 hover:bg-slate-700/60 hover:border-slate-600/60 transition-colors whitespace-nowrap"
                                        >
                                            <Mail class="h-3 w-3" />
                                            Contact
                                        </button>
                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/40 px-3 py-1.5 text-[11px] font-semibold text-emerald-200 whitespace-nowrap"
                                        >
                                            Contacté
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent transformations -->
                        <div
                            v-if="recentTransformations && recentTransformations.length"
                            class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl space-y-4"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <ImageIcon class="h-4 w-4 text-slate-300" />
                                    <h3 class="text-sm font-semibold">Transformations recentes</h3>
                                </div>
                                <Link
                                    :href="route('dashboard.gallery')"
                                    class="text-[11px] text-purple-300 hover:text-purple-200 flex items-center gap-1"
                                >
                                    Ouvrir la galerie
                                </Link>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <article
                                    v-for="t in recentTransformations"
                                    :key="t.id"
                                    class="rounded-xl border border-slate-700/60 bg-slate-900/80 p-3 flex flex-col gap-2"
                                >
                                    <p class="text-[11px] text-slate-300 line-clamp-3">
                                        {{ t.description }}
                                    </p>
                                    <div class="flex gap-2 mt-auto">
                                        <div
                                            v-if="t.before_url"
                                            class="flex-1 h-16 rounded-lg bg-slate-800 overflow-hidden"
                                        >
                                            <img :src="t.before_url" alt="Avant" class="w-full h-full object-cover" />
                                        </div>
                                        <div
                                            v-if="t.after_url"
                                            class="flex-1 h-16 rounded-lg bg-slate-800 overflow-hidden"
                                        >
                                            <img :src="t.after_url" alt="Apres" class="w-full h-full object-cover" />
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Site vitrine -->
                    <div v-else-if="activeCategory === 'site'" class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                                    <Globe2 class="h-5 w-5 text-purple-300" />
                                    Site vitrine
                                </h2>
                                <p class="text-sm text-slate-400 mt-1">
                                    Tout ce qui concerne l'image et le contenu de votre site public.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <Link
                                :href="route('dashboard.branding')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-purple-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                                        <Palette class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Branding</h3>
                                        <p class="text-xs text-slate-400">
                                            Couleurs, logo, image hero, layout du site
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.content')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-emerald-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center shadow-lg">
                                        <FileText class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Contenu</h3>
                                        <p class="text-xs text-slate-400">
                                            Textes, CTA, reseaux sociaux, statistiques
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.gallery')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-indigo-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-400 flex items-center justify-center shadow-lg">
                                        <ImageIcon class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Galerie</h3>
                                        <p class="text-xs text-slate-400">
                                            Transformations avant / apres
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.faq')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-amber-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-400 flex items-center justify-center shadow-lg">
                                        <HelpCircle class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">FAQ</h3>
                                        <p class="text-xs text-slate-400">
                                            Questions / reponses frequentes
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Business -->
                    <div v-else-if="activeCategory === 'business'" class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                                    <CreditCard class="h-5 w-5 text-purple-300" />
                                    Business
                                </h2>
                                <p class="text-sm text-slate-400 mt-1">
                                    Offres, cadre legal, clients et opportunites.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <Link
                                :href="route('dashboard.plans')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-emerald-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center shadow-lg">
                                        <CreditCard class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Plans et offres</h3>
                                        <p class="text-xs text-slate-400">
                                            Creez et ajustez vos programmes de coaching.
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.legal')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-slate-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-slate-500 to-slate-400 flex items-center justify-center shadow-lg">
                                        <Scale class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Mentions legales</h3>
                                        <p class="text-xs text-slate-400">
                                            CGV, mentions legales, TVA et conformite.
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.clients.index')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-indigo-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-400 flex items-center justify-center shadow-lg">
                                        <Users class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Clients</h3>
                                        <p class="text-xs text-slate-400">
                                            Fiches clients, notes et suivi de progression.
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.contact')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-rose-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-rose-500 to-rose-400 flex items-center justify-center shadow-lg">
                                        <Mail class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Messages recus</h3>
                                        <p class="text-xs text-slate-400">
                                            Demandes entrantes via votre site public.
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Account -->
                    <div v-else-if="activeCategory === 'account'" class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                                    <User class="h-5 w-5 text-purple-300" />
                                    Compte
                                </h2>
                                <p class="text-sm text-slate-400 mt-1">
                                    Tout ce qui concerne votre abonnement, votre support et votre profil.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <Link
                                :href="route('dashboard.subscription')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-emerald-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center shadow-lg">
                                        <CreditCard class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Abonnement</h3>
                                        <p class="text-xs text-slate-400">
                                            Ge rez votre formule, vos paiements et votre facturation.
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('dashboard.support')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-sky-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 flex items-center justify-center shadow-lg">
                                        <LifeBuoy class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Support</h3>
                                        <p class="text-xs text-slate-400">
                                            Ouvrez un ticket et discutez avec l'equipe UNICOACH.
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="route('profile.edit')"
                                class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-xl hover:border-purple-500/60 hover:bg-slate-900/90 transition-colors flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                                        <User class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">Mon profil</h3>
                                        <p class="text-xs text-slate-400">
                                            Informations de connexion et donnees personnelles.
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <Toaster position="top-right" rich-colors />

        <!-- Modal: custom domain purchase -->
        <Modal :show="showDomainModal" @close="showDomainModal = false">
            <div class="p-6 bg-slate-900 text-slate-50">
                <h2 class="text-lg font-semibold mb-2">Nom de domaine souhaité</h2>
                <p class="text-xs text-slate-400 mb-4">
                    Indiquez le nom de domaine que vous aimeriez utiliser pour votre site.
                    Par exemple : <span class="text-purple-300">www.moncoaching.com</span>
                </p>

                <div class="space-y-2">
                    <label class="text-xs text-slate-300">Nom de domaine préféré</label>
                    <input
                        v-model="desiredDomain"
                        type="text"
                        placeholder="exemple : www.moncoaching.com"
                        class="mt-1 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-purple-500 focus:ring-purple-500"
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="showDomainModal = false"
                        class="inline-flex items-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        @click="submitDomainPurchase"
                        class="inline-flex items-center rounded-lg bg-purple-500 px-3 py-1.5 text-xs font-semibold text-slate-950 hover:bg-purple-400"
                    >
                        Continuer vers le paiement
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Modal: premium services contact -->
        <Modal :show="showContactModal" @close="showContactModal = false">
            <div class="p-6 bg-slate-900 text-slate-50">
                <h2 class="text-lg font-semibold mb-2">Demande de services premium</h2>
                <p class="text-xs text-slate-400 mb-4">
                    Sélectionnez les services qui vous intéressent et décrivez brièvement votre projet.
                </p>

                <div class="space-y-3 mb-4 text-xs">
                    <p class="font-medium text-slate-300">Services souhaités</p>
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            v-model="contactWebsite"
                            class="h-3.5 w-3.5 rounded border-slate-600 bg-slate-900"
                        />
                        <span>Site web</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            v-model="contactBranding"
                            class="h-3.5 w-3.5 rounded border-slate-600 bg-slate-900"
                        />
                        <span>Branding</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            v-model="contactSocial"
                            class="h-3.5 w-3.5 rounded border-slate-600 bg-slate-900"
                        />
                        <span>Réseaux sociaux</span>
                    </label>
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-medium text-slate-300">Votre message (optionnel)</p>
                    <textarea
                        v-model="contactMessage"
                        rows="4"
                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Expliquez brièvement votre besoin..."
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="showContactModal = false"
                        class="inline-flex items-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        @click="submitContactRequest"
                        class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-white"
                    >
                        Envoyer la demande
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
@keyframes breathe {
  0% {
    transform: scale(0.9);
    opacity: 0.8;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.45);
  }
  70% {
    transform: scale(1.4);
    opacity: 0.2;
    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
  }
  100% {
    transform: scale(0.9);
    opacity: 0.8;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
  }
}

.animate-breathe {
  animation: breathe 2.2s ease-in-out infinite;
}
</style>
