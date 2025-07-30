import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { Organization } from '@/types/schema';
import { createMRTColumnHelper } from 'mantine-react-table';

const columnHelper = createMRTColumnHelper<Organization>();

const columns = [
    columnHelper.accessor('prefixed_id', {
        header: 'Id',
        Cell: ({ cell }) => {
            const prefixedId = cell.getValue();

            return (
                <Badge variant="secondary" className="border-slate-300 bg-slate-100 font-mono text-xs text-slate-700 hover:bg-slate-200">
                    <span className="font-bold">{prefixedId}</span>
                </Badge>
            );
        },
    }),

    columnHelper.accessor('name', {
        header: 'Name',
        enableSorting: true,
    }),

    columnHelper.accessor('business_email', {
        header: 'Business Email',
    }),

    columnHelper.accessor((originalRow) => (originalRow.deleted_at ? 'false' : 'true'), {
        header: 'Active',
        filterVariant: 'checkbox',
        Cell: ({ cell }) => {
            const statusValue: 'false' | 'true' = cell.getValue();
            const isActive: boolean = statusValue === 'true';

            return (
                <Badge variant="outline" className={cn(isActive ? 'border-green-600 text-green-700' : 'border-red-600 text-red-700', 'font-bold')}>
                    {isActive ? 'Active' : 'Inactive'}
                </Badge>
            );
        },
    }),
];

export { columns as getOrganizationColumns };
