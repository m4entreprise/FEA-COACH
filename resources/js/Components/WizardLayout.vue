<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    variant: {
        type: String,
        default: 'wizard',
    },
});

const progress = computed(() => (props.currentStep / props.totalSteps) * 100);

const isBeta = computed(() => props.variant === 'beta');

const steps = [
    { number: 1, title: 'Branding', icon: '🎨' },
    { number: 2, title: 'Images', icon: '📸' },
    { number: 3, title: 'Contenu', icon: '✍️' },
    { number: 4, title: 'Avancé', icon: '⚡' },
    { number: 5, title: 'Finalisation', icon: '🎉' },
];
</script>

<template>
    <div v-if="isBeta" class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
        <header class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    <span>U</span>
                </div>
                <div class="flex flex-col">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Panel coach</p>
                    <h1 class="text-base md:text-lg font-semibold">Configuration</h1>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-slate-100">
                    <span class="text-slate-400">Étape</span>
                    <span class="font-semibold text-slate-50">{{ currentStep }}/{{ totalSteps }}</span>
                </span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8">
            <div class="max-w-5xl mx-auto space-y-6">
                <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
                    <div class="flex justify-between items-center mb-2 text-xs text-slate-400">
                        <span>Progression</span>
                        <span class="font-medium text-purple-300">{{ currentStep }}/{{ totalSteps }}</span>
                    </div>
                    <div class="h-3 bg-slate-950/70 rounded-full overflow-hidden border border-slate-800">
                        <div
                            class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500 ease-out"
                            :style="{ width: progress + '%' }"
                        ></div>
                    </div>

                    <div class="hidden md:flex justify-between items-center relative mt-6">
                        <div class="absolute top-6 left-0 right-0 h-0.5 bg-slate-800"></div>
                        
                        <div
                            v-for="step in steps"
                            :key="step.number"
                            class="relative flex flex-col items-center"
                        >
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center text-xl mb-2 transition-all duration-300 relative z-10"
                                :class="[
                                    step.number <= currentStep
                                        ? 'bg-gradient-to-br from-purple-500 to-pink-500 shadow-lg shadow-purple-500/20'
                                        : 'bg-slate-950/60 border border-slate-800'
                                ]"
                            >
                                <span v-if="step.number < currentStep" class="text-white">✓</span>
                                <span v-else>{{ step.icon }}</span>
                            </div>
                            <span
                                class="text-xs font-medium transition-colors"
                                :class="step.number <= currentStep ? 'text-slate-50' : 'text-slate-500'"
                            >
                                {{ step.title }}
                            </span>
                        </div>
                    </div>

                    <div class="md:hidden flex justify-center space-x-2 mt-5">
                        <div
                            v-for="step in steps"
                            :key="step.number"
                            class="flex flex-col items-center"
                        >
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-lg transition-all duration-300"
                                :class="[
                                    step.number <= currentStep
                                        ? 'bg-gradient-to-br from-purple-500 to-pink-500'
                                        : 'bg-slate-950/60 border border-slate-800'
                                ]"
                            >
                                <span v-if="step.number < currentStep" class="text-white text-sm">✓</span>
                                <span v-else class="text-sm">{{ step.icon }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <div>
                    <slot />
                </div>
            </div>
        </main>
    </div>

    <div v-else class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 -left-40 w-96 h-96 bg-purple-600 rounded-full opacity-20 blur-3xl animate-pulse"></div>
            <div class="absolute top-1/2 -right-40 w-96 h-96 bg-pink-600 rounded-full opacity-20 blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-600 rounded-full opacity-10 blur-3xl animate-pulse delay-2000"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 px-4 py-8">
            <!-- Header -->
            <div class="max-w-5xl mx-auto mb-8 text-center">
                <div class="inline-flex items-center space-x-2 mb-6">
                    <div class="text-4xl font-bold text-white">
                        UNI<span class="text-purple-400">COACH</span>
                    </div>
                    <span class="px-3 py-1 bg-purple-500/20 border border-purple-400/30 rounded-full text-purple-300 text-sm font-medium">
                        Configuration
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Créons votre site de coaching
                </h1>
                <p class="text-lg text-gray-300">
                    Suivez les étapes pour personnaliser votre site et impressionner vos clients
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="max-w-5xl mx-auto mb-12">
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2 text-sm text-gray-400">
                        <span>Progression</span>
                        <span class="font-medium text-purple-400">{{ currentStep }}/{{ totalSteps }}</span>
                    </div>
                    <div class="h-3 bg-white/5 rounded-full overflow-hidden backdrop-blur-sm border border-white/10">
                        <div 
                            class="h-full bg-gradient-to-r from-purple-600 to-pink-600 rounded-full transition-all duration-500 ease-out relative overflow-hidden"
                            :style="{ width: progress + '%' }"
                        >
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <!-- Step Indicators -->
                <div class="hidden md:flex justify-between items-center relative">
                    <div class="absolute top-6 left-0 right-0 h-0.5 bg-white/10"></div>
                    
                    <div 
                        v-for="step in steps" 
                        :key="step.number"
                        class="relative flex flex-col items-center"
                    >
                        <div 
                            class="w-14 h-14 rounded-full flex items-center justify-center text-2xl mb-3 transition-all duration-300 relative z-10"
                            :class="[
                                step.number <= currentStep 
                                    ? 'bg-gradient-to-br from-purple-600 to-pink-600 shadow-lg shadow-purple-500/50' 
                                    : 'bg-white/5 border-2 border-white/10'
                            ]"
                        >
                            <span v-if="step.number < currentStep" class="text-white">✓</span>
                            <span v-else>{{ step.icon }}</span>
                        </div>
                        <span 
                            class="text-sm font-medium transition-colors"
                            :class="step.number <= currentStep ? 'text-white' : 'text-gray-500'"
                        >
                            {{ step.title }}
                        </span>
                    </div>
                </div>

                <!-- Mobile Step Indicator -->
                <div class="md:hidden flex justify-center space-x-2">
                    <div 
                        v-for="step in steps" 
                        :key="step.number"
                        class="flex flex-col items-center"
                    >
                        <div 
                            class="w-10 h-10 rounded-full flex items-center justify-center text-lg transition-all duration-300"
                            :class="[
                                step.number <= currentStep 
                                    ? 'bg-gradient-to-br from-purple-600 to-pink-600' 
                                    : 'bg-white/5 border border-white/10'
                            ]"
                        >
                            <span v-if="step.number < currentStep" class="text-white text-sm">✓</span>
                            <span v-else class="text-sm">{{ step.icon }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-5xl mx-auto">
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.delay-1000 {
    animation-delay: 1s;
}
.delay-2000 {
    animation-delay: 2s;
}
</style>
