<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { MoreHorizontal } from 'lucide-vue-next'

import type { Service } from '@/types/service'

const emit = defineEmits(['edit', 'delete']);

const props = defineProps<{
  item: Service
}>()

</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0">
        <span class="sr-only">Open menu</span>
        <MoreHorizontal class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuLabel>Actions</DropdownMenuLabel>

      <DropdownMenuItem  @click="$emit('edit', item)" >Edit</DropdownMenuItem>
      <DropdownMenuItem  @click="$emit('delete', item)" class="text-red-600">Delete</DropdownMenuItem>
    
    </DropdownMenuContent>
  </DropdownMenu>
</template>