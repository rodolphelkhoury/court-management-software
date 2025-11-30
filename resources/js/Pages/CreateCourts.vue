<script setup> 
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    complex: {
        type: Object,
        required: true
    },
    court_types: {
        type: Array,
        required: true
    },
    surface_types: {
        type: Array,
        required: true
    },
});

const complex = ref(props.complex);
const surfaceTypes = ref(props.surface_types);
const courtTypes = ref(props.court_types);

const form = useForm({
    name: '',
    description: '',
    complex_id: props.complex.id,
    surface_type_id: null,
    court_type_id: null,
    hourly_rate: '',
    opening_time: '',
    closing_time: '',
    divisible: false,
    max_divisions: 0,
});

let selectedCourtTypeId = null;

watch(() => form.court_type_id, (newCourtTypeId) => {
    if (newCourtTypeId != selectedCourtTypeId) {
        const selectedCourtType = courtTypes.value ? courtTypes.value.find(c => c.id === newCourtTypeId) : null;
        surfaceTypes.value = selectedCourtType?.surface_types || [];
        selectedCourtTypeId = newCourtTypeId;
    }
});

const handleSubmit = () => {
    form.post(route('court.store'), {
        onSuccess: () => {
            // handle success
        },
        onError: (errors) => {
            console.log(errors);
        }
    });
};
</script>

<template>
    <Head title="Create Court" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Create New Court
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add a new court to {{ complex.name }}
                    </p>
                </div>
                <Link 
                    :href="`/complexes/${complex.id}`" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to {{ complex.name }}
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Card wrapper aligned with Create Complex -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
                    
                    <!-- Gradient header like Create Complex -->
                    <div class="bg-indigo-600 px-6 py-8">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 6h18M5 10h14M7 14h10M9 18h6"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold text-white">Court Information</h3>
                                <p class="text-indigo-100 text-sm mt-1">
                                    Fill in the details to create a new court in this complex
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <form @submit.prevent="handleSubmit" class="px-6 py-8 space-y-6">
                        <div class="space-y-6">
                            
                            <!-- Basic Information Section -->
                            <div class="space-y-5">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        Court Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        id="name" 
                                        v-model="form.name" 
                                        type="text"
                                        placeholder="Enter court name (e.g., Court 1, Center Court)"
                                        class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                        required 
                                    />
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        id="description" 
                                        v-model="form.description" 
                                        rows="4"
                                        placeholder="Provide a detailed description of the court..."
                                        class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all resize-none"
                                        required
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        Complex
                                    </label>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ complex.name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Court Specifications Section -->
                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-5">
                                    Court Specifications
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="court_type" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Court Type <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                id="court_type" 
                                                v-model="form.court_type_id"
                                                class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none cursor-pointer transition-all"
                                                required
                                            >
                                                <option disabled value="">Select Court Type</option>
                                                <option v-for="courtType in courtTypes" :key="courtType.id" :value="courtType.id">
                                                    {{ courtType.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="surface_type" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Surface Type <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select 
                                                id="surface_type" 
                                                v-model="form.surface_type_id"
                                                class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none cursor-pointer transition-all"
                                                required
                                            >
                                                <option disabled value="">Select Surface Type</option>
                                                <option v-for="surface in surfaceTypes" :key="surface.id" :value="surface.id">
                                                    {{ surface.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Schedule Section -->
                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-5">
                                    Pricing &amp; Operating Hours
                                </h4>
                                
                                <div class="space-y-5">
                                    <div>
                                        <label for="hourly_rate" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Hourly Rate ($) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                                            <input 
                                                id="hourly_rate" 
                                                v-model="form.hourly_rate" 
                                                type="number" 
                                                step="0.50" 
                                                min="0"
                                                placeholder="0.00"
                                                class="block w-full pl-8 pr-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                                required 
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="opening_time" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                                Opening Time <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                id="opening_time" 
                                                v-model="form.opening_time" 
                                                type="time"
                                                class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all"
                                                required 
                                            />
                                        </div>

                                        <div>
                                            <label for="closing_time" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                                Closing Time <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                id="closing_time" 
                                                v-model="form.closing_time" 
                                                type="time"
                                                class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all"
                                                required 
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Max Divisions (if divisible) -->
                            <div v-if="form.divisible" class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <label for="max_divisions" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    Maximum Divisions <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    id="max_divisions" 
                                    v-model="form.max_divisions" 
                                    type="number" 
                                    min="0"
                                    placeholder="Enter maximum divisions"
                                    class="block w-full px-4 py-3 text-base text-gray-900 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                    required 
                                />
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-end">
                            <Link 
                                :href="`/complexes/${complex.id}`" 
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-all"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Creating...' : 'Create Court' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
