<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import L from 'leaflet';

// Import Leaflet CSS
import 'leaflet/dist/leaflet.css';

// Fix for default marker icons
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';
import iconRetina from 'leaflet/dist/images/marker-icon-2x.png';

let DefaultIcon = L.icon({
    iconUrl: icon,
    iconRetinaUrl: iconRetina,
    shadowUrl: iconShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

L.Marker.prototype.options.icon = DefaultIcon;

const form = useForm({
    name: '',
    description: '',
    line1: '',
    line2: '',
    city: '',
    country: '',
    latitude: '',
    longitude: '',
});

const map = ref(null);
const marker = ref(null);
const mapContainer = ref(null);

onMounted(async () => {
    // Wait for DOM to be fully ready
    await nextTick();
    
    // Small delay to ensure container is rendered
    setTimeout(() => {
        initMap();
    }, 100);
});

const initMap = () => {
    if (!mapContainer.value) {
        console.error('Map container not found!');
        return;
    }

    try {
        // Default to Beirut, Lebanon
        const defaultLocation = [33.8938, 35.5018];
        
        // Initialize map
        map.value = L.map(mapContainer.value, {
            center: defaultLocation,
            zoom: 12,
            zoomControl: true,
            scrollWheelZoom: true,
        });

        console.log('Map initialized:', map.value);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 3,
        }).addTo(map.value);

        console.log('Tiles added to map');

        // Force map to refresh after a moment
        setTimeout(() => {
            if (map.value) {
                map.value.invalidateSize();
                console.log('Map size invalidated');
            }
        }, 200);

        // Add click listener
        map.value.on('click', (e) => {
            console.log('Map clicked:', e.latlng);
            placeMarker(e.latlng);
        });

        // Try to get user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLocation = [position.coords.latitude, position.coords.longitude];
                    map.value.setView(userLocation, 13);
                    console.log('User location set:', userLocation);
                },
                (error) => {
                    console.log('Geolocation error:', error);
                }
            );
        }
    } catch (error) {
        console.error('Error initializing map:', error);
    }
};

const placeMarker = (latlng) => {
    // Remove existing marker if any
    if (marker.value) {
        map.value.removeLayer(marker.value);
    }

    // Create new marker with a nice popup
    marker.value = L.marker(latlng, {
        draggable: true, // Allow users to drag the marker for fine-tuning
    }).addTo(map.value);

    marker.value.bindPopup(`
        <div style="text-align: center;">
            <strong>Selected Location</strong><br>
            <small>Drag marker to adjust</small>
        </div>
    `).openPopup();

    // Update form values
    form.latitude = latlng.lat.toFixed(6);
    form.longitude = latlng.lng.toFixed(6);

    // Update coordinates when marker is dragged
    marker.value.on('dragend', (e) => {
        const position = e.target.getLatLng();
        form.latitude = position.lat.toFixed(6);
        form.longitude = position.lng.toFixed(6);
    });
};

const handleSubmit = () => {
    form.post(route('complex.store'), {
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            console.log(errors);
        }
    });
};
</script>

<template>
    <Head title="Create Complex" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Create New Complex
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add a new sports complex to your management system
                    </p>
                </div>
                <Link 
                    href="/complexes" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
                    
                    <!-- Header Section -->
                    <div class="bg-indigo-600 px-6 py-8">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold text-white">Complex Details</h3>
                                <p class="text-indigo-100 text-sm mt-1">Fill in the information below to create a new sports complex</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <form @submit.prevent="handleSubmit" class="px-6 py-8 space-y-6">
                        
                        <!-- Basic Information Section -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                Complex Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <input 
                                    id="name" 
                                    v-model="form.name" 
                                    type="text"
                                    placeholder="Enter complex name (e.g., Downtown Sports Complex)"
                                    class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                    required 
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                Description
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </div>
                                <textarea 
                                    id="description" 
                                    v-model="form.description" 
                                    rows="4"
                                    placeholder="Provide a detailed description of the complex and its facilities..."
                                    class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all resize-none"
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="pt-4 space-y-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Address Information</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Provide the complete address of the complex</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="line1" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Address Line 1
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <input 
                                        id="line1" 
                                        v-model="form.line1" 
                                        type="text"
                                        placeholder="Street address, building number"
                                        class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                        required 
                                    />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="line2" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Address Line 2
                                    <span class="text-gray-500 text-xs font-normal ml-2">(Optional)</span>
                                </label>
                                <input 
                                    id="line2" 
                                    v-model="form.line2" 
                                    type="text"
                                    placeholder="Apartment, suite, unit, floor, etc."
                                    class="block w-full px-4 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="city" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                        City
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                            </svg>
                                        </div>
                                        <input 
                                            id="city" 
                                            v-model="form.city" 
                                            type="text"
                                            placeholder="City name"
                                            class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                            required 
                                        />
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="country" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                        Country
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <input 
                                            id="country" 
                                            v-model="form.country" 
                                            type="text"
                                            placeholder="Country name"
                                            class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                            required 
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Coordinates Section -->
                        <div class="pt-4 space-y-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Location on Map</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Click on the map to set the exact location</p>
                                </div>
                            </div>

                            <!-- Map Container -->
                            <div class="relative">
                                <div 
                                    ref="mapContainer"
                                    style="height: 400px; min-height: 400px; width: 100%; position: relative; z-index: 0;"
                                    class="rounded-xl border-2 border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700 shadow-inner"
                                ></div>
                                
                                <!-- Map overlay instructions -->
                                <div 
                                    v-if="!form.latitude || !form.longitude"
                                    class="absolute inset-0 flex items-center justify-center pointer-events-none"
                                >
                                    <div class="bg-white/95 dark:bg-gray-800/95 px-6 py-4 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 max-w-sm">
                                        <div class="flex items-start">
                                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Click anywhere on the map</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Select the precise location of your complex</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Coordinates Display -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-900 dark:text-white">
                                        Latitude
                                    </label>
                                    <div class="flex items-center px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl">
                                        <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 dark:text-white font-mono">
                                            {{ form.latitude || 'Click on map to set' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-900 dark:text-white">
                                        Longitude
                                    </label>
                                    <div class="flex items-center px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl">
                                        <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 dark:text-white font-mono">
                                            {{ form.longitude || 'Click on map to set' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Creating...' : 'Create Complex' }}
                            </button>
                            <Link 
                                href="/complexes"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-all"
                            >
                                Cancel
                            </Link>
                        </div>
                    </form>
                </div>

                <!-- Help Text -->
                <div class="mt-6 px-4">
                    <div class="flex items-start space-x-3 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <p>Make sure to set the location on the map for accurate navigation. You can drag the marker to fine-tune the position.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    </template>