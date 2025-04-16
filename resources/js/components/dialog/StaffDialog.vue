<template>
    <Dialog v-model:open="isOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ isEditMode ? 'Edit Staff' : 'Add Staff' }}</DialogTitle>
        </DialogHeader>
  
        <form @submit="onSubmit" class="space-y-6 py-4">
          <!-- First Name -->
          <FormField v-slot="{ componentField }" name="firstName">
            <FormItem>
              <FormLabel>First Name</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Enter first name" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
  
          <!-- Last Name -->
          <FormField v-slot="{ componentField }" name="lastName">
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
  
          <!-- Specializations (Multi-select) -->
          <FormField v-slot="{ value, handleChange }" name="specialization">
            <FormItem>
              <FormLabel>Specializations</FormLabel>
              <FormControl>
                <Popover v-model:open="openPopOver">
                  <PopoverTrigger as-child>
                    <div class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm hover:cursor-pointer">
                      <span class="truncate">
                        {{ value?.length
                          ? servicesList.filter(s => value.includes(s.id)).map(s => s.name).join(', ')
                          : 'Select services...' }}
                      </span>
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-50" fill="none"
                           viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                  </PopoverTrigger>
                  <PopoverContent class="w-[300px] p-0">
                    <Command>
                      <CommandInput placeholder="Search services..." />
                      <CommandList>
                        <CommandItem
                          v-for="service in servicesList"
                          :key="service.id"
                          @select.prevent
                        >
                          <div class="flex items-center space-x-2">
                            <Checkbox
                              :checked="value?.includes(service.id)"
                              @update:checked="(checked) => {
                                let updated = [...(value || [])];
                                if (checked) {
                                  updated.push(service.id);
                                } else {
                                  updated = updated.filter((id) => id !== service.id);
                                }
                                handleChange(updated);
                              }"
                            />
                            <span>{{ service.name }}</span>
                          </div>
                        </CommandItem>
                      </CommandList>
                    </Command>
                  </PopoverContent>
                </Popover>
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
  
          <!-- Emergency Contact -->
          <FormField v-slot="{ componentField }" name="emergencyContact">
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
  import {
    Popover, PopoverContent, PopoverTrigger,
  } from '@/components/ui/popover';
  import {
    Command, CommandInput, CommandItem, CommandList,
  } from '@/components/ui/command';
  import { Checkbox } from '@/components/ui/checkbox';
  import type { Staff } from '@/types/staff';
  import type { Service } from '@/types/service';
  
  // Define props; note, we make services optional by adding "?" and then using a fallback.
  const props = defineProps<{
    open: boolean;
    staff?: Staff | null;
    services?: Service[];  // services is now optional.
  }>();
  
  // Use a fallback to ensure services is always an array.
  const servicesList = props.services || [];
  
  const openPopOver = ref(false);
  const emit = defineEmits(['close']);
  const isOpen = ref(props.open);
  
  watch(() => props.open, (val) => isOpen.value = val);
  watch(isOpen, (val) => { if (!val) emit('close') });
  
  const isEditMode = computed(() => !!props.staff);
  
  const staffSchema = toTypedSchema(z.object({
    firstName: z.string().min(4, 'First name is required'),
    lastName: z.string().min(4, 'Last name is required'),
    email: z.string().email('Email is required'),
    address: z.string(),
    phone: z.string().min(11, 'Phone number is required'),
    commission: z.number().min(0, 'Commission is required').max(99, 'Commission must be less than 100'),
    emergencyContact: z.string().min(10, 'Emergency contact is required'),
    specialization: z.array(z.number()).nullable(),  // allow null
  }));
  
  const { handleSubmit, values, isSubmitting, setValues } = useForm({
    validationSchema: staffSchema,
    initialValues: {
      firstName: props.staff?.firstName ?? '',
      lastName: props.staff?.lastName ?? '',
      email: props.staff?.email ?? '',
      address: props.staff?.address ?? '',
      phone: props.staff?.phone ?? '',
      commission: props.staff?.commission ?? 0,
      emergencyContact: props.staff?.emergencyContact ?? '',
      specialization: props.staff?.specialization?.map(s => s.id) ?? [],
    }
  });
  
  watch(() => props.staff, (newStaff) => {
    if (newStaff) {
      setValues({
        firstName: newStaff.firstName,
        lastName: newStaff.lastName,
        email: newStaff.email,
        address: newStaff.address ?? '',
        phone: newStaff.phone,
        commission: newStaff.commission,
        emergencyContact: newStaff.emergencyContact,
        specialization: newStaff.specialization?.map(s => s.id) ?? [],
      });
    }
  });
  
  const onSubmit = handleSubmit((data) => {
    const form = useInertiaForm(data);
    if (isEditMode.value && props.staff) {
      form.put(route('admin.staff.update', props.staff.id), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
      });
    } else {
      form.post(route('admin.staff.store'), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
      });
    }
  });
  </script>
  