import { Badge } from '@/components/ui/badge';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { Globe } from 'lucide-react';

export default function EnvironmentBadge() {
    const { env } = usePage<SharedData>().props;

    const getBadgeVariant = (environment: 'local' | 'staging' | 'production' | undefined): 'default' | 'destructive' | 'outline' | 'secondary' => {
        switch (environment?.toLowerCase()) {
            case 'production':
                return 'destructive';
            case 'staging':
                return 'outline';
            case 'local':
                return 'secondary';
            default:
                return 'default';
        }
    };

    return (
        <Badge variant={getBadgeVariant(env)} className="pointer-events-none">
            <Globe />
            {env?.toLowerCase() || 'Unknown'}
        </Badge>
    );
}
