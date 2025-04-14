<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import HeroSection from '@/Components/HeroSection.vue';


const services = [
    {
        image: "/images/barbing.png",
        title: "Barbing",
        icon: "fa-scissors",
        description: "Fresh fades, clean lines, personalized cuts – from classic styles to trendy looks.",
        link: "service"
    },
    {
        image: "/images/weave.png",
        title: "Braiding and Hair Installation",
        icon: "fa-hand-peace-o",
        description: "Expert braiding and seamless hair installations for a flawless look.",
        link: "service-list2.php"
    },
    {
        image: "/images/lash.png",
        title: "Lash Extensions & Microblading",
        icon: "fa-hand-peace-o",
        description: "Elevate your beauty game with semi-permanent lashes and well-defined brows.",
        link: "service-list2.php"
    },
    {
        image: "/images/pedicure.png",
        title: "Pedicures & Manicures",
        icon: "fa-hand-peace-o",
        description: "Sit back, relax, and let our nail techs treat your hands and feet right.",
        link: "service-list2.php"
    },
    {
        image: "/images/tattoo.png",
        title: "Tattoo Art",
        icon: "fa-adjust",
        description: "Express yourself with clean, bold, professional tattoos by our in-house artists.",
        link: "service-list2.php"
    }
];
</script>

<template>
    <HomeLayout>
        <!-- Your welcome page content here -->
        <div class="w-full">
            <HeroSection></HeroSection>

            <!-- Why Choose US Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <h1 class="text-4xl font-bold text-center mb-12">
                    <span class="text-gray-700 dark:text-gray-300">WHY CHOOSE</span>
                    <span class="text-red-500 dark:text-red-400"> US!</span>
                </h1>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-red-500 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-cut text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Expert Stylists</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Our team of skilled professionals delivers exceptional service.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-red-500 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-spa text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Premium Services</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Top-quality treatments using the finest products.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-red-500 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-clock text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Convenient Hours</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Flexible scheduling to fit your busy lifestyle.
                        </p>
                    </div>
                </div>
            </div>

            <section class="services py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-7xl mx-auto px-6">
                    <h2 class="text-4xl font-bold text-center text-gray-900 dark:text-gray-100 mb-12">
                        Our Services
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                        <div v-for="(service, index) in services" :key="index"
                            class="service bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                            <img :src="service.image" :alt="service.title"
                                class="w-full h-60 object-cover rounded-md mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ service.title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                {{ service.description }}
                            </p>
                            <a :href="service.link"
                                class="btn px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                View More <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </HomeLayout>
</template>