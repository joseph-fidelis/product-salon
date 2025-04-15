// resources/js/components/data-table/columns.ts
import type { ColumnDef } from '@tanstack/vue-table';
import type { Service } from '@/types/service';
import { Button } from '@/components/ui/button'
import { ArrowUpDown } from 'lucide-vue-next'
import { ref, h } from 'vue'
import DropdownAction from '@/components/data-table/DataTableDropDown.vue'


export const columns: ColumnDef<Service>[] = [
    {
        accessorKey: 'id',
        header: '#',
        cell: ({ row }) => row.index + 1,
      },
    {
        accessorKey: 'name',
        header: ({ column }) =>
          h(Button, {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          }, () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
        cell: ({ row }) => h('div', row.getValue('name')),
      },
      {
        accessorKey: 'price',
        header: () => h('div', { class: 'text-left' }, 'Amount'),
        cell:({row})=>{
            const price = Number.parseFloat(row.getValue('price'));
            const formattedPrice = new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN',
            }).format(price);
            return h('div', { class: 'text-left' }, formattedPrice);
        }
      },
      {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => h('div', row.getValue('description')),
      },
      {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'text-left' }, 'Actions'),
        cell: ({ row }) => {
          const service = row.original
          return h('div', { class: 'relative' }, h(DropdownAction, {
            item: service,
            actions: [
              {
                label: 'Edit',
                icon: 'edit',
                onClick: () => {
                  // Handle edit action
                  console.log('Edit action clicked for service:', service);
                },
              },
              {
                label: 'Delete',
                icon: 'trash',
                onClick: () => {
                  // Handle delete action
                  console.log('Delete action clicked for service:', service);
                },
              },
            ],
        
        }))
        },
      },
];

export const publicColumn: ColumnDef<Service>[] = [
    {
        accessorKey: 'id',
        header: () => h('div',{class: 'text-center'}, '#'),
        cell: ({ row }) => row.index + 1,
      },
    {
        accessorKey: 'name',
        header: () => h('div',{class: 'text-center'}, 'Name'),
        cell: ({ row }) => h('div', row.getValue('name')),
      },
      {
        accessorKey: 'price',
        header: () => h('div', { class: 'text-left' }, 'Amount'),
        cell:({row})=>{
            const price = Number.parseFloat(row.getValue('price'));
            const formattedPrice = new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN',
            }).format(price);
            return h('div', { class: 'text-left' }, formattedPrice);
        }
      },
      {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => h('div', {class: 'text-left'},row.getValue('description')),
      },

];
