import { characterLimitForArray } from '@/configs/inputs';
import { z } from 'zod';
import type { IdAndPrefixedId } from './default';

// =======================
// Type Definition
// =======================
export type Organization = IdAndPrefixedId & {
    name: string;
    business_email: string;
    domain: string;
    alt_domains: string[];
    deleted_at?: Date | string | null;
};

// =======================
// Character Limits
// =======================
const CHAR_LIMITS = characterLimitForArray(['name', 'email', 'domain']);

// =======================
// Zod Schema
// =======================

const schema = z
    .object({
        name: z.string().trim().min(1, 'Name is required').max(CHAR_LIMITS?.name, `Name must be less than ${CHAR_LIMITS?.name} characters`),
        business_email: z
            .email()
            .trim()
            .min(1, 'Business Email is required')
            .max(CHAR_LIMITS?.email, `Business Email must be less than ${CHAR_LIMITS?.email} characters`),
        domain: z.string().trim().min(1, 'Domain is required').max(CHAR_LIMITS?.domain, `Domain must be less than ${CHAR_LIMITS?.domain} characters`),
        alt_domains: z
            .array(
                z
                    .string()
                    .trim()
                    .min(1, 'Alt Domain is required')
                    .max(CHAR_LIMITS?.domain, `Alt Domain must be less than ${CHAR_LIMITS?.domain} characters`)
                    .regex(z.regexes.domain, 'Invalid domain format (e.g., example.com)')
                    .transform((str) => str.toLowerCase()),
            )
            .max(5),
    })
    .refine((data) => !data.alt_domains.includes(data.domain.toLowerCase()), {
        path: ['alt_domains'],
        message: 'Alternative domains must not include the main domain.',
    })
    .refine((data) => new Set(data.alt_domains).size === data.alt_domains.length, {
        path: ['alt_domains'],
        message: 'Alternative domains must be unique.',
    });

const partialSchema = schema.partial();

// =======================
// Types from Schema`
// =======================
type CreateType = z.infer<typeof schema>;
type UpdateType = z.infer<typeof partialSchema>;

// =======================
// Export
// =======================
export { CHAR_LIMITS as organizationCharacterLimits, partialSchema as organizationPartialSchema, schema as organizationSchema };
export type { CreateType as OrganizationCreateType, UpdateType as OrganizationUpdateType };
