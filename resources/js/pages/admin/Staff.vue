<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Staff } from '@/types/staff';
import { ref, onMounted } from 'vue';
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
  staff?: {
    data: Staff[];
  } | null;
  services?: {
    data: Service[];
  } | null;
}>();

console.log('staffs:', props.staff);
console.log('services:', props.services);

const dialogOpen = ref(false);
const selectedStaff = ref<Staff | null>(null);
const totalCommission = ref(0);
const isLoading = ref(true);

// Calculate total commission if data is available
onMounted(() => {
  if (props.staff?.data) {
    calculateTotalCommission();
    isLoading.value = false;
  }
});

function calculateTotalCommission() {
  if (props.staff?.data) {
    totalCommission.value = props.staff.data.reduce((total, staff) => {
      return total + (staff.commission || 0);
    }, 0);
  }
}

// Define table columns with proper field mapping
const columns = getStaffColumns({
  onEdit: handleEdit,
  onDelete: handleDelete,
});

function handleEdit(staff: Staff) {
  selectedStaff.value = staff;
  dialogOpen.value = true;
}

function handleDelete(staff: Staff) {
  if (confirm('Are you sure you want to delete this staff member?')) {
    router.delete(route('admin.staff.destroy', staff.id), {
      preserveScroll: true,
      onSuccess: () => {
        console.log('Staff deleted successfully');
      },
      onError: (errors) => {
        console.log('Staff failed to delete', errors);
      },
    });
  }
}

function openAddStaffDialog() {
  selectedStaff.value = null;
  dialogOpen.value = true;
}

function handleDialogClose() {
  dialogOpen.value = false;
  selectedStaff.value = null;
}

// Function to refresh data after adding/editing staff
function refreshData() {
  router.reload({ preserveScroll: true, only: ['staff'] });
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
            <h1 class="text-4xl font-semibold">{{ staff?.data.length || 0 }}</h1>
            <h1 class="text-xl">Total Staff</h1>
          </div>
        </div>

        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
            <h1 class="text-4xl font-semibold">{{ totalCommission }}</h1>
            <h1 class="text-xl">Total Commission Rate</h1>
          </div>
        </div>

        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
            <h1 class="text-4xl font-semibold">{{ staff?.data.length || 0 }}</h1>
            <h1 class="text-xl">Active Staff</h1>
          </div>
        </div>
      </div>

      <!-- Add button -->
      <div class="flex justify-end mt-4">
        <button @click="openAddStaffDialog"
          class="border border-sidebar-border/70 dark:border-sidebar-border text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 transition duration-200">
          + Add Staff
        </button>
      </div>

      <!-- Table Section -->
      <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
        <div v-if="isLoading" class="flex justify-center items-center h-40">
          <span class="text-gray-500">Loading staff data...</span>
        </div>
        <div v-else-if="!staff?.data?.length" class="flex justify-center items-center h-40">
          <span class="text-gray-500">No staff records found. Add a new staff member to get started.</span>
        </div>
        <DataTable
          v-else
          :columns="columns"
          :data="staff?.data || []"
          @edit="handleEdit"
          @delete="handleDelete"
        />
      </div>
    </div>

    <!-- Staff Dialog with correct props -->
    <StaffDialog
      :open="dialogOpen"
      @close="handleDialogClose"
      :staff="selectedStaff"
      @updated="refreshData"
    />
  </AppLayout>
</template>
