import SkeletonDatatable from '@/components/skeletons/skeleton-datatable';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { getDefaultMRTOptions } from '@/configs/datatables';
import { getOrganizationColumns } from '@/definitions/columns';
import { useGetAllOrganizations } from '@/hooks/queries/sys';
import { Organization } from '@/types/schema/organization';
import { AlertTriangle } from 'lucide-react';
import { MantineReactTable, type MRT_ColumnDef, useMantineReactTable } from 'mantine-react-table';
import { useMemo } from 'react';

export default function SysOrgDataTable() {
    const { isPending, isFetching, error, data = [] } = useGetAllOrganizations();

    const columns = useMemo<MRT_ColumnDef<Organization>[]>(() => getOrganizationColumns, []);

    const defaultMRTOptions = getDefaultMRTOptions<Organization>();

    const table = useMantineReactTable<Organization>({
        ...defaultMRTOptions,
        columns,
        data,
        state: { isLoading: isPending || isFetching },
    });

    return (
        <div className="space-y-4">
            {/* --- Data Table --- */}
            <MantineReactTable table={table} />

            {/* --- Alert Message --- */}
            {error instanceof Error && (
                <Alert variant="destructive">
                    <AlertTriangle className="h-4 w-4" />
                    <AlertTitle>Error</AlertTitle>
                    <AlertDescription>{error.message}</AlertDescription>
                </Alert>
            )}

            {/* --- Skeleton --- */}
            {isPending && <SkeletonDatatable />}
        </div>
    );
}
