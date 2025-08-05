'use client';
import ConfirmDialog from '@/components/dialogs/confirm-dialog';
import InputCounter from '@/components/inputs/input-counter';
import FormLabelConditional from '@/components/labels/form-label-conditional';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { FormMode } from '@/types/app';
import {
    organizationCharacterLimits,
    OrganizationCreateType,
    organizationPartialSchema,
    organizationSchema,
    OrganizationUpdateType,
} from '@/types/schema';
import { zodResolver } from '@hookform/resolvers/zod';
import { router } from '@inertiajs/react';
import { AlertCircleIcon, LinkIcon, Loader2, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { SubmitErrorHandler, useFieldArray, useForm } from 'react-hook-form';

type FormProps = {
    mode: FormMode;
    formKey?: string | number;
    formData?: Record<string, unknown>;
    onFormStateChange?: (isDirty: boolean, mode: FormMode) => void;
};

type ConfirmActionType = 'delete' | 'deactivate' | 'restore';

export default function SysOrgManageForm({ mode, formKey, formData, onFormStateChange }: FormProps) {
    // ============ STATE MANAGEMENT ============
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});
    const [syncDomainWithEmail, setSyncDomainWithEmail] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isConfirmAlertOpen, setIsConfirmAlertOpen] = useState(false);
    const [confirmActionType, setConfirmActionType] = useState<ConfirmActionType>('delete');

    // ============ CONSTANTS & COMPUTED VALUES ============
    const charLimits = useMemo(() => organizationCharacterLimits, []);

    const defaultValues: OrganizationCreateType | OrganizationUpdateType = useMemo(
        () => ({
            name: '',
            business_email: '',
            domain: '',
            alt_domains: [] as string[],
        }),
        [],
    );

    // Form mode flags
    const formModeFlags = useMemo(
        () => ({
            isCreateMode: mode === 'create',
            isManageMode: mode === 'manage',
            isEditOrManageMode: mode === 'edit' || mode === 'manage',
            hasData: formData !== null && formData !== undefined && Object.keys(formData).length > 0,
        }),
        [mode, formData],
    );

    // Data state flags
    const dataStateFlags = useMemo(() => {
        const usesSoftDelete: boolean = formModeFlags.hasData && formData !== undefined && Object.hasOwn(formData, 'deleted_at');
        return {
            usesSoftDelete,
            isCurrentlyActive: usesSoftDelete && formData?.deleted_at === null,
            isCurrentlyInactive: usesSoftDelete && formData?.deleted_at !== null,
        };
    }, [formModeFlags.hasData, formData]);

    // Control flags
    const controlFlags = useMemo(
        () => ({
            disableForm: mode === 'unknown' || dataStateFlags.isCurrentlyInactive,
            disableActionButtons: mode === 'unknown',
            disableFormSubmit: mode === 'unknown' || dataStateFlags.isCurrentlyInactive,
        }),
        [mode, dataStateFlags.isCurrentlyInactive],
    );

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

    const form = useForm<OrganizationCreateType | OrganizationUpdateType>({
        disabled: controlFlags.disableForm,
        resolver: zodResolver(formModeFlags.isEditOrManageMode ? organizationPartialSchema : organizationSchema),
        defaultValues: resolvedDefaultValues,
    });

    const { watch, setValue, formState } = form;
    const { fields, append, remove } = useFieldArray({
        name: 'alt_domains' as keyof (OrganizationCreateType | OrganizationUpdateType),
        control: form.control,
    });

    // ============ EFFECTS ============
    // Form dirty state listener
    useEffect(() => {
        if (onFormStateChange) {
            onFormStateChange(formState?.isDirty, mode);
        }
    }, [formState?.isDirty, mode, onFormStateChange]);

    // Domain sync effect
    const businessEmail = watch('business_email');
    useEffect(() => {
        if (syncDomainWithEmail && businessEmail && typeof businessEmail === 'string' && !controlFlags.disableForm) {
            const extracted = businessEmail.split('@')[1] || '';
            if (extracted) {
                setValue('domain', extracted);
            }
        }
    }, [businessEmail, setValue, syncDomainWithEmail, controlFlags.disableForm]);

    // ============ EVENT HANDLERS ============
    const toggleDomainSync = useCallback(() => {
        setSyncDomainWithEmail((prev) => !prev);
    }, []);

    const handleValidSubmit = useCallback(
        (data: OrganizationUpdateType | OrganizationCreateType) => {
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

    const handleInvalidSubmit: SubmitErrorHandler<OrganizationCreateType | OrganizationUpdateType> = useCallback((errors) => {
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
                                    <FormLabel>Name</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.name} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="Organization Name" maxLength={charLimits.name} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>This is your organization's display name.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Business Email Field */}
                    <FormField
                        control={form.control}
                        name="business_email"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Business Email</FormLabel>
                                </FormLabelConditional>
                                <InputCounter maxLength={charLimits.email} value={field.value ?? ''}>
                                    <FormControl>
                                        <Input placeholder="contact@example.com" maxLength={charLimits.email} {...field} value={field.value ?? ''} />
                                    </FormControl>
                                </InputCounter>
                                <FormDescription>Primary email for your organization.</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Domain Field */}
                    <FormField
                        control={form.control}
                        name="domain"
                        render={({ field }) => (
                            <FormItem className="max-w-2xl">
                                <FormLabelConditional required>
                                    <FormLabel>Domain</FormLabel>
                                </FormLabelConditional>
                                <div className="flex items-start gap-2">
                                    <InputCounter maxLength={charLimits.domain} value={field.value ?? ''}>
                                        <FormControl>
                                            <Input placeholder="example.com" maxLength={charLimits.domain} {...field} value={field.value ?? ''} />
                                        </FormControl>
                                    </InputCounter>
                                    <Button
                                        type="button"
                                        variant={syncDomainWithEmail ? 'secondary' : 'ghost'}
                                        size="icon"
                                        className="cursor-pointer"
                                        onClick={toggleDomainSync}
                                        disabled={controlFlags.disableForm || isSubmitting}
                                        title={syncDomainWithEmail ? 'Auto-sync enabled' : 'Auto-sync disabled'}
                                    >
                                        <LinkIcon className={`h-4 w-4 ${syncDomainWithEmail ? 'text-foreground' : 'text-muted-foreground'}`} />
                                    </Button>
                                </div>
                                <FormDescription>
                                    Main domain used for company email.
                                    {syncDomainWithEmail ? ' Auto-syncs with business email domain.' : ' Auto-sync disabled.'}
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />

                    {/* Alternative Domains */}
                    <div className="max-w-2xl space-y-3">
                        <FormLabel>Alternative Domains</FormLabel>
                        <div className="mt-3 space-y-2">
                            {fields.map((field, index) => (
                                <div key={field.id} className="flex items-start gap-2">
                                    <FormField
                                        control={form.control}
                                        name={`alt_domains.${index}` as const}
                                        render={({ field }) => (
                                            <FormItem className="flex-1">
                                                <InputCounter maxLength={charLimits.domain} value={field.value ?? ''}>
                                                    <FormControl>
                                                        <Input
                                                            placeholder={`alt-${index + 1}.example.com`}
                                                            maxLength={charLimits.domain}
                                                            {...field}
                                                            value={field.value ?? ''}
                                                        />
                                                    </FormControl>
                                                </InputCounter>
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        className="cursor-pointer"
                                        onClick={() => remove(index)}
                                        disabled={isSubmitting || controlFlags.disableForm}
                                    >
                                        <Trash2 className="h-4 w-4 text-muted-foreground" />
                                    </Button>
                                </div>
                            ))}
                        </div>
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            className="cursor-pointer"
                            onClick={() => append('')}
                            disabled={fields.length >= 5 || isSubmitting || controlFlags.disableForm}
                        >
                            <Plus className="h-4 w-4" />
                            <span className="text-sm">Domain</span>
                        </Button>
                        <FormDescription>You may add up to 5 unique domains.</FormDescription>
                        <FormMessage>{form?.formState?.errors?.alt_domains?.root?.message}</FormMessage>
                    </div>

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
