<template>
    <Dialog v-model:open="isOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ isEditMode ? 'Edit Service' : 'Add Service' }}</DialogTitle>
        </DialogHeader>
  
        <form @submit="onSubmit" class="space-y-6 py-4">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel>Service Name</FormLabel>
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
  import type { Service } from '@/types/service';
  
  const props = defineProps<{
    open: boolean;
    service?: Service | null;
  }>();

  const emit = defineEmits(['close']);
  
  const isOpen = ref(props.open);

  watch(() => props.open, (val) => isOpen.value = val);

  watch(isOpen, (val) => { if (!val) emit('close') });
  
  // Determine if it's in edit mode
  const isEditMode = computed(() => !!props.service);

  // Zod validation schema
  const serviceSchema = toTypedSchema(z.object({
    name: z.string().min(2, 'Name is required'),
    price: z.coerce.number().min(2, 'Price must be a valid number'),
    description: z.string().min(5, 'Description is required'),
  }));

  // VeeValidate form
  const { handleSubmit, values, isSubmitting, setValues } = useForm({
    validationSchema: serviceSchema,
    initialValues: {
      name: props.service?.name ?? '',
      price: props.service?.price ?? 0.0,
      description: props.service?.description ?? '',
    }
  });

  // Watch for changes to service prop and update form values if in edit mode
  watch(() => props.service, (newService) => {
    if (newService) {
      setValues({
        name: newService.name,
        price: newService.price,
        description: newService.description,
      });
    }
  });

  // Form submission handler (create or update)
  const onSubmit = handleSubmit((data) => {
    if (isEditMode.value && props.service) {
      useInertiaForm(data).put(route('admin.services.update', props.service.id), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
      });
    } else {
      useInertiaForm(data).post(route('admin.services.store'), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
      });
    }
  });
  
  </script>
  