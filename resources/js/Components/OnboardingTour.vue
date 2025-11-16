<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';

const props = defineProps({
    steps: {
        type: Array,
        required: true,
    },
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['complete', 'skip', 'close']);

const currentStepIndex = ref(0);
const tooltipPosition = ref({ top: '0px', left: '0px' });
const arrowPosition = ref('bottom');
const isVisible = ref(props.show);
const spotlightStyle = ref({});
const tooltipRef = ref(null);

const currentStep = computed(() => props.steps[currentStepIndex.value]);
const isFirstStep = computed(() => currentStepIndex.value === 0);
const isLastStep = computed(() => currentStepIndex.value === props.steps.length - 1);
const progress = computed(() => ((currentStepIndex.value + 1) / props.steps.length) * 100);

watch(() => props.show, (newValue) => {
    isVisible.value = newValue;
    if (newValue) {
        currentStepIndex.value = 0;
        nextTick(() => {
            positionTooltip();
        });
    }
});

watch(currentStepIndex, () => {
    nextTick(() => {
        positionTooltip();
        scrollToElement();
    });
});

// Update spotlight position on scroll
const handleScroll = () => {
    if (isVisible.value && currentStep.value?.target) {
        nextTick(() => {
            positionTooltip();
        });
    }
};

watch(isVisible, (newValue) => {
    if (newValue) {
        window.addEventListener('scroll', handleScroll, true);
    } else {
        window.removeEventListener('scroll', handleScroll, true);
    }
});

const positionTooltip = () => {
    if (!currentStep.value?.target) return;

    const targetElement = document.querySelector(currentStep.value.target);
    if (!targetElement) {
        console.warn(`Element not found: ${currentStep.value.target}`);
        return;
    }

    const rect = targetElement.getBoundingClientRect();
    const tooltipElement = tooltipRef.value;
    const tooltipWidth = tooltipElement?.offsetWidth || 400;
    const tooltipHeight = tooltipElement?.offsetHeight || 300;
    const padding = 20;

    // Highlight the target element with proper z-index
    const originalPosition = targetElement.style.position;
    const originalZIndex = targetElement.style.zIndex;
    
    // Store original values to restore later
    targetElement.dataset.originalPosition = originalPosition || '';
    targetElement.dataset.originalZIndex = originalZIndex || '';
    
    // Set high z-index to ensure visibility above overlay
    if (!originalPosition || originalPosition === 'static') {
        targetElement.style.position = 'relative';
    }
    targetElement.style.zIndex = '10001';
    
    // Create spotlight effect using fixed positioning (viewport coordinates)
    spotlightStyle.value = {
        top: `${rect.top}px`,
        left: `${rect.left}px`,
        width: `${rect.width}px`,
        height: `${rect.height}px`,
    };
    
    // Tooltip : position fixe en bas à droite pour garantir qu'elle reste visible.
    // On accepte qu'elle recouvre parfois la carte pour favoriser la lisibilité.
    const margin = 24;

    tooltipPosition.value = {
        bottom: `${margin}px`,
        right: `${margin}px`,
    };

    arrowPosition.value = 'none';
};

const scrollToElement = () => {
    if (!currentStep.value?.target) return;

    const targetElement = document.querySelector(currentStep.value.target);
    if (targetElement) {
        targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        });
    }
};

const nextStep = () => {
    if (isLastStep.value) {
        complete();
    } else {
        // Remove highlight from current element
        if (currentStep.value?.target) {
            const currentElement = document.querySelector(currentStep.value.target);
            if (currentElement) {
                // Restore original values
                const originalPosition = currentElement.dataset.originalPosition;
                const originalZIndex = currentElement.dataset.originalZIndex;
                
                if (originalPosition !== undefined) {
                    currentElement.style.position = originalPosition;
                    delete currentElement.dataset.originalPosition;
                }
                
                if (originalZIndex !== undefined) {
                    currentElement.style.zIndex = originalZIndex;
                    delete currentElement.dataset.originalZIndex;
                }
            }
        }
        currentStepIndex.value++;
    }
};

const previousStep = () => {
    if (!isFirstStep.value) {
        // Remove highlight from current element
        if (currentStep.value?.target) {
            const currentElement = document.querySelector(currentStep.value.target);
            if (currentElement) {
                // Restore original values
                const originalPosition = currentElement.dataset.originalPosition;
                const originalZIndex = currentElement.dataset.originalZIndex;
                
                if (originalPosition !== undefined) {
                    currentElement.style.position = originalPosition;
                    delete currentElement.dataset.originalPosition;
                }
                
                if (originalZIndex !== undefined) {
                    currentElement.style.zIndex = originalZIndex;
                    delete currentElement.dataset.originalZIndex;
                }
            }
        }
        currentStepIndex.value--;
    }
};

const skip = () => {
    cleanupHighlights();
    isVisible.value = false;
    emit('skip');
};

const complete = () => {
    cleanupHighlights();
    isVisible.value = false;
    emit('complete');
};

const close = () => {
    cleanupHighlights();
    isVisible.value = false;
    emit('close');
};

const cleanupHighlights = () => {
    props.steps.forEach(step => {
        if (step.target) {
            const element = document.querySelector(step.target);
            if (element) {
                // Restore original position and z-index
                const originalPosition = element.dataset.originalPosition;
                const originalZIndex = element.dataset.originalZIndex;
                
                if (originalPosition !== undefined) {
                    element.style.position = originalPosition;
                    delete element.dataset.originalPosition;
                }
                
                if (originalZIndex !== undefined) {
                    element.style.zIndex = originalZIndex;
                    delete element.dataset.originalZIndex;
                }
            }
        }
    });
};

onMounted(() => {
    if (isVisible.value) {
        nextTick(() => {
            positionTooltip();
        });
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="isVisible" class="fixed inset-0 z-[10000]">
            <!-- Blurred overlay with cutout for target element -->
            <div class="absolute inset-0 bg-black/70" style="pointer-events: none; backdrop-filter: blur(3px);"></div>

            <!-- Clear spotlight on target element - no glow, just shows the element clearly -->
            <div
                v-if="currentStep?.target"
                class="absolute rounded-xl transition-all duration-300 pointer-events-none"
                :style="{
                    ...spotlightStyle,
                    boxShadow: '0 0 0 9999px rgba(0, 0, 0, 0.7)',
                    backgroundColor: 'transparent',
                }"
            ></div>

            <!-- Tooltip -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
                mode="out-in"
            >
                <div
                    :key="currentStepIndex"
                    ref="tooltipRef"
                    class="absolute bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-2 border-purple-500/30 w-[400px] max-w-[90vw] max-h-[90vh] flex flex-col overflow-hidden z-[10002]"
                    :style="tooltipPosition"
                >
                    <!-- Arrow -->
                    <div
                        v-if="arrowPosition !== 'none'"
                        class="absolute w-4 h-4 bg-white dark:bg-gray-800 border-purple-500/30 rotate-45"
                        :class="{
                            'top-[-9px] left-1/2 -translate-x-1/2 border-t-2 border-l-2': arrowPosition === 'top',
                            'bottom-[-9px] left-1/2 -translate-x-1/2 border-b-2 border-r-2': arrowPosition === 'bottom',
                            'left-[-9px] top-1/2 -translate-y-1/2 border-l-2 border-b-2': arrowPosition === 'left',
                            'right-[-9px] top-1/2 -translate-y-1/2 border-r-2 border-t-2': arrowPosition === 'right',
                        }"
                    ></div>

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 rounded-t-2xl text-white">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-2xl">{{ currentStep.icon }}</span>
                                    <h3 class="text-xl font-bold">{{ currentStep.title }}</h3>
                                </div>
                                <p class="text-sm text-purple-100">
                                    Étape {{ currentStepIndex + 1 }} sur {{ steps.length }}
                                </p>
                            </div>
                            <button
                                @click="close"
                                class="text-white/80 hover:text-white hover:bg-white/20 rounded-full p-2 transition-colors flex-shrink-0"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-purple-400/30 backdrop-blur-sm">
                            <div
                                class="h-full bg-white shadow-lg transition-all duration-500 ease-out"
                                :style="{ width: progress + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Content (scrollable) -->
                    <div class="p-6 overflow-y-auto flex-1">
                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed" v-html="currentStep.content"></div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 rounded-b-2xl border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <button
                                v-if="!isFirstStep"
                                @click="previousStep"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Précédent
                            </button>
                            <button
                                v-if="isFirstStep"
                                @click="skip"
                                class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-semibold transition-colors"
                            >
                                Passer le tutoriel
                            </button>
                            
                            <button
                                @click="nextStep"
                                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 shadow-lg transform hover:scale-105 transition-all duration-200"
                            >
                                {{ isLastStep ? 'Terminer' : 'Suivant' }}
                                <svg v-if="!isLastStep" class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <svg v-else class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
