<script setup>
import CourtDataViewComponent from '@/Components/CourtDataViewComponent.vue';
import SearchComponent from '@/Components/SearchComponent.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    courts: {
        type: Array,
        required: true
    },
    search: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <Head title="Courts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <div class="flex space-x-8">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Courts
                    </h2>
                    <SearchComponent 
                        placeholder="Search courts..."
                        search-route="/courts"
                        :initial-query="search"
                    />
                </div>
                <div>
                    <Link 
                        :href="`/create-courts`" 
                        class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Add
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div style="display: flex; flex-wrap: wrap; gap: 16px;">
                    <CourtDataViewComponent v-for="court in courts" :key="court.id" :court="court" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>