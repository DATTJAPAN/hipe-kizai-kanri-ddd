import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

export type BadgePreset = 'default' | 'create' | 'edit' | 'view' | 'manage' | 'unknown';

type LabelConditionalProps = {
    badgePreset?: BadgePreset;
    hideBadge?: boolean;
    showDirtyBadge?: boolean;
    showDirtyBadgeText?: string;
    children?: React.ReactNode;
    className?: string;
};

export default function LabelConditional({
    badgePreset = 'default',
    hideBadge = false,
    showDirtyBadge = false,
    showDirtyBadgeText = 'Unsaved Changes',
    children,
    className,
}: LabelConditionalProps) {
    const DEFAULT_CHILD = <h2 className="mb-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Label Here</h2>;

    const getBadgeConfig = (preset: BadgePreset) => {
        switch (preset) {
            case 'create':
                return {
                    text: 'Create',
                    className: 'bg-blue-500 text-white dark:bg-blue-900/30 dark:text-blue-400',
                };
            case 'edit':
                return {
                    text: 'Edit',
                    className: 'bg-orange-500 text-white dark:bg-orange-900/30 dark:text-orange-400',
                };
            case 'view':
                return {
                    text: 'View',
                    className: 'bg-gray-500 text-white dark:bg-gray-900/30 dark:text-gray-400',
                };
            case 'manage':
                return {
                    text: 'Manage',
                    className: 'bg-purple-500 text-white dark:bg-purple-900/30 dark:text-purple-400',
                };
            case 'unknown':
                return {
                    text: 'Unknown',
                    className: 'bg-red-500 text-white dark:bg-red-900/30 dark:text-red-400',
                };
            case 'default':
            default:
                return {
                    text: 'Default',
                    className: 'bg-green-500 text-white dark:bg-green-900/30 dark:text-green-400',
                };
        }
    };

    const badgeConfig = getBadgeConfig(badgePreset);

    const dirtyBadgeConfig = {
        text: showDirtyBadgeText,
        className: 'bg-yellow-500 text-white dark:bg-yellow-900/30 dark:text-yellow-400',
    };

    return (
        <div className={cn('flex items-center justify-between', className)}>
            {children ?? DEFAULT_CHILD}
            <div className="flex items-center gap-2">
                {showDirtyBadge && <Badge className={cn('rounded px-2 py-0.5 text-xs', dirtyBadgeConfig.className)}>{dirtyBadgeConfig.text}</Badge>}
                {!hideBadge && <Badge className={cn('rounded px-2 py-0.5 text-xs', badgeConfig.className)}>{badgeConfig.text}</Badge>}
            </div>
        </div>
    );
}
