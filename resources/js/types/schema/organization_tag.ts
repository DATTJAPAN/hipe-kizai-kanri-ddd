import { characterLimitForArray } from '@/configs/inputs';
import { z } from 'zod';
import type { CreatorOrgUserId, IdAndPrefixedId } from './default';

// =======================
// Type Definition
// =======================
export type OrganizationTag = IdAndPrefixedId & {
    name: string;
    code: string;
    creator_org_user_id?: CreatorOrgUserId;
    parent_tag?: OrganizationTag | null;
};

// =======================
// Character Limits
// =======================
const CHAR_LIMITS: Record<string, number> = characterLimitForArray(['name', 'code', 'description']);

// =======================
// Zod Schema
// =======================
const schema = z.object({
    name: z.string().trim().min(1, 'Name is required').max(CHAR_LIMITS?.name, `Name must be less than ${CHAR_LIMITS?.name} characters`),
    code: z.string().trim().min(1, 'Code is required').max(CHAR_LIMITS?.code, `Code must be less than ${CHAR_LIMITS?.code} characters`),
    parent_tag_id: z.number().nullable().optional(),
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
export { CHAR_LIMITS as organizationTagCharacterLimits, partialSchema as organizationTagPartialSchema, schema as organizationTagSchema };
export type { CreateType as OrganizationTagCreateType, UpdateType as OrganizationTagUpdateType };
