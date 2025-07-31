import { Badge } from '@/components/ui/badge';
import { OrganizationUnit } from '@/types/schema';
import { createMRTColumnHelper } from 'mantine-react-table';

const columnHelper = createMRTColumnHelper<OrganizationUnit>();

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

    columnHelper.accessor('type', {
        header: 'Unit Type',
    }),

    columnHelper.accessor('parent_unit_id', {
        header: 'Parent Unit',
    }),
];

export { columns as getOrganizationUnitColumns };
