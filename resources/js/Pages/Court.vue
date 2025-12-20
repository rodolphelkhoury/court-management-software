<script setup>
import { ScheduleXCalendar } from '@schedule-x/vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  createCalendar,
  createViewDay,
  createViewMonthAgenda,
  createViewMonthGrid,
  createViewWeek,
} from '@schedule-x/calendar'
import '@schedule-x/theme-default/dist/index.css'
import { createDragAndDropPlugin } from '@schedule-x/drag-and-drop'
import { createResizePlugin } from '@schedule-x/resize'
import { Head, Link, router } from '@inertiajs/vue3';
import { createEventsServicePlugin } from '@schedule-x/events-service'

const props = defineProps({
    court: {
        type: Object,
        required: true
    },
    reservations: {
        type: Array,
        required: true
    },
});

const eventsServicePlugin = createEventsServicePlugin();
const viewWeek = createViewWeek({ cellDuration: 60 });
const viewMonthGrid = createViewMonthGrid();
const viewDay = createViewDay();

const getCurrentDate = () => {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const calendarApp = createCalendar({
  selectedDate: getCurrentDate(),
  views: [
    viewDay,
    viewWeek,
    viewMonthGrid,
    createViewMonthAgenda(),
  ],
  defaultView: viewWeek.name,
  events: [],
  callbacks: {
    onEventUpdate(event) {
      updateEvent(event);
    },
    onEventClick(event) {
      clickOnEvent(event);
    }
  },
}, [
  createDragAndDropPlugin(),
  createResizePlugin(),
  eventsServicePlugin,
]);

const transformReservationsToScheduleEvents = (reservations) => {
  return reservations.map((reservation) => {
    const formatTime = (time) => time.substring(0, 5);

    let titre;
    if (reservation.section) {
      titre = `Reservation for ${reservation.customer.name} - ${reservation.section.name}`;
    } else {
      titre = `Reservation for ${reservation.customer.name}`;
    }

    return {
      id: reservation.id,
      title: titre,
      start: `${reservation.reservation_date} ${formatTime(reservation.start_time)}`,
      end: `${reservation.reservation_date} ${formatTime(reservation.end_time)}`,
      customer_id: reservation.customer.id,
      section_id: reservation.section_id,
      reservation_date: reservation.reservation_date,
      start_time: reservation.start_time,
      end_time: reservation.end_time,
      is_canceled: reservation.is_canceled,
      court_id: reservation.court_id
    };
  });
};

const transformEventToReservation = (event) => {
  const extractTime = (datetime) => datetime.split(' ');

  return {
    id: event.id,
    customer: {
      name: event.title.replace('Reservation for ', ''),
    },
    reservation_date: extractTime(event.start)[0],
    start_time: extractTime(event.start)[1],
    end_time: extractTime(event.end)[1],
    customer_id: event.customer_id,
    section_id: event.section_id,
    is_canceled: event.is_canceled,
    court_id: event.court_id
  };
};

const addEvents = (reservations) => {
  const events = transformReservationsToScheduleEvents(reservations);
  events.forEach(event => {
    eventsServicePlugin.add(event);
  });
};

const updateEvent = (event) => {
  const updatedReservation = transformEventToReservation(event);
  axios
    .put(`/courts/${event.court_id}/reservations/${updatedReservation.id}`, updatedReservation)
    .then((response) => {
      // Optional: Provide feedback to the user
    })
    .catch((error) => {
      alert(error.response.data.message);
      const allEvents = eventsServicePlugin.getAll();
      allEvents.forEach(existingEvent => {
        eventsServicePlugin.remove(existingEvent.id);
      });
      router.reload({ only: ['reservations'] })
      addEvents(props.reservations);
    });
};

const clickOnEvent = (event) => {
  router.visit(route('get.reservation', {reservation: event.id}))
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

const formatTime = (time) => {
    if (!time) return 'N/A';
    return time.substring(0, 5);
};

addEvents(props.reservations);
</script>

<template>
    <Head :title="court.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Court Details
                </h2>
                <div class="flex gap-2">
                    <!-- <Link :href="`/courts/${court.id}/edit`"
                        class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        Edit Court
                    </Link> -->
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Court Overview Card -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Image Section -->
                        <div class="lg:col-span-1">
                            <div class="h-64 lg:h-full bg-gradient-to-br from-blue-500 to-indigo-600 relative">
                                <img 
                                    v-if="court.image_url" 
                                    :src="court.image_url" 
                                    :alt="court.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="lg:col-span-2 p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ court.name }}
                                </h1>
                                <span v-if="court.hourly_rate" class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-semibold">
                                    {{ formatCurrency(court.hourly_rate) }}/hr
                                </span>
                            </div>
                            
                            <p v-if="court.description" class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                {{ court.description }}
                            </p>

                            <!-- Court Information Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Info -->
                                <div class="space-y-3">
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Court Information
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                <span class="text-gray-500 dark:text-gray-400">Type:</span> {{ court.court_type_name }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                <span class="text-gray-500 dark:text-gray-400">Surface:</span> {{ court.surface_type_name }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                <span class="text-gray-500 dark:text-gray-400">Duration:</span> {{ court.reservation_duration }} minutes
                                            </span>
                                        </div>
                                        <div v-if="court.divisible" class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                <span class="text-gray-500 dark:text-gray-400">Divisible:</span> Up to {{ court.max_divisions }} sections
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Operating Hours & Location -->
                                <div class="space-y-3">
                                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Schedule & Location
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                <span class="text-gray-500 dark:text-gray-400">Hours:</span> {{ formatTime(court.opening_time) }} - {{ formatTime(court.closing_time) }}
                                            </span>
                                        </div>
                                        <div v-if="court.address_line" class="flex items-start">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-gray-300">{{ court.address_line }}</span>
                                        </div>
                                        <div v-if="court.latitude && court.longitude" class="flex items-start">
                                            <svg class="w-4 h-4 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 013.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                            </svg>
                                            <a 
                                                :href="`https://www.google.com/maps?q=${court.latitude},${court.longitude}`"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline transition-colors inline-flex items-center gap-1"
                                            >
                                                View on Google Maps
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sections -->
                            <div v-if="court.sections && court.sections.length > 0" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                    Sections
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="section in court.sections" 
                                        :key="section.id"
                                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm"
                                    >
                                        {{ section.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservations Calendar Section -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Reservations Calendar
                        </h2>
                        <Link 
                            :href="`/courts/${court.id}/create-reservations`" 
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Reservation
                        </Link>
                    </div>

                    <div class="p-4">
                        <ScheduleXCalendar :calendar-app="calendarApp" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.sx-vue-calendar-wrapper {
  height: 800px;
  max-height: 90vh;
}
</style>