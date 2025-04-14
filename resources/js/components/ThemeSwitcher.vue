<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

interface Props {
    class?: string;
}

const { class: containerClass = '' } = defineProps<Props>();

const { appearance, updateAppearance } = useAppearance();

// Toggle between light and dark modes
const toggleAppearance = () => {
    updateAppearance(appearance === 'light' ? 'dark' : 'light');
};
</script>

<template>
    <div :class="['inline-flex items-center gap-2', containerClass]">
        <button
            @click="toggleAppearance"
            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-neutral-100 dark:bg-neutral-800 transition-colors hover:bg-neutral-200 dark:hover:bg-neutral-700"
        >
            <component
                :is="appearance === 'light' ? Moon : Sun"
                class="h-5 w-5 text-neutral-500 dark:text-neutral-400"
            />
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                {{ appearance === 'light' ? 'Dark Mode' : 'Light Mode' }}
            </span>
        </button>
    </div>
</template>