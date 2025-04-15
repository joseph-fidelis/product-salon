
import type { ColumnDef } from '@tanstack/vue-table';
import type { Service } from '@/types/service';
import {  h } from 'vue'
import DropdownAction from '@/components/data-table/DataTableDropDown.vue'


export function getServiceColumns({
    onEdit,
    onDelete,
  }: {
    onEdit: (service: Service) => void
    onDelete: (service: Service) => void
  }): ColumnDef<Service>[] {
    return [
      {
        accessorKey: 'id',
        header: '#',
        cell: ({ row }) => row.index + 1,
      },
      {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', row.getValue('name')),
      },
      {
        accessorKey: 'price',
        header: 'Amount',
        cell: ({ row }) => {
          const price = Number.parseFloat(row.getValue('price'))
          const formattedPrice = new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN',
          }).format(price)
          return h('div', formattedPrice)
        },
      },
      {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => h('div', row.getValue('description')),
      },
      {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
          const service = row.original
          return h(DropdownAction, {
            item: service,
            onEdit: () => onEdit(service),
            onDelete: () => onDelete(service),
          })
        },
      },
    ]
  }

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
