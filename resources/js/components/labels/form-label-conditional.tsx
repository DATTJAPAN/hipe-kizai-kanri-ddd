import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

type FormLabelConditionalProps = {
    required?: boolean;
    hideBadge?: boolean;
    children?: React.ReactNode;
    className?: string;
};

export default function FormLabelConditional({ required, hideBadge = false, children, className }: FormLabelConditionalProps) {
    return (
        <div className={cn('flex items-center justify-between', className)}>
            {children ?? 'Condition Form Label'}
            {!hideBadge && (
                <Badge
                    className={cn(
                        'rounded px-2 py-0.5 text-xs',
                        required
                            ? 'bg-red-500 text-white dark:bg-red-900/30 dark:text-red-400'
                            : 'bg-green-500 text-white dark:bg-green-900/30 dark:text-green-400',
                    )}
                >
                    {required ? 'Required' : 'Optional'}
                </Badge>
            )}
        </div>
    );
}
