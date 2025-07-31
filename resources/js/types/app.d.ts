export type AppEnvironment = 'local' | 'staging' | 'production';
export type GuardScope = 'web' | 'system';
export type FormMode = 'create' | 'edit' | 'manage' | 'unknown' | 'view';

export interface AuthScope {
    scope?: GuardScope;
}

export interface FormContext {
    form?: {
        mode: FormMode;
        // only for edit or manage
        key?: string | number; // Subtle name for unique ID, inferred by type
        key_type?: 'generated' | 'sequence'; // Clarifies generation method
        key_val_type?: 'string' | 'number'; // Enables type-safe handling
        data?: Record<string, unknown>;
    };
}

export type FormProps<T = Record<string, unknown>> = {
    mode: FormMode;
    formKey?: string | number;
    formData?: T;
    onFormStateChange?: (isDirty: boolean, mode: FormMode) => void;
};

export type FormConfirmActionType = 'create' | 'update' | 'delete' | 'deactivate' | 'restore';
