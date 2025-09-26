<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    complex: {
        type: Object,
        required: true
    }
});

// Helper function to get image URL or fallback
const getImageUrl = (complex) => {
    if (complex.image && complex.image.filepath) {
        return complex.image.filepath;
    }
    return null;
};

// Helper function to format coordinates
const formatCoordinate = (coord, type) => {
    if (!coord) return 'N/A';
    const formatted = Math.abs(coord).toFixed(4);
    const direction = type === 'lat' 
        ? (coord >= 0 ? 'N' : 'S')
        : (coord >= 0 ? 'E' : 'W');
    return `${formatted}° ${direction}`;
};
</script>

<template>
    <Link :href="`/complexes/${complex.id}`" class="block">
        <div class="group relative bg-white dark:bg-gray-900 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-800 hover:border-indigo-300 dark:hover:border-indigo-600 transform hover:-translate-y-1">
            
            <!-- Image Section -->
            <div class="relative h-48 overflow-hidden bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
                <img 
                    v-if="getImageUrl(complex)" 
                    :src="getImageUrl(complex)" 
                    :alt="complex.name"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-white/80" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                
                <!-- Overlay gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                
                <!-- Status badge (if available) -->
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/90 text-gray-800 backdrop-blur-sm">
                        Active
                    </span>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-6">
                <!-- Title and Description -->
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                        {{ complex.name }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                        {{ complex.description || 'No description available' }}
                    </p>
                </div>

                <!-- Location Info -->
                <div class="space-y-3">
                    <!-- Address -->
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ complex.line1 }}
                            </p>
                            <p v-if="complex.line2" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ complex.line2 }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ complex.city }}, {{ complex.country }}
                            </p>
                        </div>
                    </div>

                    <!-- Coordinates -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ formatCoordinate(complex.latitude, 'lat') }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                                </svg>
                                <span>{{ formatCoordinate(complex.longitude, 'lng') }}</span>
                            </span>
                        </div>
                        
                        <!-- View details arrow -->
                        <div class="flex items-center text-indigo-600 dark:text-indigo-400 text-sm font-medium">
                            <span class="mr-1">View Details</span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hover effect overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/0 to-purple-500/0 group-hover:from-indigo-500/5 group-hover:to-purple-500/5 transition-all duration-300 pointer-events-none"></div>
        </div>
    </Link>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>