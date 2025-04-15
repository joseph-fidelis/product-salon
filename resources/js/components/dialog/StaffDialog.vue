<template>
    <Dialog v-model:open="isOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ isEditMode ? 'Edit Service' : 'Add Service' }}</DialogTitle>
        </DialogHeader>
  
        <form @submit="onSubmit" class="space-y-6 py-4">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel>Staff Name</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Enter name" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
  
          <FormField v-slot="{ componentField }" name="price">
            <FormItem>
              <FormLabel>Price (₦)</FormLabel>
              <FormControl>
                <Input type="number" placeholder="1000" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
  
          <FormField v-slot="{ componentField }" name="description">
            <FormItem>
              <FormLabel>Description</FormLabel>
              <FormControl>
                <Textarea rows="3" placeholder="Optional" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
  
          <DialogFooter>
            <Button type="submit" :disabled="isSubmitting">
              {{ isEditMode ? 'Update' : 'Create' }}
            </Button>
            <Button type="button" variant="secondary" @click="isOpen = false">Cancel</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </template>
  
  <script setup lang="ts">
  import { ref, watch, computed, h } from 'vue';
  import { useForm } from 'vee-validate';
  import { toTypedSchema } from '@vee-validate/zod';
  import * as z from 'zod';
  import { useForm as useInertiaForm } from '@inertiajs/vue3';
  import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
  import { Input } from '@/components/ui/input';
  import { Textarea } from '@/components/ui/textarea';
  import { Button } from '@/components/ui/button';
  import {
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormMessage
  } from '@/components/ui/form';
  import type { Staff } from '@/types/staff';
  
  const props = defineProps<{
    open: boolean;
    staff?: Staff | null;
  }>();

  const emit = defineEmits(['close']);
  
  const isOpen = ref(props.open);

  watch(() => props.open, (val) => isOpen.value = val);

  watch(isOpen, (val) => { if (!val) emit('close') });
  
  
  const isEditMode = computed(() => !!props.staff);

  
  const staffSchema = toTypedSchema(z.object({
    firstName: z.string().min(4, 'Name is required'),
    lastName: z.string().min(4, 'Name is required'),
    price: z.coerce.number().min(2, 'Price must be a valid number'),
    description: z.string().min(5, 'Description is required'),
  }));

  // VeeValidate form
  const { handleSubmit, values, isSubmitting, setValues } = useForm({
    validationSchema: staffSchema,
    initialValues: {
      firstName: props.staff?.firstName ?? '',
    }
  });

  // Watch for changes to service prop and update form values if in edit mode
  watch(() => props.staff, (newStaff) => {
    if (newStaff) {
      setValues({
        firstName: newService.name,
        // price: newService.price,
        // description: newService.description,
      });
    }
  });

  // Form submission handler (create or update)
  const onSubmit = handleSubmit((data) => {
    if (isEditMode.value && props.staff) {
      useInertiaForm(data).put(route('admin.staff.update', props.staff.id), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
      });
    } else {
      useInertiaForm(data).post(route('admin.staff.store'), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
      });
    }
  });
  
  </script>
  