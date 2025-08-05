import SkeletonDatatable from '@/components/skeletons/skeleton-datatable';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getDefaultMRTOptions } from '@/configs/datatables';
import { getOrganizationNetworkHostColumns } from '@/definitions/columns/organization_network_host_column';
import { useGetAllOrganizationNetworkHosts } from '@/hooks/queries/orgs';
import { cn } from '@/lib/utils';
import { OrganizationNetworkHost } from '@/types/schema/organization_network_host';
import { Link } from '@inertiajs/react';
import { AlertTriangle, RefreshCw, SquarePen } from 'lucide-react';
import { MantineReactTable, type MRT_ColumnDef, useMantineReactTable } from 'mantine-react-table';
import { useMemo } from 'react';

export default function OrgNetworkHostDashboardDataTable() {
    const { isPending, isFetching, error, data = [], refetch } = useGetAllOrganizationNetworkHosts();

    const columns = useMemo<MRT_ColumnDef<OrganizationNetworkHost>[]>(() => getOrganizationNetworkHostColumns, []);

    const defaultMRTOptions = getDefaultMRTOptions<OrganizationNetworkHost>();

    const handleRefetch = () => {
        refetch();
    };

    const _handleRedirectRoute = (prefixedId: string = '') => {
        return route('v1.org.network_hosts.manage:get', prefixedId);
    };

    const table = useMantineReactTable<OrganizationNetworkHost>({
        ...defaultMRTOptions,
        columns,
        data,
        state: { isLoading: isPending || isFetching },
        enableRowActions: true,
        positionActionsColumn: 'last',
        enableTopToolbar: true,
        renderTopToolbarCustomActions: () => (
            <div className="flex items-center space-x-2">
                <TooltipProvider delayDuration={300}>
                    <Tooltip>
                        <TooltipTrigger asChild>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={handleRefetch}
                                className="group h-8 cursor-pointer border-blue-300 text-blue-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700"
                                disabled={isPending || isFetching}
                            >
                                <RefreshCw
                                    className={cn(
                                        'mr-2 h-4 w-4 text-blue-500 group-hover:text-blue-700',
                                        (isPending || isFetching) && 'animate-spin',
                                    )}
                                />
                                Refresh
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>{isPending || isFetching ? 'Refreshing data...' : 'Click to refresh the data'}</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </div>
        ),
        renderRowActions: ({ row }) => (
            <TooltipProvider delayDuration={300}>
                <Tooltip>
                    <TooltipTrigger asChild>
                        <Button
                            asChild
                            variant="outline"
                            size="icon"
                            className="group h-8 w-8 border-green-300 text-green-600 hover:border-green-400 hover:bg-green-50 hover:text-green-700"
                        >
                            <Link href={_handleRedirectRoute(row.original.prefixed_id)}>
                                <SquarePen className="h-4 w-4 text-green-500 group-hover:text-green-700" />
                                <span className="sr-only">Manage Organization Network Host</span>
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
