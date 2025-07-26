export type AppEnvironment = 'local' | 'staging' | 'production';
export type GuardScope = 'web' | 'system';

export interface AuthScope {
    scope?: GuardScope;
}

export interface FormContext {
    form?: {
        key: string | number; // Subtle name for unique ID, inferred by type
        key_type: 'generated' | 'sequence'; // Clarifies generation method
        key_val_type: 'string' | 'number'; // Enables type-safe handling
        data: Record<string, unknown>;
    };
}
