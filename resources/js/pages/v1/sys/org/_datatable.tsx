import SkeletonDatatable from '@/components/skeletons/skeleton-datatable';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getDefaultMRTOptions } from '@/configs/datatables';
import { getOrganizationColumns } from '@/definitions/columns';
import { useGetAllOrganizations } from '@/hooks/queries/sys';
import { cn } from '@/lib/utils';
import { Organization } from '@/types/schema/organization';
import { Link } from '@inertiajs/react';
import { AlertTriangle, SquarePen } from 'lucide-react';
import { MantineReactTable, type MRT_ColumnDef, useMantineReactTable } from 'mantine-react-table';
import { useMemo } from 'react';

export default function SysOrgDashboardDataTable() {
    const { isPending, isFetching, error, data = [] } = useGetAllOrganizations();

    const columns = useMemo<MRT_ColumnDef<Organization>[]>(() => getOrganizationColumns, []);

    const defaultMRTOptions = getDefaultMRTOptions<Organization>();

    const table = useMantineReactTable<Organization>({
        ...defaultMRTOptions,
        columns,
        data,
        state: { isLoading: isPending || isFetching },
        enableRowActions: true,
        positionActionsColumn: 'last',
        renderRowActions: ({ row }) => (
            <TooltipProvider delayDuration={300}>
                <Tooltip>
                    <TooltipTrigger asChild>
                        <Button asChild variant="ghost" size="icon" className="h-8 w-8">
                            <Link href={route('v1.sys.orgs.manage:get', row.original.prefixed_id)}>
                                <SquarePen className="h-4 w-4" />
                                <span className="sr-only">Manage Organization</span>
                            </Link>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>Manage</TooltipContent>
                </Tooltip>
            </TooltipProvider>
        ),
    });

    return (
        <div className="relative space-y-4">
            {/* --- Data Table --- */}
            <div className={cn(isPending && 'opacity-0')}>
                <MantineReactTable table={table} />
            </div>
            {/* --- Data Table Error Alert Message --- */}
            {error instanceof Error && (
                <Alert variant="destructive">
                    <AlertTriangle className="h-4 w-4" />
                    <AlertTitle>Error</AlertTitle>
                    <AlertDescription>{error.message}</AlertDescription>
                </Alert>
            )}
            {/* --- Data Table Skeleton --- */}
            {isPending && <SkeletonDatatable className="absolute top-0 left-0 w-full" />}
        </div>
    );
}
