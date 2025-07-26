import { OrganizationCreateType, organizationPartialSchema, organizationSchema, OrganizationUpdateType } from '@/types/schema/organization';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';

type UseResolveFormOptions =
    | {
          mode: 'create';
          defaultValues?: Partial<OrganizationCreateType>;
      }
    | {
          mode: 'update';
          defaultValues: Partial<OrganizationUpdateType>;
      };

const useResolveForm = ({ mode, defaultValues = {} }: UseResolveFormOptions) => {
    return useForm<OrganizationCreateType | OrganizationUpdateType>({
        resolver: zodResolver(mode === 'create' ? organizationSchema : organizationPartialSchema),
        defaultValues,
    });
};

export { useResolveForm as useSystemOrganizationForm };
export type { UseResolveFormOptions };
