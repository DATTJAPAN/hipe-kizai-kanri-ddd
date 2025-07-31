import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircleIcon } from 'lucide-react';

type ServerErrorAlertProps = {
    errors: Record<string, string>;
};

export default function ServerErrorAlert({ errors }: ServerErrorAlertProps) {
    if (Object.keys(errors).length === 0) return null;

    return (
        <Alert className="mt-4 bg-muted">
            <AlertCircleIcon className="h-4 w-4 text-red-500 dark:text-red-400" />
            <AlertTitle className="text-red-500 dark:text-red-400">Submission failed due to validation errors.</AlertTitle>
            <AlertDescription>
                <ul className="mt-2 list-inside list-disc space-y-1 text-sm">
                    {Object.entries(errors).map(([key, message]) => (
                        <li key={key}>
                            <span className="font-medium text-muted-foreground capitalize dark:text-gray-400">{key.replace(/_/g, ' ')}</span>
                            {': '}
                            <span className="text-red-600 dark:text-red-400">{message}</span>
                        </li>
                    ))}
                </ul>
            </AlertDescription>
        </Alert>
    );
}
