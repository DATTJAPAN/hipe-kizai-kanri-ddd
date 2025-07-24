import type { IdAndPrefixedId } from './default';

export type Organization = IdAndPrefixedId & {
    name: string;
    business_email: string;
    domain: string;
    alt_domains: string[];
};
