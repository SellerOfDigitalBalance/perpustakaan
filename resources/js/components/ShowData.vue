<script setup lang="ts">
import { Table, TableBody, TableCell, TableRow } from '@/components/ui/table';

interface Column {
    key: string;
    label: string;
    width?: string;
}

interface Props {
    columns: Column[];
    data: Record<string, any>;
}

const props = defineProps<Props>();
</script>

<template>
    <Table class="rounded-lg border border-muted">
        <TableBody>
            <TableRow
                v-for="col in columns"
                :key="col.key"
                class="hover:bg-muted/20"
            >
                <!-- Label -->
                <TableCell
                    class="w-[200px] bg-muted/30 font-medium whitespace-nowrap"
                    :style="{ width: col.width }"
                >
                    {{ col.label }}
                </TableCell>

                <!-- Value -->
                <TableCell class="whitespace-nowrap">
                    <slot
                        :name="col.key"
                        :value="props.data[col.key]"
                        :data="props.data"
                    >
                        {{ props.data[col.key] ?? '-' }}
                    </slot>
                </TableCell>
            </TableRow>

            <TableRow v-if="!Object.keys(props.data).length">
                <TableCell
                    :colspan="2"
                    class="py-4 text-center text-muted-foreground"
                >
                    Data tidak ditemukan.
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
