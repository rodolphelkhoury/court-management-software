<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3';

// Carousel
const currentSlide = ref(0)
const carouselItems = ref([
  { id: 1, title: 'Real-time Reservations', description: 'Manage bookings instantly with our intuitive interface', image: 'https://images.unsplash.com/photo-1460661419862-00a94e3a3ebc?w=600&h=400&fit=crop' },
  { id: 2, title: 'Customer Management', description: 'Keep track of all your customers and their preferences', image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop' },
  { id: 3, title: 'Analytics & Reporting', description: 'Get insights into your business performance', image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop' },
  { id: 4, title: 'Multi-Complex Support', description: 'Manage multiple court complexes from one platform', image: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&h=400&fit=crop' }
])

// Features
const features = ref([
  {
    id: 1,
    icon: '📅',
    title: 'Easy Scheduling',
    description: 'Schedule court sessions with a simple drag-and-drop interface. Manage multiple courts effortlessly.'
  },
  {
    id: 2,
    icon: '👥',
    title: 'Customer Management',
    description: 'Track customer details, preferences, and payment history all in one place.'
  },
  {
    id: 3,
    icon: '💰',
    title: 'Invoicing & Billing',
    description: 'Generate invoices automatically and manage payments with integrated billing system.'
  },
  {
    id: 4,
    icon: '📊',
    title: 'Analytics',
    description: 'Get detailed insights into your business with comprehensive reporting tools.'
  },
  {
    id: 5,
    icon: '🔒',
    title: 'Secure & Reliable',
    description: 'Enterprise-grade security with 99.9% uptime guarantee.'
  },
  {
    id: 6,
    icon: '📱',
    title: 'Mobile Ready',
    description: 'Manage your courts on the go with our responsive mobile app.'
  }
])

// How it works steps
const steps = ref([
  {
    id: 1,
    title: 'Sign Up',
    description: 'Create your account and set up your court complex in minutes'
  },
  {
    id: 2,
    title: 'Add Courts',
    description: 'Add all your courts and configure their details'
  },
  {
    id: 3,
    title: 'Accept Bookings',
    description: 'Start accepting reservations from your customers'
  },
  {
    id: 4,
    title: 'Grow Your Business',
    description: 'Use analytics to optimize your operations and increase revenue'
  }
])

// Team members
const teamMembers = ref([
  { id: 1, name: 'John Smith', role: 'CEO & Founder' },
  { id: 2, name: 'Sarah Johnson', role: 'CTO & Co-founder' },
  { id: 3, name: 'Mike Chen', role: 'Product Lead' },
  { id: 4, name: 'Emma Davis', role: 'Head of Customer Success' }
])

// Contact Form with useForm
const form = useForm({
  name: '',
  email: '',
  subject: '',
  message: ''
})

// Carousel autoplay
let carouselInterval = null
onMounted(() => {
  carouselInterval = setInterval(() => nextSlide(), 5000)
})
onUnmounted(() => carouselInterval && clearInterval(carouselInterval))

// Carousel controls
const nextSlide = () => currentSlide.value = (currentSlide.value + 1) % carouselItems.value.length
const prevSlide = () => currentSlide.value = (currentSlide.value - 1 + carouselItems.value.length) % carouselItems.value.length

// Smooth scroll
const scrollToSection = sectionId => {
  const el = document.getElementById(sectionId)
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}

// Submit form
const submitForm = () => {
  form.post(route('contact.send'), {
    onSuccess: () => form.reset(),
    onError: () => console.log('Validation errors:', form.errors)
  })
}
</script>
<template>
  <div class="min-h-screen bg-white">
    <!-- Navigation Bar -->
    <nav class="fixed w-full bg-white shadow-md z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">CM</span>
            </div>
            <span class="text-xl font-bold text-black">CourtConnect</span>
          </div>
          <div class="hidden md:flex space-x-8">
            <a href="#features" class="text-black hover:text-indigo-600 transition">Features</a>
            <a href="#how-it-works" class="text-black hover:text-indigo-600 transition">How It Works</a>
            <a href="#about" class="text-black hover:text-indigo-600 transition">About Us</a>
            <a href="#sitemap" class="text-black hover:text-indigo-600 transition">Sitemap</a>
          </div>
          <div class="flex space-x-4">
            <button @click="scrollToSection('contact')" class="px-4 py-2 text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 transition">
              Contact
            </button>
            <a href="/login" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
              Sign In
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section with Carousel -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-indigo-50 to-indigo-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div class="space-y-8">
            <div>
              <h1 class="text-5xl md:text-6xl font-bold text-black leading-tight">
                Manage Your Courts with <span class="text-indigo-600">Ease</span>
              </h1>
              <p class="mt-6 text-xl text-gray-700 leading-relaxed">
                A comprehensive SaaS platform for court reservation management, customer administration, and business analytics. Streamline your operations today.
              </p>
            </div>
            <div class="flex gap-4">
              <button class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105">
                Get Started Free
              </button>
              <button class="px-8 py-3 border-2 border-indigo-600 text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition">
                Watch Demo
              </button>
            </div>
          </div>

          <!-- Carousel -->
          <div class="relative">
            <div class="relative h-96 bg-gray-200 rounded-lg overflow-hidden shadow-lg">
              <img 
                :src="carouselItems[currentSlide].image"
                :alt="carouselItems[currentSlide].title"
                class="w-full h-full object-cover"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end">
                <div class="p-6 text-white">
                  <h3 class="text-2xl font-bold">{{ carouselItems[currentSlide].title }}</h3>
                  <p class="text-sm mt-2">{{ carouselItems[currentSlide].description }}</p>
                </div>
              </div>
              
              <!-- Navigation Buttons -->
              <button 
                @click="prevSlide" 
                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-black rounded-full p-2 shadow-lg transition"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button 
                @click="nextSlide" 
                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-black rounded-full p-2 shadow-lg transition"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
              
              <!-- Indicators -->
              <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                <button
                  v-for="(item, index) in carouselItems"
                  :key="item.id"
                  @click="currentSlide = index"
                  :class="[ 'w-2 h-2 rounded-full transition', currentSlide === index ? 'bg-white w-8' : 'bg-white/50' ]"
                ></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-black">Powerful Features</h2>
          <p class="mt-4 text-xl text-gray-700">Everything you need to manage your court business efficiently</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="feature in features" :key="feature.id" class="p-8 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-4xl mb-4">{{ feature.icon }}</div>
            <h3 class="text-2xl font-bold text-black mb-3">{{ feature.title }}</h3>
            <p class="text-gray-700">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-black">How It Works</h2>
          <p class="mt-4 text-xl text-gray-700">Simple steps to get started with CourtConnect</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div v-for="step in steps" :key="step.id" class="relative">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold shadow-lg">
                {{ step.id }}
              </div>
              <h3 class="mt-4 text-lg font-bold text-black text-center">{{ step.title }}</h3>
              <p class="mt-2 text-gray-700 text-center text-sm">{{ step.description }}</p>
            </div>
            <div v-if="step.id < 4" class="hidden md:block absolute top-8 left-full w-12 h-1 bg-indigo-300 -z-10"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div>
            <img 
              src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=500&fit=crop"
              alt="Team collaboration"
              class="rounded-lg shadow-lg w-full"
            />
          </div>
          <div class="space-y-6">
            <h2 class="text-4xl font-bold text-black">About Us</h2>
            <p class="text-gray-700 leading-relaxed">
              CourtConnect was founded by a team of passionate sports enthusiasts and software engineers who recognized the need for a modern, efficient court management solution. We've been serving court operators for over a decade.
            </p>
            <p class="text-gray-700 leading-relaxed">
              Our mission is to simplify court management, allowing owners and operators to focus on providing excellent service to their customers. We're committed to innovation, reliability, and customer success.
            </p>
            
            <div class="space-y-4 mt-8">
              <h3 class="text-2xl font-bold text-black">Our Team</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="member in teamMembers" :key="member.id" class="p-4 bg-indigo-50 rounded-lg">
                  <p class="font-bold text-black">{{ member.name }}</p>
                  <p class="text-sm text-gray-700">{{ member.role }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-black">Get In Touch</h2>
          <p class="mt-4 text-xl text-gray-700">We'd love to hear from you. Send us a message!</p>
        </div>

        <div class="bg-gray-50 p-8 rounded-lg shadow-md">
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-black mb-2">Name</label>
                <input v-model="form.name" type="text" class="input-field" required />
                <span v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</span>
              </div>
              <div>
                <label class="block text-sm font-semibold text-black mb-2">Email</label>
                <input v-model="form.email" type="email" class="input-field" required />
                <span v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email }}</span>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-black mb-2">Subject</label>
              <input v-model="form.subject" type="text" class="input-field" required />
              <span v-if="form.errors.subject" class="text-red-600 text-sm">{{ form.errors.subject }}</span>
            </div>

            <div>
              <label class="block text-sm font-semibold text-black mb-2">Message</label>
              <textarea v-model="form.message" rows="5" class="input-field" required></textarea>
              <span v-if="form.errors.message" class="text-red-600 text-sm">{{ form.errors.message }}</span>
            </div>

            <button 
              type="submit" 
              class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Sending...' : 'Send Message' }}
            </button>
          </form>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-gray-300 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
          <div>
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">CM</span>
              </div>
              <span class="text-lg font-bold text-white">CourtConnect</span>
            </div>
            <p class="text-sm">Manage your courts with ease and efficiency.</p>
          </div>
          <div>
            <h4 class="text-white font-bold mb-4">Quick Links</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#features" class="hover:text-indigo-400 transition">Features</a></li>
              <li><a href="#about" class="hover:text-indigo-400 transition">About</a></li>
              <li><a href="#contact" class="hover:text-indigo-400 transition">Contact</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-bold mb-4">Follow Us</h4>
            <div class="flex space-x-4">
              <a href="#" class="hover:text-indigo-400 transition">Twitter</a>
              <a href="#" class="hover:text-indigo-400 transition">LinkedIn</a>
              <a href="#" class="hover:text-indigo-400 transition">Facebook</a>
            </div>
          </div>
        </div>
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
          <p>&copy; 2025 CourtConnect. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Button hover effects */
button {
  transition: all 0.3s ease;
}

button:hover {
  transform: translateY(-2px);
}

/* Form inputs */
.input-field {
  width: 100%;
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  outline: none;
}
.input-field:focus {
  border-color: #4f46e5;
}
</style>
