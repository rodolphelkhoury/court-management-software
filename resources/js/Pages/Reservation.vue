<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps({
    reservation: {
        type: Object,
        required: true,
    },
});

const reservation = ref(props.reservation)

const now = new Date();

const isReservationPast = computed(() => {
    const reservationDate = new Date(`${props.reservation.reservation_date}T${props.reservation.start_time}`);
    return reservationDate < now;
});

const markNoShow = () => {
    axios
        .put(`/reservations/${reservation.value.id}/update-is-no-show-status`, {
            is_no_show: !reservation.value.is_no_show
        })
        .then(response => {
            reservation.value.is_no_show = !reservation.value.is_no_show;
        })
        .catch(error => {
            console.error('Error updating no-show status:', error);
        });
};

const cancelReservation = () => {
    axios
        .put(`/reservations/${reservation.value.id}/update-is-canceled-status`, {
            is_canceled: !reservation.value.is_canceled
        })
        .then(response => {
            reservation.value.is_canceled = !reservation.value.is_canceled;
        })
        .catch(error => {
            console.error('Error canceling reservation:', error);
        });
};

const handleInvoiceClick = () => {
    router.visit(route('reservation.invoice', { reservation: reservation.value.id }));
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const formatTime = (timeString) => {
    const [hours, minutes] = timeString.split(':');
    const date = new Date();
    date.setHours(hours, minutes);
    return date.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
};

const statusColor = computed(() => {
    if (reservation.value.is_canceled) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    if (reservation.value.is_no_show) return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
});

const statusText = computed(() => {
    if (reservation.value.is_canceled) return 'Canceled';
    if (reservation.value.is_no_show) return 'No Show';
    return 'Active';
});

</script>

<template>
    <Head title="Reservation Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Reservation Details
                </h2>
                <div class="flex flex-wrap gap-3">
                    <!-- Button for No-Show or Attendance -->
                    <button 
                        v-if="isReservationPast" 
                        @click="markNoShow" 
                        type="button"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        {{ reservation.is_no_show ? "Mark Attendance" : "Flag as No Show" }}
                    </button>
                    <!-- Button for Cancel Reservation or Undo -->
                    <button 
                        v-else 
                        @click="cancelReservation" 
                        type="button"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        {{ reservation.is_canceled ? "Undo Cancellation" : "Cancel Reservation" }}
                    </button>
                    <!-- Invoice Button -->
                    <button 
                        @click="handleInvoiceClick" 
                        type="button"
                        class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                        Get Invoice
                    </button>
                </div>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Status Badge -->
                <div class="mb-6">
                    <span :class="statusColor" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                        {{ statusText }}
                    </span>
                </div>

                <!-- Main Card -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                    
                    <!-- Header Section -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-8">
                        <h2 class="text-3xl font-bold text-white mb-2">
                            {{ reservation.court.name }}
                        </h2>
                        <p v-if="reservation.section" class="text-indigo-100">
                            {{ reservation.section.name }}
                        </p>
                    </div>

                    <!-- Details Grid -->
                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Customer Information -->
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Customer</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ reservation.customer.name }}
                                </p>
                            </div>

                            <!-- Date -->
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ formatDate(reservation.reservation_date) }}
                                </p>
                            </div>

                            <!-- Time Range -->
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Time</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ formatTime(reservation.start_time) }} - {{ formatTime(reservation.end_time) }}
                                </p>
                            </div>

                            <!-- Total Price -->
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Price</p>
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    ${{ reservation.total_price }}
                                </p>
                            </div>

                        </div>

                        <!-- Status Information -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5" :class="reservation.is_canceled ? 'text-red-500' : 'text-green-500'" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="!reservation.is_canceled" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ reservation.is_canceled ? "Canceled" : "Active" }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Reservation Status</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5" :class="reservation.is_no_show ? 'text-orange-500' : 'text-green-500'" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="!reservation.is_no_show" fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            <path v-else fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" opacity="0.5"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ reservation.is_no_show ? "No Show" : "Attended" }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Attendance Status</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>