import { characterLimitForArray } from '@/configs/inputs';
import { OrganizationUnitEnum, type OrganizationUnitType } from '@/types/enums/organization_unit_enum';
import { z } from 'zod';
import type { CreatorOrgUserId, HeadOrgId, IdAndPrefixedId, ParentUnitId } from './default';

// =======================
// Type Definition
// =======================
export type OrganizationUnit = IdAndPrefixedId & {
    name: string;
    code: string;
    type: OrganizationUnitType;
    description: string;
    hierarchy: number;
    is_strict_hierarchy: boolean;
    is_active: boolean;
    parent_unit_id: ParentUnitId;
    head_org_user_id: HeadOrgId;
    creator_org_user_id: CreatorOrgUserId;
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
    description: z
        .string()
        .max(CHAR_LIMITS?.description, `Description must be less than ${CHAR_LIMITS?.description} characters`)
        .nullable()
        .optional(),
    type: z.enum(OrganizationUnitEnum),
    parent_unit_id: z.number().nullable().optional(),
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
export { CHAR_LIMITS as organizationUnitCharacterLimits, partialSchema as organizationUnitPartialSchema, schema as organizationUnitSchema };
export type { CreateType as OrganizationUnitCreateType, UpdateType as OrganizationUnitUpdateType };
