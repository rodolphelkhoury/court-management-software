<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Search...'
    },
    searchRoute: {
        type: String,
        required: true
    },
    initialQuery: {
        type: String,
        default: ''
    },
    debounceMs: {
        type: Number,
        default: 500
    }
});

const searchQuery = ref(props.initialQuery);
let debounceTimeout = null;

watch(searchQuery, (newValue) => {
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }

    debounceTimeout = setTimeout(() => {
        performSearch(newValue);
    }, props.debounceMs);
});

const performSearch = (query) => {
    router.get(
        props.searchRoute,
        { search: query },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
};

const clearSearch = () => {
    searchQuery.value = '';
};
</script>

<template>
    <div class="relative">
        <input
            v-model="searchQuery"
            type="text"
            :placeholder="placeholder"
            class="w-64 px-4 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        />
        <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            type="button"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>