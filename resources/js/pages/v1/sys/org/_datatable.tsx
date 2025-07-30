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
import { AlertTriangle, Archive, RefreshCw, SquarePen, Trash2 } from 'lucide-react';
import { MantineReactTable, type MRT_ColumnDef, useMantineReactTable } from 'mantine-react-table';
import { useMemo, useState } from 'react';

export default function SysOrgDashboardDataTable() {
    const [showOnlyTrashed, setShowOnlyTrashed] = useState(false);
    const [showWithTrashed, setShowWithTrashed] = useState(false);

    const {
        isPending,
        isFetching,
        error,
        data = [],
        refetch,
    } = useGetAllOrganizations({
        onlyTrashed: showOnlyTrashed,
        withTrashed: showWithTrashed && !showOnlyTrashed,
    });

    const columns = useMemo<MRT_ColumnDef<Organization>[]>(() => getOrganizationColumns, []);

    const defaultMRTOptions = getDefaultMRTOptions<Organization>();

    const handleToggleOnlyTrashed = () => {
        setShowOnlyTrashed(!showOnlyTrashed);
        if (!showOnlyTrashed) {
            setShowWithTrashed(false); // Reset withTrashed when switching to onlyTrashed
        }
    };

    const handleToggleWithTrashed = () => {
        setShowWithTrashed(!showWithTrashed);
        if (!showWithTrashed) {
            setShowOnlyTrashed(false); // Reset onlyTrashed when switching to withTrashed
        }
    };

    const handleRefetch = () => {
        refetch();
    };

    // Helper function to generate manage URL with query string
    const getManageUrl = (prefixedId: string, deletedAt?: Date | string | null) => {
        const baseUrl = route('v1.sys.orgs.manage:get', prefixedId);

        // Add a query string if the item is deleted, or we're showing trashed items
        if (deletedAt != null && (showOnlyTrashed || showWithTrashed)) {
            return `${baseUrl}?trashed=true`;
        }

        return baseUrl;
    };

    const table = useMantineReactTable<Organization>({
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
                                variant={showOnlyTrashed ? 'default' : 'outline'}
                                size="sm"
                                onClick={handleToggleOnlyTrashed}
                                className={cn(
                                    'h-8',
                                    showOnlyTrashed
                                        ? 'border-red-600 bg-red-600 text-white hover:bg-red-700'
                                        : 'border-red-300 text-red-600 hover:border-red-400 hover:bg-red-50 hover:text-red-700',
                                )}
                            >
                                <Trash2 className={cn('mr-2 h-4 w-4', showOnlyTrashed ? 'text-white' : 'text-red-500 group-hover:text-red-700')} />
                                Only Trashed
                                {showOnlyTrashed && <span className="ml-2 rounded-full bg-white/20 px-1.5 py-0.5 text-xs">{data.length}</span>}
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            {showOnlyTrashed ? 'Showing only soft-deleted organizations' : 'Click to view only soft-deleted organizations'}
                        </TooltipContent>
                    </Tooltip>

                    <Tooltip>
                        <TooltipTrigger asChild>
                            <Button
                                variant={showWithTrashed ? 'default' : 'outline'}
                                size="sm"
                                onClick={handleToggleWithTrashed}
                                className={cn(
                                    'group h-8',
                                    showWithTrashed
                                        ? 'border-amber-600 bg-amber-600 text-white hover:bg-amber-700'
                                        : 'border-amber-300 text-amber-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700',
                                )}
                                disabled={showOnlyTrashed}
                            >
                                <Archive
                                    className={cn('mr-2 h-4 w-4', showWithTrashed ? 'text-white' : 'text-amber-500 group-hover:text-amber-700')}
                                />
                                Include Trashed
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            {showWithTrashed
                                ? 'Currently showing active and soft-deleted organizations'
                                : 'Click to include soft-deleted organizations with active ones'}
                        </TooltipContent>
                    </Tooltip>

                    <Tooltip>
                        <TooltipTrigger asChild>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={handleRefetch}
                                className="group h-8 border-blue-300 text-blue-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700"
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

                {/* Status indicator */}
                <div className="ml-4 text-sm text-muted-foreground">
                    {showOnlyTrashed && 'Showing only trashed items'}
                    {showWithTrashed && !showOnlyTrashed && 'Showing active and trashed items'}
                    {!showOnlyTrashed && !showWithTrashed && 'Showing active items only'}
                </div>
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
                            <Link href={getManageUrl(row.original.prefixed_id, row.original.deleted_at)}>
                                <SquarePen className="h-4 w-4 text-green-500 group-hover:text-green-700" />
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
