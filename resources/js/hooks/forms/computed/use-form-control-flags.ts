import { FormMode } from '@/types/app';
import { useMemo } from 'react';

/**
 * Props for the useFormControlFlags hook
 */
interface useFormControlFlagsProps {
    /** The current form mode */
    mode: FormMode;
    /** Whether the entity is currently inactive (soft-deleted) */
    isCurrentlyInactive: boolean;
}

/**
 * Control flags returned by the hook
 */
interface ControlFlags {
    /** Whether the form should be disabled */
    disableForm: boolean;
    /** Whether action buttons should be disabled */
    disableActionButtons: boolean;
    /** Whether the form submit should be disabled */
    disableFormSubmit: boolean;
}

/**
 * Hook to determine form control flags based on mode and data state
 * @param params - The hook parameters
 * @returns Control flags
 */
export function useFormControlFlags({ mode, isCurrentlyInactive }: useFormControlFlagsProps): ControlFlags {
    return useMemo(
        () => ({
            disableForm: mode === 'unknown' || isCurrentlyInactive,
            disableActionButtons: mode === 'unknown',
            disableFormSubmit: mode === 'unknown' || isCurrentlyInactive,
        }),
        [mode, isCurrentlyInactive],
    );
}
