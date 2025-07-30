export interface QueryOptions {
    queryKey?: string[];
    url?: string;
    withTrashed?: boolean;
    onlyTrashed?: boolean;
    additionalData?: Record<string, unknown>;
}
