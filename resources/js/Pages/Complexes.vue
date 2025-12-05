<script setup>
import ComplexComponent from '@/Components/ComplexComponent.vue';
import SearchComponent from '@/Components/SearchComponent.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    complexes: {
        type: Object, // pagination object
        required: true
    },
    search: {
        type: String,
        default: ''
    }
});

// Helper to check if it's Previous/Next button
const isNavButton = (label) => {
    return label.includes('&laquo;') || label.includes('&raquo;');
};
</script>

<template>
    <Head title="Complexes" />

    <AuthenticatedLayout>
        <!-- Header -->
        <template #header>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
                <!-- Title + Search -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-3 sm:space-y-0 w-full sm:w-auto">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Complexes
                    </h2>
                    
                    <!-- Search bar -->
                    <div class="w-full sm:w-64">
                        <SearchComponent 
                            placeholder="Search complexes..."
                            search-route="/complexes"
                            :initial-query="search"
                        />
                    </div>
                </div>

                <!-- Add Complex Button -->
                <div class="w-full sm:w-auto flex justify-center sm:justify-start">
                    <Link 
                        :href="`/create-complexes`" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors whitespace-nowrap"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Complex
                    </Link>
                </div>
            </div>
        </template>

        <!-- Content -->
        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Complex list -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <ComplexComponent 
                        v-for="complex in complexes.data" 
                        :key="complex.id" 
                        :complex="complex" 
                    />
                </div>

                <!-- Enhanced Pagination -->
                <div class="mt-8 sm:mt-12">
                    <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-0 sm:px-4">
                        <!-- Mobile: Compact Previous/Next -->
                        <div class="flex w-full justify-between -mt-px md:hidden pt-4">
                            <Link
                                v-if="complexes.links && complexes.links[0]?.url"
                                :href="complexes.links[0].url"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-1">Previous</span>
                            </Link>
                            <span
                                v-else
                                class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 cursor-not-allowed"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-1">Previous</span>
                            </span>

                            <!-- Page indicator on mobile -->
                            <div class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <template v-for="(link, index) in complexes.links" :key="index">
                                    <span v-if="link.active && !isNavButton(link.label)" class="flex items-center">
                                        Page
                                        <span class="mx-1 px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded font-semibold">
                                            {{ link.label }}
                                        </span>
                                    </span>
                                </template>
                            </div>

                            <Link
                                v-if="complexes.links && complexes.links[complexes.links.length - 1]?.url"
                                :href="complexes.links[complexes.links.length - 1].url"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                <span class="mr-1">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                            <span
                                v-else
                                class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 cursor-not-allowed"
                            >
                                <span class="mr-1">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>

                        <!-- Desktop: Full pagination -->
                        <div class="hidden md:flex md:flex-1 md:items-center md:justify-between -mt-px">
                            <!-- Previous Button -->
                            <div class="flex w-0 flex-1">
                                <Link
                                    v-if="complexes.links && complexes.links[0]?.url"
                                    :href="complexes.links[0].url"
                                    class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500 hover:border-indigo-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:border-indigo-400 transition-all"
                                >
                                    <svg class="mr-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Previous
                                </Link>
                                <span
                                    v-else
                                    class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed"
                                >
                                    <svg class="mr-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Previous
                                </span>
                            </div>

                            <!-- Page Numbers -->
                            <div class="hidden sm:flex -mt-px">
                                <template v-for="(link, index) in complexes.links" :key="index">
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

                            <!-- Next Button -->
                            <div class="flex w-0 flex-1 justify-end">
                                <Link
                                    v-if="complexes.links && complexes.links[complexes.links.length - 1]?.url"
                                    :href="complexes.links[complexes.links.length - 1].url"
                                    class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-indigo-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:border-indigo-400 transition-all"
                                >
                                    Next
                                    <svg class="ml-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                                <span
                                    v-else
                                    class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed"
                                >
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
        </div>
    </AuthenticatedLayout>
</template>