<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { Link } from '@inertiajs/vue3';


</script>

<template>
    <HomeLayout>
        <div class="w-full">
            <div class="w-full relative h-64 mb-8 -mt-6 -mx-6 lg:-mx-8">
                <!-- Background Image -->
                <img 
                    src="/images/header-background-image.png" 
                    alt="About Header" 
                    class="absolute inset-0 w-full h-full object-cover"
                />
                <!-- Dark Overlay -->
                <div class="absolute inset-0 bg-black/60 dark:bg-black/70"></div>
                
                <!-- Content -->
                <div class="relative h-full flex flex-col items-center justify-center py-8">
                    <h2 class="text-3xl font-bold mb-4 text-white">
                        About us
                    </h2>
                    <nav class="flex justify-center items-center space-x-2">
                        <ol class="flex items-center space-x-2 text-sm text-gray-200">
                            <li>
                                <Link 
                                    :href="route('home')" 
                                    class="hover:text-white transition-colors duration-300"
                                >
                                    Home
                                </Link>
                            </li>
                            <li class="flex items-center space-x-2">
                                <span>/</span>
                                <span class="text-white">About us</span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Add your about page content -->

              <!-- About Content Section -->
              <div class="max-w-6xl mx-auto px-6 py-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                    <!-- Image Column -->
                    <div class="lg:col-span-5">
                        <img 
                            src="/images/about-us.jpg" 
                            alt="About Averette Salon" 
                            class="w-full h-auto rounded-lg shadow-lg"
                        >
                    </div>

                    <!-- Content Column -->
                    <div class="lg:col-span-7">
                        <div class="space-y-6">
                            <h1 class="text-4xl font-bold text-gray-900 dark:text-[#EDEDEC]">
                                <span class="text-gray-700 dark:text-gray-700">ABOUT </span>
                                <span class="text-red-500 dark:text-red-400">US</span>
                            </h1>
                            <h5 class="text-lg font-medium text-gray-600 dark:text-gray-400">
                                Experience the best in beauty and grooming at Averette Unisex Salon
                            </h5>
                            <div class="space-y-4 text-gray-700 dark:text-gray-300">
                                <p>
                                    At Averette Unisex Salon, we believe beauty is for everyone. Our mission is to provide 
                                    a relaxing, luxurious grooming experience that helps you look great and feel confident. 
                                    Whether you're after a fresh haircut, a new color, or a rejuvenating lash lift, we 
                                    tailor each service to suit your unique style and personality.
                                </p>
                                <p>
                                    Our team of skilled professionals is dedicated to giving you a personalized experience 
                                    that meets your needs. We focus on creating a welcoming, comfortable atmosphere where 
                                    you can unwind and enjoy top-notch service. No matter who you are, we're here to make 
                                    sure you leave feeling your best.
                                </p>
                                <p>
                                    At Averette, beauty is not just about looking good—it's about feeling good, too. Let us 
                                    help you express your individuality and boost your confidence every time you walk 
                                    through our doors.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>