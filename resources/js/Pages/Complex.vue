<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import SearchComponent from '@/Components/SearchComponent.vue';

const props = defineProps({
    complex: {
        type: Object,
        required: true
    },
    courts: {
        type: Object, // pagination object
        required: false,
        default: () => ({ data: [] })
    },
    search: {
        type: String,
        required: false,
        default: ''
    }
});

// Helper to check if it's Previous/Next button
const isNavButton = (label) => {
    return label.includes('&laquo;') || label.includes('&raquo;');
};

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};
</script>

<template>
    <Head :title="complex.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Complex Details
                </h2>
                <Link :href="`/complexes/${complex.id}/edit`"
                    class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                    Edit Complex
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Complex Overview Card -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Image Section -->
                        <div class="lg:col-span-1">
                            <div class="h-64 lg:h-full bg-gradient-to-br from-indigo-500 to-purple-600 relative">
                                <img 
                                    v-if="complex.image?.filepath" 
                                    :src="complex.image.filepath" 
                                    :alt="complex.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="lg:col-span-2 p-6">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                {{ complex.name }}
                            </h1>
                            
                            <p v-if="complex.description" class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                {{ complex.description }}
                            </p>

                            <!-- Address Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Location
                                    </h3>
                                    <div class="space-y-2">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <div class="text-sm">
                                                <div v-if="complex.latitude && complex.longitude">
                                                    <a 
                                                        :href="`https://www.google.com/maps?q=${complex.latitude},${complex.longitude}`"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline transition-colors"
                                                    >
                                                        <p>{{ complex.line1 }}</p>
                                                        <p v-if="complex.line2">{{ complex.line2 }}</p>
                                                        <p>{{ complex.city }}, {{ complex.country }}</p>
                                                    </a>
                                                    <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        <span>Click to view on Google Maps</span>
                                                    </div>
                                                </div>
                                                <div v-else class="text-gray-700 dark:text-gray-300">
                                                    <p>{{ complex.line1 }}</p>
                                                    <p v-if="complex.line2">{{ complex.line2 }}</p>
                                                    <p>{{ complex.city }}, {{ complex.country }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courts Section -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <!-- Courts Section Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 w-full md:w-auto">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Courts
                        </h2>

                        <!-- Search Bar -->
                        <div class="w-full md:w-64">
                            <SearchComponent
                                placeholder="Search courts..."
                                :search-route="`/complexes/${complex.id}`"
                                :initial-query="search"
                            />
                        </div>
                    </div>

                    <!-- Add Court Button -->
                    <Link :href="`/complexes/${complex.id}/courts/create`"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors w-full md:w-auto justify-center md:justify-start"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Court
                    </Link>
                </div>


                    <!-- Courts Grid -->
                    <div v-if="courts.data && courts.data.length > 0" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="court in courts.data" :key="court.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                
                                <!-- Court Image -->
                                <div class="h-40 bg-gradient-to-br from-blue-500 to-indigo-600 relative">
                                    <img 
                                        v-if="court.image_url" 
                                        :src="court.image_url" 
                                        :alt="court.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Court Info -->
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ court.name }}
                                    </h3>
                                    
                                    <div class="space-y-2 text-sm mb-4">
                                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <span>{{ court.court_type_name }}</span>
                                        </div>
                                        
                                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                            </svg>
                                            <span>{{ court.surface_type_name }}</span>
                                        </div>
                                        
                                        <div v-if="court.hourly_rate" class="flex items-center text-indigo-600 dark:text-indigo-400 font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ formatCurrency(court.hourly_rate) }}/hour</span>
                                        </div>
                                    </div>

                                    <Link :href="`/courts/${court.id}`"
                                        class="block w-full text-center px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-600 dark:border-indigo-400 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                       <!-- Pagination - Show when there's more than 1 page -->
                        <div v-if="courts.last_page > 1" class="mt-8">
                            <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-0 sm:px-4">
                                <!-- Mobile: Compact Previous/Next -->
                                <div class="flex w-full justify-between -mt-px md:hidden pt-4">
                                    <Link
                                        v-if="courts.links[0]?.url"
                                        :href="courts.links[0].url"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-1">Previous</span>
                                    </Link>
                                    <span v-else class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 cursor-not-allowed">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-1">Previous</span>
                                    </span>

                                    <div class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Page
                                        <span class="mx-1 px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded font-semibold">
                                            {{ courts.current_page }}
                                        </span>
                                        of {{ courts.last_page }}
                                    </div>

                                    <Link
                                        v-if="courts.links[courts.links.length - 1]?.url"
                                        :href="courts.links[courts.links.length - 1].url"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    >
                                        <span class="mr-1">Next</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </Link>
                                    <span v-else class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 cursor-not-allowed">
                                        <span class="mr-1">Next</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>

                                <!-- Desktop: Full pagination -->
                                <div class="hidden md:flex md:flex-1 md:items-center md:justify-between -mt-px">
                                    <div class="flex w-0 flex-1">
                                        <Link
                                            v-if="courts.links[0]?.url"
                                            :href="courts.links[0].url"
                                            class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500 hover:border-indigo-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:border-indigo-400 transition-all"
                                        >
                                            <svg class="mr-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Previous
                                        </Link>
                                        <span v-else class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                                            <svg class="mr-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Previous
                                        </span>
                                    </div>

                                    <div class="hidden sm:flex -mt-px">
                                        <template v-for="(link, index) in courts.links" :key="index">
                                            <Link
                                                v-if="!isNavButton(link.label) && link.url"
                                                :href="link.url"
                                                class="inline-flex items-center border-t-2 px-4 pt-4 text-sm font-medium transition-all"
                                                :class="link.active 
                                                    ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' 
                                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600'"
                                            >
                                                {{ link.label }}
                                            </Link>
                                            <span
                                                v-else-if="!isNavButton(link.label) && !link.url"
                                                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-300 dark:text-gray-600"
                                            >
                                                {{ link.label }}
                                            </span>
                                        </template>
                                    </div>

                                    <div class="flex w-0 flex-1 justify-end">
                                        <Link
                                            v-if="courts.links[courts.links.length - 1]?.url"
                                            :href="courts.links[courts.links.length - 1].url"
                                            class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-indigo-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:border-indigo-400 transition-all"
                                        >
                                            Next
                                            <svg class="ml-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        <span v-else class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                                            Next
                                            <svg class="ml-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div v-else class="p-6 text-gray-500 dark:text-gray-400 text-center">
                        No courts found.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
