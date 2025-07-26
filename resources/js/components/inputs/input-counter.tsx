import { cn } from '@/lib/utils';
import React from 'react';

type InputCounterProps = {
    value: string;
    maxLength: number;
    children: React.ReactNode;
};

export default function InputCounter({ value, maxLength, children }: InputCounterProps) {
    const length = value?.length || 0;
    const remaining = maxLength - length;

    const isOver = remaining < 0;
    const isWarning = length > 0 && remaining <= Math.floor(maxLength * 0.3);

    const message = isOver ? 'Over' : `${remaining}`;

    const counterColor = isOver
        ? 'bg-red-500 text-white dark:bg-red-900/30 dark:text-red-400'
        : isWarning
          ? 'bg-yellow-500 text-white dark:bg-yellow-900/30 dark:text-yellow-300'
          : 'border-transparent bg-muted/90 text-muted-foreground dark:border-stone-500';

    return (
        <div className="relative w-full">
            {children}
            <div
                className={cn(
                    'pointer-events-none absolute top-1/2 right-2 -translate-y-1/2 rounded-md border px-2 py-0.5 text-xs font-medium backdrop-blur-sm',
                    counterColor,
                )}
            >
                {message}
            </div>
        </div>
    );
}
