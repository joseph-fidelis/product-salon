<template>
    <Dialog v-model:open="isOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ isEditMode ? 'Edit Staff' : 'Add Staff' }}</DialogTitle>
        </DialogHeader>

        <!-- Display the exact error message from the backend -->
        <div v-if="formError" class="mb-4 p-3 bg-red-100 border border-red-300 rounded-md text-red-600 text-sm">
          {{ formError }}
        </div>

        <form @submit="onSubmit" class="space-y-6 py-4">
          <!-- First Name -->
          <FormField v-slot="{ componentField }" name="first_name">
            <FormItem>
              <FormLabel>First Name</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Enter first name" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Last Name -->
          <FormField v-slot="{ componentField }" name="last_name">
            <FormItem>
              <FormLabel>Last Name</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Enter last name" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Email -->
          <FormField v-slot="{ componentField }" name="email">
            <FormItem>
              <FormLabel>Email</FormLabel>
              <FormControl>
                <Input type="email" placeholder="Enter email" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Phone -->
          <FormField v-slot="{ componentField }" name="phone">
            <FormItem>
              <FormLabel>Phone</FormLabel>
              <FormControl>
                <Input type="tel" placeholder="Enter phone number" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Address -->
          <FormField v-slot="{ componentField }" name="address">
            <FormItem>
              <FormLabel>Address</FormLabel>
              <FormControl>
                <Textarea placeholder="Enter address (optional)" v-bind="componentField" rows="2" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Commission -->
          <FormField v-slot="{ componentField }" name="commission">
            <FormItem>
              <FormLabel>Commission (%)</FormLabel>
              <FormControl>
                <Input type="number" placeholder="e.g. 5" min="0" max="99" step="0.01"
                       v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Emergency Contact -->
          <FormField v-slot="{ componentField }" name="emergency_contact">
            <FormItem>
              <FormLabel>Emergency Contact</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Enter emergency contact" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <DialogFooter>
            <Button type="submit" :disabled="isSubmitting">
              {{ isEditMode ? 'Update' : 'Create' }}
            </Button>
            <Button type="button" variant="secondary" @click="isOpen = false">
              Cancel
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </template>

  <script setup lang="ts">
  import { ref, watch, computed } from 'vue';
  import { useForm } from 'vee-validate';
  import { toTypedSchema } from '@vee-validate/zod';
  import * as z from 'zod';
  import { useForm as useInertiaForm } from '@inertiajs/vue3';
  import {
    Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter
  } from '@/components/ui/dialog';
  import { Input } from '@/components/ui/input';
  import { Textarea } from '@/components/ui/textarea';
  import { Button } from '@/components/ui/button';
  import {
    FormField, FormItem, FormLabel, FormControl, FormMessage
  } from '@/components/ui/form';
  import type { Staff } from '@/types/staff';

  // Define props with optional properties
  const props = defineProps<{
    open: boolean;
    staff?: Staff | null;
    isAdding?: boolean;
  }>();

  const emit = defineEmits(['close', 'updated']);
  const isOpen = ref(props.open);
  const formError = ref('');

  watch(() => props.open, (val) => {
    isOpen.value = val;
    if (val) {
      // Reset error when dialog opens
      formError.value = '';
    }
  });

  watch(isOpen, (val) => {
    if (!val) emit('close');
  });

  const isEditMode = computed(() => !!props.staff);

  const staffSchema = toTypedSchema(z.object({
    first_name: z.string().min(2, 'First name is required'),
    last_name: z.string().min(2, 'Last name is required'),
    email: z.string().email('Valid email is required'),
    address: z.string().optional().or(z.literal('')),
    phone: z.string().min(10, 'Phone number is required'),
    commission: z.number().min(0, 'Commission is required').max(99, 'Commission must be less than 100'),
    emergency_contact: z.string().min(10, 'Emergency contact is required')
  }));

  const { handleSubmit, values, isSubmitting, setValues } = useForm({
    validationSchema: staffSchema,
    initialValues: {
      first_name: props.staff?.first_name ?? '',
      last_name: props.staff?.last_name ?? '',
      email: props.staff?.email ?? '',
      address: props.staff?.address ?? '',
      phone: props.staff?.phone ?? '',
      commission: props.staff?.commission ?? 0,
      emergency_contact: props.staff?.emergency_contact ?? '',
    }
  });

  watch(() => props.staff, (newStaff) => {
    if (newStaff) {
      setValues({
        first_name: newStaff.first_name,
        last_name: newStaff.last_name,
        email: newStaff.email,
        address: newStaff.address ?? '',
        phone: newStaff.phone,
        commission: newStaff.commission ?? 0,
        emergency_contact: newStaff.emergency_contact
      });
      // Reset error
      formError.value = '';
    }
  });

  const onSubmit = handleSubmit((data) => {
    // Reset error state
    formError.value = '';

    const form = useInertiaForm(data);

    if (isEditMode.value && props.staff) {
      form.put(route('admin.staff.update', props.staff.id), {
        preserveScroll: true,
        onSuccess: () => {
          emit('close');
          emit('updated');
        },
        onError: (errors) => {
          // Display the exact error message from the backend
          console.log('Form errors:', errors);

          // Check if we have a specific error message
          if (errors.error) {
            formError.value = errors.error;
          }
          // Check for email error
          else if (errors.email) {
            formError.value = errors.email;
          }
          // If we have any errors but no specific one to display
          else if (Object.keys(errors).length > 0) {
            // Get the first error message
            const firstErrorKey = Object.keys(errors)[0];
            formError.value = errors[firstErrorKey];
          }
        }
      });
    } else {
      form.post(route('admin.staff.store'), {
        preserveScroll: true,
        onSuccess: () => {
          emit('close');
          emit('updated');
        },
        onError: (errors) => {
          // Display the exact error message from the backend
          console.log('Form errors:', errors);

          // Check if we have a specific error message
          if (errors.error) {
            formError.value = errors.error;
          }
          // Check for email error
          else if (errors.email) {
            formError.value = errors.email;
          }
          // If we have any errors but no specific one to display
          else if (Object.keys(errors).length > 0) {
            // Get the first error message
            const firstErrorKey = Object.keys(errors)[0];
            formError.value = errors[firstErrorKey];
          }
        }
      });
    }
  });
  </script>
