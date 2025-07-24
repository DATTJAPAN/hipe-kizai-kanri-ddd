import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { twMerge } from 'tailwind-merge';
import { Skeleton } from '../ui/skeleton';

export default function SkeletonDatatable(props: React.HTMLAttributes<HTMLDivElement>) {
    const mergedClassName = twMerge('space-y-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border', props.className);

    const rowCount = 10;
    const columnCount = 4;

    const renderCell = (key: string, height: string, extraClass?: string, patternStroke = 'stroke-neutral-900/20 dark:stroke-neutral-100/20') => (
        <Skeleton key={key} className={twMerge(`relative w-full ${height} overflow-hidden rounded-sm`, extraClass)}>
            <PlaceholderPattern className={twMerge('absolute inset-0 size-full', patternStroke)} />
        </Skeleton>
    );

    return (
        <div {...props} className={mergedClassName}>
            {/* Toolbar */}
            <div className="flex justify-end">
                <Skeleton className="relative h-10 w-32 overflow-hidden rounded-md">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </Skeleton>
            </div>

            {/* Header Skeleton */}
            <div className="grid grid-cols-4 gap-4 rounded-md bg-muted px-2 py-2">
                {[...Array(columnCount)].map((_, i) =>
                    renderCell(
                        `header-${i}`,
                        'h-10',
                        'bg-muted dark:bg-muted',
                        'stroke-neutral-700/30 dark:stroke-neutral-100/40', // darker stroke
                    ),
                )}
            </div>

            {/* Data Row Skeletons */}
            {[...Array(rowCount)].map((_, rowIndex) => (
                <div key={`row-${rowIndex}`} className="grid grid-cols-4 gap-4">
                    {[...Array(columnCount)].map((_, colIndex) => renderCell(`cell-${rowIndex}-${colIndex}`, 'h-10'))}
                </div>
            ))}

            {/* Pagination */}
            <div className="mt-6 flex items-center justify-end space-x-2">
                {[...Array(3)].map((_, i) => (
                    <Skeleton key={`pagination-${i}`} className="relative h-10 w-10 overflow-hidden rounded-md">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </Skeleton>
                ))}
            </div>
        </div>
    );
}
