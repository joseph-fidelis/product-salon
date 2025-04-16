<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Staff } from '@/types/staff';
import { ref } from 'vue';
import StaffDialog from '@/components/dialog/StaffDialog.vue';
import { getStaffColumns } from '@/components/data-table/StaffColumn';
import { router } from '@inertiajs/vue3';
import DataTable from '@/components/data-table/DataTable.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Staff',
    href: '/admin/staff',
  },
];

const props = defineProps<{
  staffs: {
    data: Staff[];
  } | null;
  services: {
    data: Service[];
  } | null;
}>();
console.log('staffs:', props.staffs);
console.log('services:', props.services);

const dialogOpen = ref(false);
const selectedStaff = ref<Staff | null>(null);

const columns = getStaffColumns({
  onEdit: handleEdit,
  onDelete: handleDelete,
});

function handleEdit(staff: Staff) {
  selectedStaff.value = staff;
  dialogOpen.value = true;
}

function handleDelete(staff: Staff) {
  router.delete(route('admin.staff.destroy', staff.id), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Staff deleted successfully');
    },
    onError: (errors) => {
      console.log('Staff failed to delete');
    },
  });
}
</script>

<template>
  <Head title="Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Analytics Cards -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
            <h1 class="text-4xl font-semibold">{{ staffs?.data.length || 0 }}</h1>
            <h1 class="text-xl">Total Staff</h1>
          </div>
        </div>

        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
            <h1 class="text-4xl font-semibold">8000</h1>
            <h1 class="text-xl">Total Commission Paid</h1>
          </div>
        </div>

        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
            <h1 class="text-4xl font-semibold">8000</h1>
            <h1 class="text-xl">Total </h1>
          </div>
        </div>
      </div>

      <!-- Add button -->
      <div class="flex justify-end mt-4">
        <button @click="dialogOpen = true"
          class="border border-sidebar-border/70 dark:border-sidebar-border text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 transition duration-200">
          + Add Staff
        </button>
      </div>

      <!-- Table Section -->
      <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
        <DataTable :columns="columns" :data="staffs?.data || []" @edit="handleEdit" @delete="handleDelete" />
      </div>
    </div>

    <!-- Pass services data correctly -->
    <StaffDialog
      :open="dialogOpen"
      @close="dialogOpen = false"
      :staff="selectedStaff"
      :services="props.services?.data || []"
    />
  </AppLayout>
</template>
