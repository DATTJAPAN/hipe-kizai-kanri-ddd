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
                            <Button variant={showOnlyTrashed ? 'default' : 'outline'} size="sm" onClick={handleToggleOnlyTrashed} className="h-8">
                                <Trash2 className="mr-2 h-4 w-4" />
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
                                className="h-8"
                                disabled={showOnlyTrashed}
                            >
                                <Archive className="mr-2 h-4 w-4" />
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
                            <Button variant="outline" size="sm" onClick={handleRefetch} className="h-8" disabled={isPending || isFetching}>
                                <RefreshCw className={cn('mr-2 h-4 w-4', (isPending || isFetching) && 'animate-spin')} />
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
                        <Button asChild variant="ghost" size="icon" className="h-8 w-8">
                            <Link href={getManageUrl(row.original.prefixed_id, row.original.deleted_at)}>
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
