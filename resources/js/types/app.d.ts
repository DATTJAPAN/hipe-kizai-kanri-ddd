export type AppEnvironment = 'local' | 'staging' | 'production';
export type GuardScope = 'web' | 'system';

export interface AuthScope {
    scope?: GuardScope;
}
