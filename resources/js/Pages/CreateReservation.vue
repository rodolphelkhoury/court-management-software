<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import CreateCustomerComponent from '@/Components/CreateCustomerComponent.vue';

const props = defineProps({
    court: {
        type: Object,
        required: true
    },
    sections: {
        type: Array,
        required: true
    },
    customers: {
        type: Array,
        required: true
    },
});

const sections = ref(props.sections);
const customers = ref(props.customers);
const isCustomerModalVisible = ref(false);

const form = useForm({
    section_id: null,
    customer_id: null,
    reservation_date: '',
    start_time: '',
    end_time: '',
    is_canceled: false,
    is_no_show: false,
});

const handleSubmit = () => {
    form.post(route('reservation.store', { court: props.court.id }), {
        onSuccess: () => {
            // Success handler
        },
        onError: (errors) => {
            alert(errors.message);
        }
    });
};

const customerCreated = () => {
    isCustomerModalVisible.value = false;
    axios.get('/customers')
        .then(response => {
            customers.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching customers:', error);
        });
};

</script>

<template>
    <Head title="Create Reservation" />

    <Modal :show="isCustomerModalVisible" @close="isCustomerModalVisible = false">
        <CreateCustomerComponent @customer-created="customerCreated()" />
    </Modal>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Create Reservation
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Book a new court reservation for {{ court.name }}
                    </p>
                </div>
                <Link :href="`/courts/${court.id}/reservations`"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold text-white">Reservation Details</h3>
                                <p class="text-indigo-100 text-sm mt-1">Fill in the information below to create a new booking</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <form @submit.prevent="handleSubmit" class="px-6 py-8 space-y-6">
                        
                        <!-- Customer Selection -->
                        <div class="space-y-2">
                            <label for="customer_id" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                Customer
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="flex gap-3">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <select id="customer_id" v-model="form.customer_id"
                                        class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all"
                                        required>
                                        <option disabled value="">Choose a customer</option>
                                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                            {{ customer.name }}
                                        </option>
                                    </select>
                                </div>
                                <button @click="isCustomerModalVisible = true" type="button"
                                    class="inline-flex items-center px-5 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    New
                                </button>
                            </div>
                        </div>

                        <!-- Reservation Date -->
                        <div class="space-y-2">
                            <label for="reservation_date" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                Reservation Date
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="reservation_date" v-model="form.reservation_date" type="date"
                                    class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all"
                                    required />
                            </div>
                        </div>

                        <!-- Time Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="start_time" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Start Time
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input id="start_time" v-model="form.start_time" type="time"
                                        class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all"
                                        required />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="end_time" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    End Time
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input id="end_time" v-model="form.end_time" type="time"
                                        class="block w-full pl-10 pr-3 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all"
                                        required />
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Reservation
                            </button>
                            <Link :href="`/courts/${court.id}/reservations`"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-all">
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
                        <p>Make sure to double-check the date and time before creating the reservation. You can always edit or cancel it later if needed.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>