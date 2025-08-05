'use client';
import ServerErrorAlert from '@/components/alerts/server-error-alert';
import CustomizableCombobox from '@/components/comboboxes/customizable-combobox';
import ConfirmDialog from '@/components/dialogs/confirm-dialog';
import InputCounter from '@/components/inputs/input-counter';
import FormLabelConditional from '@/components/labels/form-label-conditional';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useFormControlFlags } from '@/hooks/forms/computed/use-form-control-flags';
import { useFormDataStateFlags } from '@/hooks/forms/computed/use-form-data-state-flags';
import { useFormModeFlags } from '@/hooks/forms/computed/use-form-mode-flags';
import { FormConfirmActionType, FormProps } from '@/types/app';
import {
    OrganizationTag,
    organizationTagCharacterLimits,
    OrganizationTagCreateType,
    organizationTagPartialSchema,
    organizationTagSchema,
    OrganizationTagUpdateType,
} from '@/types/schema';
import { zodResolver } from '@hookform/resolvers/zod';
import { router } from '@inertiajs/react';
import { Loader2, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { SubmitErrorHandler, useForm } from 'react-hook-form';

type ConfirmActionType = Exclude<FormConfirmActionType, 'create' | 'update' | 'restore' | 'deactivate'>;

export default function OrgUnitManageTagForm({ mode, formKey, formData, onFormStateChange }: FormProps<OrganizationTag>) {
    // ============ STATE MANAGEMENT ============
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isConfirmAlertOpen, setIsConfirmAlertOpen] = useState(false);
    const [confirmActionType, setConfirmActionType] = useState<ConfirmActionType>('delete');

    // ============ CONSTANTS & COMPUTED VALUES ============
    const charLimits = useMemo(() => organizationTagCharacterLimits, []);

    const defaultValues: OrganizationTagCreateType | OrganizationTagUpdateType = useMemo(
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
        () =>
            formModeFlags.isEditOrManageMode
                ? {
                      ...defaultValues,
                      ...formData,
                      parent_tag_id: formData?.parent_tag?.id,
                  }
                : defaultValues,
        [formModeFlags.isEditOrManageMode, defaultValues, formData],
    );

    const form = useForm<OrganizationTagCreateType | OrganizationTagUpdateType>({
        disabled: controlFlags.disableForm,
        resolver: zodResolver(formModeFlags.isEditOrManageMode ? organizationTagPartialSchema : organizationTagSchema),
        defaultValues: resolvedDefaultValues,
    });

    const { formState, getValues } = form;

    // ============ EFFECTS ============

    // Form : dirty state listener
    useEffect(() => {
        if (onFormStateChange) {
            onFormStateChange(formState?.isDirty, mode);
        }
    }, [formState?.isDirty, mode, getValues, onFormStateChange]);

    // ============ EVENT HANDLERS ============

    const handleValidSubmit = useCallback(
        (data: OrganizationTagCreateType | OrganizationTagUpdateType) => {
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
                    route('v1.org.tags.update:put', { prefixedId: routeParams.modelIdentifier ?? routeParams.prefixedId }),
                    data,
                    visitOptions,
                );
            }

            return router.post(route('v1.org.tags.add:post'), data, visitOptions);
        },
        [formModeFlags.isEditOrManageMode, form, routeParams.modelIdentifier, routeParams.prefixedId],
    );

    const handleInvalidSubmit: SubmitErrorHandler<OrganizationTagCreateType | OrganizationTagUpdateType> = useCallback((errors) => {
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

        router.delete(route('v1.org.tags.delete:delete', routeParams.modelIdentifier ?? routeParams?.prefixedId), {
            onSuccess: () => setServerErrors({}),
            onError: (errors) => {
                console.error('Error permanent deleting:', errors);
                setServerErrors(errors);
            },
            onFinish: () => setIsConfirmAlertOpen(false),
        });
    }, [formModeFlags.isEditOrManageMode, routeParams.modelIdentifier, routeParams?.prefixedId]);

    const handleExecuteConfirmedAction = useCallback(() => {
        const actions = {
            delete: handlePermanentDelete,
        };
        actions[confirmActionType]();
    }, [confirmActionType, handlePermanentDelete]);

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
                                    <FormLabel>Tag Name</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.name} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="ex. Equipment" maxLength={charLimits.name} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>This is your organization's tag display name.</FormDescription>
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
                                    <FormLabel>Tag Code</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.code} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="ex. EQ001" maxLength={charLimits.code} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>This is your organization's tag unique code.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Parent TAG */}
                    <FormField
                        control={form.control}
                        name="parent_tag_id"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional>
                                    <FormLabel>Parent Tag</FormLabel>
                                </FormLabelConditional>
                                <CustomizableCombobox
                                    persistedValue={formData?.parent_tag?.id ?? ''}
                                    selectedValue={field.value ?? ''}
                                    onChange={(value) => {
                                        const converted = value?.trim() !== '' ? Number(value) : undefined;
                                        field.onChange(converted);
                                    }}
                                    dataSource={{
                                        queryKeys: ['tag', 'parent'],
                                        endpoint: route('v1.req.org.tags.options:post'),
                                        payload: {
                                            exclude: { prefixed_id: routeParams?.prefixedId },
                                            scopes: { notPointingBackTo: [routeParams?.prefixedId] },
                                        },
                                    }}
                                    labels={{
                                        selectedValuePlaceholder: 'No Parent (Top Level)',
                                    }}
                                    disableHoverableOption={true}
                                />
                                <FormDescription>
                                    Select the parent this tag belongs to. Leave it blank to make this a top-level tag with no parent.
                                </FormDescription>

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
