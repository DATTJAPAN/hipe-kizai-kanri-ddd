import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const data_get = <T, R = unknown>(obj: T, path: string, defaultValue: R = null as R): R => {
    if (!obj || typeof obj !== 'object') return defaultValue;

    const [currentKey, ...remainingKeys] = path.split('.'); // Split the path into the first key and the rest

    if (Object.hasOwn(obj as Record<string, unknown>, currentKey)) {
        const currentValue = (obj as Record<string, unknown>)[currentKey];

        // If there are more keys to traverse, call dataGet recursively
        if (remainingKeys.length > 0) {
            return data_get(currentValue, remainingKeys.join('.'), defaultValue);
        }

        // If this is the last key, return the value or defaultValue
        return currentValue !== undefined && currentValue !== null ? (currentValue as R) : defaultValue;
    }

    return defaultValue;
};
