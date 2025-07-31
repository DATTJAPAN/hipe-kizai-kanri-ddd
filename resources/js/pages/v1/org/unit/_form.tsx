'use client';
import ServerErrorAlert from '@/components/alerts/server-error-alert';
import CustomizableCombobox from '@/components/comboboxes/customizable-combobox';
import ConfirmDialog from '@/components/dialogs/confirm-dialog';
import InputCounter from '@/components/inputs/input-counter';
import FormLabelConditional from '@/components/labels/form-label-conditional';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useFormControlFlags } from '@/hooks/forms/computed/use-form-control-flags';
import { useFormDataStateFlags } from '@/hooks/forms/computed/use-form-data-state-flags';
import { useFormModeFlags } from '@/hooks/forms/computed/use-form-mode-flags';
import { FormConfirmActionType, FormMode } from '@/types/app';
import { orgUnitTypeOptions } from '@/types/enums/organization_unit_enum';
import {
    organizationUnitCharacterLimits,
    OrganizationUnitCreateType,
    organizationUnitPartialSchema,
    organizationUnitSchema,
    OrganizationUnitUpdateType,
} from '@/types/schema';
import { zodResolver } from '@hookform/resolvers/zod';
import { router } from '@inertiajs/react';
import { Loader2, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { SubmitErrorHandler, useForm } from 'react-hook-form';

type FormProps = {
    mode: FormMode;
    formKey?: string | number;
    formData?: Record<string, unknown>;
    onFormStateChange?: (isDirty: boolean, mode: FormMode) => void;
};

type ConfirmActionType = Exclude<FormConfirmActionType, 'create' | 'update'>;

export default function OrgUnitManageUnitForm({ mode, formKey, formData, onFormStateChange }: FormProps) {
    // ============ STATE MANAGEMENT ============
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isConfirmAlertOpen, setIsConfirmAlertOpen] = useState(false);
    const [confirmActionType, setConfirmActionType] = useState<ConfirmActionType>('delete');

    // ============ CONSTANTS & COMPUTED VALUES ============
    const charLimits = useMemo(() => organizationUnitCharacterLimits, []);

    const defaultValues: OrganizationUnitCreateType | OrganizationUnitUpdateType = useMemo(
        () => ({
            name: '',
            code: '',
            description: '',
            type: undefined,
            parent_unit_id: undefined,
        }),
        [],
    );

    // Form mode flags
    const formModeFlags = useFormModeFlags({ mode, formData });

    // Data state flags
    const dataStateFlags = useFormDataStateFlags({ hasData: formModeFlags.hasData, formData });

    // Control flags
    const controlFlags = useFormControlFlags({ mode, isCurrentlyInactive: dataStateFlags.isCurrentlyInactive });
    console.log(formModeFlags, dataStateFlags, controlFlags);
    // Route params
    const routeParams = useMemo(() => {
        const { prefixedId } = route().params;

        return {
            prefixedId,
            modelIdentifier: prefixedId ?? formKey ?? null,
        };
    }, [formKey]);

    // ============ FORM SETUP ============
    const resolvedDefaultValues = useMemo(
        () => (formModeFlags.isEditOrManageMode ? { ...defaultValues, ...formData } : defaultValues),
        [formModeFlags.isEditOrManageMode, defaultValues, formData],
    );

    const form = useForm<OrganizationUnitCreateType | OrganizationUnitUpdateType>({
        disabled: controlFlags.disableForm,
        resolver: zodResolver(formModeFlags.isEditOrManageMode ? organizationUnitPartialSchema : organizationUnitSchema),
        defaultValues: resolvedDefaultValues,
    });

    const { formState, getValues } = form;

    // ============ EFFECTS ============

    // Form dirty state listener
    useEffect(() => {
        if (onFormStateChange) {
            onFormStateChange(formState?.isDirty, mode);
        }
    }, [formState?.isDirty, mode, getValues, onFormStateChange]);

    // ============ EVENT HANDLERS ============

    const handleValidSubmit = useCallback(
        (data: OrganizationUnitCreateType | OrganizationUnitUpdateType) => {
            console.log('Form submitted with data:', data);
            setIsSubmitting(true);

            const visitOptions = {
                onSuccess: () => {
                    setServerErrors({});
                    form.reset(data);
                    setIsSubmitting(false);
                },
                onError: (errors: Record<string, string>) => {
                    console.error('Form submission errors:', errors);
                    setServerErrors(errors);
                    setIsSubmitting(false);
                },
                onFinish: () => {
                    setIsSubmitting(false);
                },
            };

            if (formModeFlags.isEditOrManageMode) {
                return router.put(
                    route('v1.org.units.update::put', { prefixedId: routeParams.modelIdentifier ?? routeParams.prefixedId }),
                    data,
                    visitOptions,
                );
            }

            return router.post(route('v1.org.units.add:post'), data, visitOptions);
        },
        [formModeFlags.isEditOrManageMode, form, routeParams.modelIdentifier, routeParams.prefixedId],
    );

    const handleInvalidSubmit: SubmitErrorHandler<OrganizationUnitCreateType | OrganizationUnitUpdateType> = useCallback((errors) => {
        console.error('Form submission errors:', errors);
    }, []);

    const handleFormSubmit = useCallback(
        (e: FormEvent) => {
            e.preventDefault();
            if (controlFlags.disableForm || isSubmitting) return;
            form.handleSubmit(handleValidSubmit, handleInvalidSubmit)();
        },
        [controlFlags.disableForm, isSubmitting, form, handleValidSubmit, handleInvalidSubmit],
    );

    // ============ CONFIRMATION DIALOG HANDLERS ============
    const handleOpenConfirmation = useCallback((type: ConfirmActionType) => {
        setConfirmActionType(type);
        setIsConfirmAlertOpen(true);
    }, []);

    const handlePermanentDelete = useCallback(() => {
        if (!formModeFlags.isEditOrManageMode) return;

        router.delete(route('v1.sys.orgs.force_delete:delete', routeParams.modelIdentifier ?? routeParams?.prefixedId), {
            onSuccess: () => setServerErrors({}),
            onError: (errors) => {
                console.error('Error permanent deleting:', errors);
                setServerErrors(errors);
            },
            onFinish: () => setIsConfirmAlertOpen(false),
        });
    }, [formModeFlags.isEditOrManageMode, routeParams.modelIdentifier, routeParams?.prefixedId]);

    const handleDeactivate = useCallback(() => {
        if (!formModeFlags.isEditOrManageMode) return;

        router.delete(route('v1.sys.orgs.soft_delete:delete', routeParams.modelIdentifier ?? routeParams?.prefixedId), {
            onSuccess: () => setServerErrors({}),
            onError: (errors) => {
                console.error('Error deleting organization:', errors);
                setServerErrors(errors);
            },
            onFinish: () => setIsConfirmAlertOpen(false),
        });
    }, [formModeFlags.isEditOrManageMode, routeParams.modelIdentifier, routeParams?.prefixedId]);

    const handleRestore = useCallback(() => {
        if (!formModeFlags.isEditOrManageMode) return;

        router.patch(
            route('v1.sys.orgs.restore:patch', routeParams.modelIdentifier ?? routeParams?.prefixedId),
            {},
            {
                onSuccess: () => setServerErrors({}),
                onError: (errors) => {
                    console.error('Error restoring organization:', errors);
                    setServerErrors(errors);
                },
                onFinish: () => setIsConfirmAlertOpen(false),
            },
        );
    }, [formModeFlags.isEditOrManageMode, routeParams.modelIdentifier, routeParams?.prefixedId]);

    const handleExecuteConfirmedAction = useCallback(() => {
        const actions = {
            delete: handlePermanentDelete,
            deactivate: handleDeactivate,
            restore: handleRestore,
        };
        actions[confirmActionType]();
    }, [confirmActionType, handleDeactivate, handlePermanentDelete, handleRestore]);

    // ============ UTILITY FUNCTIONS ============
    const getButtonText = useCallback(() => {
        if (isSubmitting) {
            return formModeFlags.isEditOrManageMode ? 'Saving...' : 'Submitting...';
        }
        if (formState.isDirty && mode !== 'create') {
            return 'Save Changes';
        }
        return 'Submit';
    }, [isSubmitting, formModeFlags.isEditOrManageMode, formState.isDirty, mode]);

    // ============ RENDER ============
    return (
        <>
            <Form {...form}>
                <form onSubmit={handleFormSubmit} className="space-y-8">
                    {/* Name Field */}
                    <FormField
                        control={form.control}
                        name="name"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Unit Name</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.name} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="ex. AI Division " maxLength={charLimits.name} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>This is your organization's unit display name.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Code Field */}
                    <FormField
                        control={form.control}
                        name="code"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Unit Code</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.code} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="ex. AIDIV001" maxLength={charLimits.code} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>This is your organization's unit unique code.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Unit Type */}
                    <FormField
                        control={form.control}
                        name="type"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Unit Type</FormLabel>
                                </FormLabelConditional>
                                <CustomizableCombobox
                                    selectedValue={field.value}
                                    staticOptions={orgUnitTypeOptions}
                                    disableDeSelectingOption
                                    onChange={(value) => field.onChange(value === '' ? undefined : value)}
                                />
                                <FormDescription>This defines the structural category of the organization unit.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Parent Unit */}
                    <FormField
                        control={form.control}
                        name="parent_unit_id"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Parent Unit</FormLabel>
                                </FormLabelConditional>
                                <CustomizableCombobox
                                    selectedValue={field.value ?? ''}
                                    persistedValue={''}
                                    onChange={(value) => {
                                        const converted = typeof value === 'string' && value.trim() !== '' ? Number(value) : undefined;
                                        field.onChange(converted);
                                    }}
                                    dataSource={{
                                        queryKeys: ['unit', 'parent'],
                                        endpoint: route('v1.req.org.units.options:post'),
                                    }}
                                    labels={{
                                        selectedValuePlaceholder: 'No Parent (Top Level)',
                                    }}
                                    disableHoverableOption={true}
                                />
                                <FormDescription>
                                    Select the parent unit this unit belongs to. Leave it blank to make this a top-level unit with no parent.
                                </FormDescription>

                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Descriptions Field */}
                    <FormField
                        control={form.control}
                        name="description"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional>
                                    <FormLabel>Unit Description</FormLabel>
                                </FormLabelConditional>
                                <FormControl>
                                    <Textarea placeholder="Unit description here..." rows={4} {...field} value={field.value ?? ''} />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Submit Button */}
                    {(formModeFlags.isCreateMode || dataStateFlags.isCurrentlyActive) && (
                        <div className="flex max-w-2xl items-center justify-end space-x-2 pt-6">
                            <Button
                                type="submit"
                                className="w-full cursor-pointer"
                                role="button"
                                disabled={controlFlags.disableFormSubmit || !formState?.isDirty || isSubmitting}
                            >
                                {isSubmitting ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <Plus className="mr-2 h-4 w-4" />}
                                {getButtonText()}
                            </Button>
                        </div>
                    )}

                    {/* Error Alert */}
                    <div className="max-w-2xl">
                        <ServerErrorAlert errors={serverErrors} />
                    </div>
                </form>
            </Form>

            {/* Action Buttons */}
            {formModeFlags.isEditOrManageMode && !controlFlags.disableActionButtons && (
                <div className="flex max-w-2xl items-center justify-end space-x-2 pt-6">
                    {dataStateFlags.isCurrentlyActive && (
                        <Button
                            type="button"
                            onClick={() => handleOpenConfirmation('delete')}
                            className="w-full cursor-pointer"
                            variant="destructive"
                            disabled={controlFlags.disableActionButtons || isSubmitting}
                        >
                            {isSubmitting ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <Trash2 className="mr-2 h-4 w-4" />}
                            Permanently delete
                        </Button>
                    )}
                </div>
            )}

            {/* Confirmation Dialog */}
            {formModeFlags.isEditOrManageMode && !controlFlags.disableActionButtons && (
                <ConfirmDialog open={isConfirmAlertOpen} onOpenChange={setIsConfirmAlertOpen} onConfirm={handleExecuteConfirmedAction} />
            )}
        </>
    );
}
