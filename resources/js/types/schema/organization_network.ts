import { characterLimitForArray } from '@/configs/inputs';
import { z } from 'zod';
import type { CreatorOrgUserId, IdAndPrefixedId } from './default';

// =======================
// Type Definition
// =======================
export type OrganizationNetwork = IdAndPrefixedId & {
    name: string;
    creator_org_user_id?: CreatorOrgUserId;
};

// =======================
// Character Limits
// =======================
const CHAR_LIMITS: Record<string, number> = characterLimitForArray(['name', 'ip']);

// =======================
// Zod Schema
// =======================
const schema = z.object({
    name: z.string().trim().min(1, 'Name is required').max(CHAR_LIMITS?.name, `Name must be less than ${CHAR_LIMITS?.name} characters`),
    description: z
        .string()
        .max(CHAR_LIMITS?.description, `Description must be less than ${CHAR_LIMITS?.description} characters`)
        .nullable()
        .optional(),
});

const partialSchema = schema.partial();

// =======================
// Types from Schema
// =======================
type CreateType = z.infer<typeof schema>;
type UpdateType = z.infer<typeof partialSchema>;

// =======================
// Export
// =======================
export {
    CHAR_LIMITS as organizationLocationCharacterLimits,
    partialSchema as organizationLocationPartialSchema,
    schema as organizationLocationSchema,
};
export type { CreateType as OrganizationLocationCreateType, UpdateType as OrganizationLocationUpdateType };
