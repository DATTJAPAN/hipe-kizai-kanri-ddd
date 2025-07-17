import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { Globe, Shield, User } from 'lucide-react';

export default function ScopeBadge() {
    const { scope } = usePage<SharedData>().props;

    const getBadgeClassName = (scopeType: string | undefined): string => {
        switch (scopeType?.toLowerCase()) {
            case 'system':
                return 'bg-red-500 text-white border-red-500';
            case 'web':
                return 'bg-blue-500 text-white border-blue-500';
            default:
                return 'bg-gray-500 text-white border-gray-500';
        }
    };

    const getScopeIcon = (scopeType: string | undefined) => {
        const classSize = 'h-3 w-3';

        switch (scopeType?.toLowerCase()) {
            case 'system':
                return <Shield className={classSize} />;
            case 'web':
                return <User className={classSize} />;
            default:
                return <Globe className={classSize} />;
        }
    };

    return (
        <Badge variant="secondary" className={cn('pointer-events-none gap-1', getBadgeClassName(scope))}>
            {getScopeIcon(scope)}
            {scope?.toLowerCase() || 'unknown'}
        </Badge>
    );
}
