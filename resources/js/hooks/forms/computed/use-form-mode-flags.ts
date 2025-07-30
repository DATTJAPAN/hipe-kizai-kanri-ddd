import { FormMode } from '@/types/app';
import { useMemo } from 'react';

/**
 * Props for the useFormModeFlags hook
 */
interface UseFormModeFlagsProps<TFormData = Record<string, unknown>> {
    /** The current form mode */
    mode: FormMode;
    /** The form data object */
    formData: TFormData | null | undefined;
}

/**
 * Form mode flag values returned by the hook
 */
interface FormModeFlags {
    /** Whether the form is in create mode */
    isCreateMode: boolean;
    /** Whether the form is in manage mode */
    isManageMode: boolean;
    /** Whether the form is in either edit or manage mode */
    isEditOrManageMode: boolean;
    /** Whether the form has data */
    hasData: boolean;
}

/**
 * Hook to derive form mode flags based on the current mode and form data
 * @param params - The hook parameters
 * @returns Form mode flags
 */
export function useFormModeFlags<TFormData = Record<string, unknown>>({ mode, formData }: UseFormModeFlagsProps<TFormData>): FormModeFlags {
    return useMemo(
        () => ({
            isCreateMode: mode === 'create',
            isManageMode: mode === 'manage',
            isEditOrManageMode: mode === 'edit' || mode === 'manage',
            hasData: formData !== null && formData !== undefined && Object.keys(formData as object).length > 0,
        }),
        [mode, formData],
    );
}
