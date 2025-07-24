import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Skeleton } from '@/components/ui/skeleton';
import { twMerge } from 'tailwind-merge';

export default function SkeletonStats(props: React.HTMLAttributes<HTMLDivElement>) {
    return (
        <div {...props} className={twMerge('grid auto-rows-min gap-4 md:grid-cols-3', props.className)}>
            {[...Array(3)].map((_: unknown, i: number) => (
                <Skeleton
                    key={`skeleton-stats-${i}`}
                    className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </Skeleton>
            ))}
        </div>
    );
}
