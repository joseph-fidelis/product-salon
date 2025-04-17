import type { ColumnDef } from '@tanstack/vue-table';
import type { Staff } from '@/types/staff';
import { h } from 'vue'
import DropdownAction from '@/components/data-table/DataTableDropDown.vue'

export function getStaffColumns({
    onEdit,
    onDelete,
}: {
    onEdit: (staff: Staff) => void
    onDelete: (staff: Staff) => void
}): ColumnDef<Staff>[] {
    return [
        {
            accessorKey: 'id',
            header: '#',
            cell: ({ row }) => row.index + 1,
        },
        {
            accessorKey: 'first_name',
            header: 'First Name',
            cell: ({ row }) => h('div', row.getValue('first_name')),
        },
        {
            accessorKey: 'last_name',
            header: 'Last Name',
            cell: ({ row }) => h('div', row.getValue('last_name')),
        },
        {
            accessorKey: 'email',
            header: 'Email',
            cell: ({ row }) => h('div', row.getValue('email')),
        },
        {
            accessorKey: 'phone',
            header: 'Phone',
            cell: ({ row }) => h('div', row.getValue('phone')),
        },
        {
            accessorKey: 'address',
            header: 'Address',
            cell: ({ row }) => h('div', row.getValue('address')),
        },
        {
            accessorKey: 'emergency_contact',
            header: 'Emergency Contact',
            cell: ({ row }) => h('div', row.getValue('emergency_contact')),
        },
        {
            accessorKey: 'commission',
            header: 'Commission',
            cell: ({ row }) => {
                const commission = parseFloat(row.getValue('commission') || '0');
                const formattedCommission = `${commission.toFixed(2)}%`;
                return h('div', formattedCommission);
            },
        },
        {
            id: 'actions',
            header: 'Actions',
            cell: ({ row }) => {
                const staff = row.original;
                return h(DropdownAction, {
                    item: staff,
                    onEdit: () => onEdit(staff),
                    onDelete: () => onDelete(staff),
                });
            },
        },
    ];
}
