'use client';
import CustomizableCombobox from '@/components/comboboxes/customizable-combobox';
import ConfirmDialog from '@/components/dialogs/confirm-dialog';
import InputCounter from '@/components/inputs/input-counter';
import FormLabelConditional from '@/components/labels/form-label-conditional';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useFormControlFlags } from '@/hooks/forms/computed/use-form-control-flags';
import { useFormDataStateFlags } from '@/hooks/forms/computed/use-form-data-state-flags';
import { useFormModeFlags } from '@/hooks/forms/computed/use-form-mode-flags';
import { FormMode } from '@/types/app';
import { OrganizationUnitEnum, orgUnitTypeOptions, orgUnitTypes } from '@/types/enums/organization_unit_enum';
import {
    organizationUnitCharacterLimits,
    OrganizationUnitCreateType,
    organizationUnitPartialSchema,
    organizationUnitSchema,
    OrganizationUnitUpdateType,
} from '@/types/schema';
import { zodResolver } from '@hookform/resolvers/zod';
import { router } from '@inertiajs/react';
import { AlertCircleIcon, Loader2, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { SubmitErrorHandler, useForm } from 'react-hook-form';

type FormProps = {
    mode: FormMode;
    formKey?: string | number;
    formData?: Record<string, unknown>;
    onFormStateChange?: (isDirty: boolean, mode: FormMode) => void;
};

type ConfirmActionType = 'delete' | 'deactivate' | 'restore';

export default function OrgUnitManageUnitForm({ mode, formKey, formData, onFormStateChange }: FormProps) {
    // ============ STATE MANAGEMENT ============
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isConfirmAlertOpen, setIsConfirmAlertOpen] = useState(false);
    const [confirmActionType, setConfirmActionType] = useState<ConfirmActionType>('delete');

    // ============ CONSTANTS & COMPUTED VALUES ============
    const charLimits = useMemo(() => organizationUnitCharacterLimits, []);

    const defaultValues = useMemo(() => ({}), []);

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
        () => (formModeFlags.isEditOrManageMode ? { ...defaultValues, ...formData } : defaultValues),
        [formModeFlags.isEditOrManageMode, defaultValues, formData],
    );

    const form = useForm<OrganizationUnitCreateType | OrganizationUnitUpdateType>({
        disabled: controlFlags.disableForm,
        resolver: zodResolver(formModeFlags.isEditOrManageMode ? organizationUnitPartialSchema : organizationUnitSchema),
        defaultValues: resolvedDefaultValues,
    });

    const { formState, setValue } = form;

    // ============ EFFECTS ============

    // Form dirty state listener
    useEffect(() => {
        if (onFormStateChange) {
            onFormStateChange(formState?.isDirty, mode);
        }
    }, [formState?.isDirty, mode, onFormStateChange]);

    // ============ EVENT HANDLERS ============

    const handleValidSubmit = useCallback(
        (data: OrganizationUnitCreateType | OrganizationUnitUpdateType) => {
            console.log('Form submitted with data:', data);
            setIsSubmitting(true);

            const commonCallbacks = {
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
                    route('v1.sys.orgs.update:put', { prefixedId: routeParams.modelIdentifier ?? routeParams.prefixedId }),
                    data,
                    commonCallbacks,
                );
            }

            return router.post(route('v1.sys.orgs.add:post'), data, commonCallbacks);
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
                console.error('Error perma deleting organization:', errors);
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
                                <FormControl>
                                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select a unit type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {orgUnitTypes.map(({ value, label, icon: Icon, color }) => (
                                                <SelectItem key={value} value={value} className={`flex items-center gap-2 ${color}`}>
                                                    <Icon className="h-4 w-4" />
                                                    {label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormDescription>This defines the structural category of the organization unit.</FormDescription>
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
                                    currentValue={field.value}
                                    initialValue={field.value}
                                    staticOptions={orgUnitTypeOptions}
                                    onChange={(value) => {
                                        setValue('type', value as OrganizationUnitEnum);
                                    }}
                                />
                                <FormDescription>This defines the structural category of the organization unit.</FormDescription>
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
                        {Object.keys(serverErrors).length > 0 && (
                            <Alert className="mt-4 bg-muted">
                                <AlertCircleIcon className="h-4 w-4 text-red-500 dark:text-red-400" />
                                <AlertTitle className="text-red-500 dark:text-red-400">Submission failed due to validation errors.</AlertTitle>
                                <AlertDescription>
                                    <ul className="mt-2 list-inside list-disc space-y-1 text-sm">
                                        {Object.entries(serverErrors).map(([key, message]) => (
                                            <li key={key}>
                                                <span className="font-medium text-muted-foreground capitalize dark:text-gray-400">
                                                    {key.replace(/_/g, ' ')}
                                                </span>
                                                {': '}
                                                <span className="text-red-600 dark:text-red-400">{message}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </AlertDescription>
                            </Alert>
                        )}
                    </div>
                </form>
            </Form>

            {/* Action Buttons */}
            {formModeFlags.isEditOrManageMode && !controlFlags.disableActionButtons && (
                <div className="flex max-w-2xl items-center justify-end space-x-2 pt-6">
                    {dataStateFlags.isCurrentlyActive && (
                        <Button
                            type="button"
                            onClick={() => handleOpenConfirmation('deactivate')}
                            className="w-full cursor-pointer"
                            variant="outline"
                            disabled={controlFlags.disableActionButtons || isSubmitting}
                        >
                            {isSubmitting ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <AlertCircleIcon className="mr-2 h-4 w-4" />}
                            Deactivate
                        </Button>
                    )}

                    {dataStateFlags.isCurrentlyInactive && (
                        <>
                            <Button
                                type="button"
                                onClick={() => handleOpenConfirmation('restore')}
                                className="w-full cursor-pointer"
                                variant="outline"
                                disabled={controlFlags.disableActionButtons || isSubmitting}
                            >
                                {isSubmitting ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <AlertCircleIcon className="mr-2 h-4 w-4" />}
                                Restore
                            </Button>

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
                        </>
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
