import { useMemo } from 'react';

/**
 * Props for the useFormDataStateFlags hook
 */
interface UseFormDataStateFlagsProps<TFormData = Record<string, unknown>> {
    /** Whether the form has data */
    hasData: boolean;
    /** The form data object */
    formData: TFormData | null | undefined;
}

/**
 * Data state flags returned by the hook
 */
interface DataStateFlags {
    /** Whether the entity uses soft delete (has deleted_at field) */
    usesSoftDelete: boolean;
    /** Whether the entity is currently active (not soft-deleted) */
    isCurrentlyActive: boolean;
    /** Whether the entity is currently inactive (soft-deleted) */
    isCurrentlyInactive: boolean;
}

/**
 * Type guard to check if an object has a deleted_at property
 */
function hasSoftDelete(obj: unknown): obj is { deleted_at: unknown } {
    return obj !== null && typeof obj === 'object' && 'deleted_at' in obj;
}

/**
 * Hook to determine data state flags based on the form data
 * @param params - The hook parameters
 * @returns Data state flags
 */
export function useFormDataStateFlags<TFormData = Record<string, unknown>>({
    hasData,
    formData,
}: UseFormDataStateFlagsProps<TFormData>): DataStateFlags {
    return useMemo(() => {
        // Use type guard instead of Object.hasOwn to properly handle null
        const usesSoftDelete: boolean = hasData && formData !== undefined && hasSoftDelete(formData);

        return {
            usesSoftDelete,
            isCurrentlyActive: usesSoftDelete && hasSoftDelete(formData) && formData.deleted_at === null,
            isCurrentlyInactive: usesSoftDelete && hasSoftDelete(formData) && formData.deleted_at !== null,
        };
    }, [hasData, formData]);
}
