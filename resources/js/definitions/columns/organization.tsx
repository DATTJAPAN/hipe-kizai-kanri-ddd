import type { Organization } from '@/types/schema';
import { createMRTColumnHelper } from 'mantine-react-table';

const columnHelper = createMRTColumnHelper<Organization>();

const columns = [
    columnHelper.accessor('prefixed_id', {
        header: 'Id',
    }),
    columnHelper.accessor('name', {
        header: 'Name',
        enableSorting: true,
    }),
    columnHelper.accessor('business_email', {
        header: 'Business Email',
    }),
];

export { columns as getOrganizationColumns };
