<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ChevronDown, ChevronUp, ChevronsUpDown } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    columns: Column[];
    data: Record<string, any>[];
    filters?: Record<string, any>;
    current_page: number;
    per_page: number;
}
interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    width?: string;
}

const props = withDefaults(defineProps<Props>(), {
    filters: () => ({}),
});

const selectedSort = ref(props.filters?.sort || '');
const sortOrder = ref(props.filters?.direction || 'asc');

// Emit event untuk parent menangani sort
const emit = defineEmits<{
    (e: 'toggleSort', column: string): void;
}>();

const handleSortClick = (key: string) => {
    emit('toggleSort', key);
};
</script>

<template>
    <Table class="mt-2 rounded-lg border border-muted">
        <TableHeader>
            <TableRow class="bg-muted/50">
                <TableHead
                    v-for="col in columns"
                    :key="col.key"
                    class="px-6 py-3 whitespace-nowrap"
                >
                    <Button
                        v-if="col.sortable"
                        variant="ghost"
                        size="sm"
                        class="-ml-3 h-8"
                        @click="handleSortClick(col.key)"
                    >
                        <span>{{ col.label }}</span>
                        <ChevronUp
                            v-if="
                                selectedSort === col.key && sortOrder === 'desc'
                            "
                            class="ml-2 h-4 w-4"
                        />
                        <ChevronDown
                            v-else-if="
                                selectedSort === col.key && sortOrder === 'asc'
                            "
                            class="ml-2 h-4 w-4"
                        />
                        <ChevronsUpDown v-else class="ml-2 h-4 w-4" />
                    </Button>
                    <span v-else>{{ col.label }}</span>
                </TableHead>
            </TableRow>
        </TableHeader>

        <TableBody
            ><TableRow
                v-for="(item, i) in data"
                :key="i"
                class="transition hover:bg-muted/30"
            >
                <TableCell
                    v-for="col in columns"
                    :key="col.key"
                    class="whitespace-nowrap"
                >
                    <!-- Default cell -->
                    <slot
                        :name="col.key"
                        :item="item"
                        :i="i"
                        :current_page="current_page"
                        :per_page="per_page"
                    >
                        {{ item[col.key] ?? '-' }}
                    </slot>
                </TableCell>
            </TableRow>

            <TableRow v-if="!data.length">
                <TableCell
                    :colspan="columns.length"
                    class="py-4 text-center text-muted-foreground"
                >
                    Tidak ada data ditemukan.
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
