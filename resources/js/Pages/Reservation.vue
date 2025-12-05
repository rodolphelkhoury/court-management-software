<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps({
    reservation: {
        type: Object,
        required: true,
    },
    invoice: {
        type: Object,
        required: false,
    }
});

const reservation = ref(props.reservation);
const invoice = ref(props.invoice);

const now = new Date();

/* ------------------------ RESERVATION LOGIC ------------------------ */

const isReservationPast = computed(() => {
    const reservationDate = new Date(`${reservation.value.reservation_date}T${reservation.value.start_time}`);
    return reservationDate < now;
});

const markNoShow = () => {
    axios
        .put(`/reservations/${reservation.value.id}/update-is-no-show-status`, {
            is_no_show: !reservation.value.is_no_show
        })
        .then(() => {
            reservation.value.is_no_show = !reservation.value.is_no_show;
        })
        .catch(err => console.error('Error updating no-show:', err));
};

const cancelReservation = () => {
    axios
        .put(`/reservations/${reservation.value.id}/update-is-canceled-status`, {
            is_canceled: !reservation.value.is_canceled
        })
        .then(() => {
            reservation.value.is_canceled = !reservation.value.is_canceled;
        })
        .catch(err => console.error('Error canceling reservation:', err));
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

/* --------------------------- INVOICE LOGIC -------------------------- */

const formatInvoiceDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return `${date.getMonth()+1}/${date.getDate()}/${date.getFullYear()}`;
};

const markAsPaid = () => {
    if (!invoice.value) return;

    const newStatus = invoice.value.status === 'paid' ? 'unpaid' : 'paid';

    axios
        .put(`/reservations/${invoice.value.reservation_id}/invoices/${invoice.value.id}/update-status`, {
            status: newStatus
        })
        .then(response => {
            invoice.value.status = response.data.status;
            invoice.value.paid_at = response.data.paid_at ? formatInvoiceDate(response.data.paid_at) : null;
        })
        .catch(error => console.error('Error updating invoice:', error));
};

const generatePDF = () => {
    if (!invoice.value) return;
    const url = `/reservations/${invoice.value.reservation_id}/invoices/${invoice.value.id}/generate-pdf`;
    window.open(url, '_blank');
};
</script>

<template>
    <Head title="Reservation Details" />

    <AuthenticatedLayout>
        <!-- HEADER -->
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Reservation Details
                </h2>

                <div class="flex flex-wrap gap-3">
                    <!-- No-Show -->
                    <button 
                        v-if="isReservationPast" 
                        @click="markNoShow" 
                        type="button"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-orange-700 transition">
                        {{ reservation.is_no_show ? "Mark as Attended" : "Flag as No Show" }}
                    </button>

                    <!-- Cancel -->
                    <!-- <button 
                        v-else 
                        @click="cancelReservation" 
                        type="button"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        {{ reservation.is_canceled ? "Undo Cancellation" : "Cancel Reservation" }}
                    </button> -->

                    <!-- Invoice Actions (if invoice exists) -->
                    <template v-if="invoice">
                        <button 
                            @click="markAsPaid"
                            :class="[
                                'px-4 py-2 text-sm font-semibold text-white rounded-lg transition',
                                invoice.status === 'paid'
                                    ? 'bg-gray-600 hover:bg-gray-700'
                                    : 'bg-green-600 hover:bg-green-700'
                            ]">
                            {{ invoice.status === 'paid' ? 'Mark as Unpaid' : 'Mark as Paid' }}
                        </button>

                        <button 
                            @click="generatePDF"
                            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                            Download PDF
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- STATUS BADGE -->
                <div class="mb-6">
                    <span :class="statusColor" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold">
                        {{ statusText }}
                    </span>
                </div>

                <!-- MAIN CARD - Combined Reservation & Invoice Info -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">

                    <!-- Header Section -->
                    <div class="bg-indigo-600 px-8 py-10">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                            <div>
                                <h1 class="text-4xl font-bold text-white mb-2">
                                    {{ reservation.court.name }}
                                </h1>
                                <p v-if="reservation.section" class="text-indigo-100 text-lg">
                                    {{ reservation.section.name }}
                                </p>
                            </div>
                            
                            <!-- Invoice Number if available -->
                            <div v-if="invoice" class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3">
                                <p class="text-indigo-100 text-xs uppercase tracking-wider">Invoice</p>
                                <p class="text-white font-bold text-lg">#{{ invoice.id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Grid -->
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                            <!-- Left Column: Reservation Details -->
                            <div class="lg:col-span-2 space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Reservation Information
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Date</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                                {{ formatDate(reservation.reservation_date) }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Time</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                                {{ formatTime(reservation.start_time) }} - {{ formatTime(reservation.end_time) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Information -->
                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Customer
                                    </h3>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ reservation.customer.name }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ invoice ? invoice.customer.customer.phone_number : reservation.customer.phone_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Financial Summary -->
                            <div class="lg:col-span-1">
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 space-y-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Payment Summary
                                    </h3>

                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Amount</p>
                                        <p class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">
                                            ${{ reservation.total_price }}
                                        </p>
                                    </div>

                                    <!-- Invoice Details (if exists) -->
                                    <template v-if="invoice">
                                        <div class="pt-4 border-t border-indigo-200 dark:border-indigo-800 space-y-4">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Payment Status</span>
                                                <span
                                                    :class="[
                                                        'px-3 py-1 text-xs font-semibold rounded-full',
                                                        invoice.status === 'paid'
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                    ]"
                                                >
                                                    {{ invoice.status.toUpperCase() }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Due Date</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ formatInvoiceDate(invoice.due_date) }}
                                                </span>
                                            </div>

                                            <div v-if="invoice.paid_at" class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Paid On</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ invoice.paid_at }}
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>