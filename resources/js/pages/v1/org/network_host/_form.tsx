'use client';
import ServerErrorAlert from '@/components/alerts/server-error-alert';
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
    OrganizationNetworkHost,
    organizationNetworkHostCharacterLimits,
    OrganizationNetworkHostCreateType,
    organizationNetworkHostPartialSchema,
    organizationNetworkHostSchema,
    OrganizationNetworkHostUpdateType,
} from '@/types/schema';
import { zodResolver } from '@hookform/resolvers/zod';
import { router } from '@inertiajs/react';
import { Loader2, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { SubmitErrorHandler, useForm } from 'react-hook-form';

type ConfirmActionType = Exclude<FormConfirmActionType, 'create' | 'update' | 'restore' | 'deactivate'>;

export default function OrgNetworkHostManageNetworkHostForm({ mode, formKey, formData, onFormStateChange }: FormProps<OrganizationNetworkHost>) {
    // ============ STATE MANAGEMENT ============
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isConfirmAlertOpen, setIsConfirmAlertOpen] = useState(false);
    const [confirmActionType, setConfirmActionType] = useState<ConfirmActionType>('delete');

    // ============ CONSTANTS & COMPUTED VALUES ============
    const charLimits = useMemo(() => organizationNetworkHostCharacterLimits, []);

    const defaultValues: OrganizationNetworkHostCreateType | OrganizationNetworkHostUpdateType = useMemo(
        () => ({
            name: '',
            host_address: '',
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
                  }
                : defaultValues,
        [formModeFlags.isEditOrManageMode, defaultValues, formData],
    );

    const form = useForm<OrganizationNetworkHostCreateType | OrganizationNetworkHostUpdateType>({
        disabled: controlFlags.disableForm,
        resolver: zodResolver(formModeFlags.isEditOrManageMode ? organizationNetworkHostPartialSchema : organizationNetworkHostSchema),
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
        (data: OrganizationNetworkHostCreateType | OrganizationNetworkHostUpdateType) => {
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
                    route('v1.org.network_hosts.update:put', { prefixedId: routeParams.modelIdentifier ?? routeParams.prefixedId }),
                    data,
                    visitOptions,
                );
            }

            return router.post(route('v1.org.network_hosts.add:post'), data, visitOptions);
        },
        [formModeFlags.isEditOrManageMode, form, routeParams.modelIdentifier, routeParams.prefixedId],
    );

    const handleInvalidSubmit: SubmitErrorHandler<OrganizationNetworkHostCreateType | OrganizationNetworkHostUpdateType> = useCallback((errors) => {
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

        router.delete(route('v1.org.network_hosts.delete:delete', routeParams.modelIdentifier ?? routeParams?.prefixedId), {
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
                                    <FormLabel>Host Name</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.name} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="ex. Network Host - 1" maxLength={charLimits.name} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>This is your organization's network host display name.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Host Address Field */}
                    <FormField
                        control={form.control}
                        name="host_address"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Host Address</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.host_cidr} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input
                                            placeholder="ex. 192.168.0.0/24"
                                            maxLength={charLimits.host_cidr}
                                            {...field}
                                            value={field.value ?? ''}
                                        />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>Enter in CIDR notation, e.g., 192.168.0.0/24</FormDescription>
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
