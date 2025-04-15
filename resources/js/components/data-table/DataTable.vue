<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef,  ColumnFiltersState, } from '@tanstack/vue-table'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

import {
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    useVueTable,
} from '@tanstack/vue-table'

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[]
    data: TData[]
}>()

const table = useVueTable({
    get data() { return props.data },
    get columns() { return props.columns },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    enableRowSelection: true,
})
</script>

<template>
    <div class="border rounded-md">
        <Table>
            <TableHeader>
                <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                    <TableHead v-for="header in headerGroup.headers" :key="header.id">
                        <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                            :props="header.getContext()" />
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="table.getRowModel().rows?.length">
                    <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                        :data-state="row.getIsSelected() ? 'selected' : undefined">
                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </TableCell>
                    </TableRow>
                </template>
                <template v-else>
                    <TableRow>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </div>
    <div class="flex items-center justify-start py-4 space-x-4">
        <Button class="border-2 rounded-sm px-2 py-1" variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
            Prev
        </Button>
        <Button class="border-2 rounded-sm px-2 py-1" variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
            Next
        </Button>
    </div>
</template>