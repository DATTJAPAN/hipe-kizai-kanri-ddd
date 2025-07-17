import EnvironmentBadge from '@/components/badges/environment-badge';
import ScopeBadge from '@/components/badges/scope-badge';
import { clsx } from 'clsx';

export function AppSidebarHeaderIndicator() {
    return (
        <div
            className={clsx(
                // Flexbox layout
                'flex h-auto shrink-0 items-center justify-end gap-2 py-1.5',
                // Border styling
                'border-b border-sidebar-border/50',
                // Responsive padding
                'px-6 md:px-4',
                // Animation
                'transition-[width,height] ease-linear',
                // Conditional styling
                'group-has-data-[collapsible=icon]/sidebar-wrapper:h-auto',
            )}
        >
            {/*Display Environment*/}
            <EnvironmentBadge />
            {/*Display Scope*/}
            <ScopeBadge />
        </div>
    );
}
