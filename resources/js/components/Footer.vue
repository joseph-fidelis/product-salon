<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="w-full bg-[#FDFDFC] dark:bg-[#0a0a0a] border-t border-[#19140035] dark:border-[#3E3E3A]">
        <div class="max-w-6xl mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Address Block -->
                <div>
                    <h2 class="text-2xl font-semibold mb-4 dark:text-[#EDEDEC]">Salon Address</h2>
                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>51 Urua Ekpa Road, By Udo Usanga Street, Along University of Uyo, Uyo, Akwa Ibom State.</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-phone"></i>
                            <a href="tel:+2349064513941" class="hover:text-gray-900 dark:hover:text-[#EDEDEC]">
                                +234 906 451 3941
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:averette@gmail.com" class="hover:text-gray-900 dark:hover:text-[#EDEDEC]">
                                averette@gmail.com
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Social Feed -->
                <div>
                    <h2 class="text-2xl font-semibold mb-4 dark:text-[#EDEDEC]">Social Feed</h2>
                    <ul class="space-y-3">
                        <li>
                            <a href="https://www.facebook.com" 
                               class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-[#EDEDEC]">
                                <i class="fab fa-facebook"></i>
                                <span>Facebook</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com"
                               class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-[#EDEDEC]">
                                <i class="fab fa-linkedin"></i>
                                <span>LinkedIn</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com"
                               class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-[#EDEDEC]">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h2 class="text-2xl font-semibold mb-4 dark:text-[#EDEDEC]">Newsletters</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Enter your email address to receive new discount and other useful information right to your inbox.
                    </p>
                    <form class="flex gap-2">
                        <input 
                            type="email" 
                            placeholder="Enter your email address"
                            class="flex-1 rounded-sm border border-[#19140035] px-3 py-1.5 text-sm bg-transparent dark:border-[#3E3E3A] dark:text-[#EDEDEC]"
                        >
                        <button 
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-sm border border-[#19140035] px-4 py-1.5 text-sm hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Subscribe
                            <i class="fas fa-heart"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 pt-4 border-t border-[#19140035] dark:border-[#3E3E3A]">
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    <i class="far fa-copyright"></i> AVERETTE Salon Management System {{ currentYear }} | Created by
                    <span class="font-semibold">A6GNL</span>
                </p>
            </div>
        </div>
    </footer>
</template>