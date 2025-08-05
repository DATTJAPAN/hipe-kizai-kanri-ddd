import { characterLimitForArray } from '@/configs/inputs';
import { z } from 'zod';
import type { CreatorOrgUserId, IdAndPrefixedId } from './default';

// =======================
// Type Definition
// =======================
export type OrganizationNetworkHost = IdAndPrefixedId & {
    name: string;
    host_address: string;
    cidr: number;
    creator_org_user_id?: CreatorOrgUserId;
};

// =======================
// Character Limits
// =======================
const CHAR_LIMITS: Record<string, number> = characterLimitForArray(['name', 'host_cidr']);

// =======================
// Zod Schema
// =======================
const schema = z.object({
    name: z.string().trim().min(1, 'Name is required').max(CHAR_LIMITS?.name, `Name must be less than ${CHAR_LIMITS?.name} characters`),
    host_address: z.cidrv4(),
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
    CHAR_LIMITS as organizationNetworkHostCharacterLimits,
    partialSchema as organizationNetworkHostPartialSchema,
    schema as organizationNetworkHostSchema,
};
export type { CreateType as OrganizationNetworkHostCreateType, UpdateType as OrganizationNetworkHostUpdateType };
