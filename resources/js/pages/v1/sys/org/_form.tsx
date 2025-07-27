'use client';
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
import { FormEvent, useEffect, useState } from 'react';
import { SubmitErrorHandler, useFieldArray, useForm } from 'react-hook-form';

type FormProps = {
    mode: FormMode;
    formData?: Record<string, unknown>;
    onFormStateChange?: (isDirty: boolean, mode: FormMode) => void;
};

export default function SysOrgManageForm({ mode, formData, onFormStateChange }: FormProps) {
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});
    const [syncDomainWithEmail, setSyncDomainWithEmail] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const charLimits = organizationCharacterLimits;

    const defaultValues = {
        name: '',
        business_email: '',
        domain: '',
        alt_domains: [] as string[],
    };

    const DISABLE_FORM = mode === 'unknown';
    const IS_FORM_EDIT_MODE: boolean = mode === 'edit' || mode === 'manage';
    const useResolveForm = () => {
        const resolveDefaultValues = IS_FORM_EDIT_MODE ? { ...defaultValues, ...formData } : defaultValues;

        return useForm<OrganizationCreateType | OrganizationUpdateType>({
            disabled: DISABLE_FORM,
            resolver: zodResolver(IS_FORM_EDIT_MODE ? organizationPartialSchema : organizationSchema),
            defaultValues: resolveDefaultValues,
        });
    };

    const form = useResolveForm();
    const { watch, setValue, formState } = form;

    const { fields, append, remove } = useFieldArray({
        name: 'alt_domains' as const,
        control: form.control,
    });

    // listen to form dirty state
    useEffect(() => {
        if (onFormStateChange) {
            onFormStateChange(formState?.isDirty, mode);
        }
    }, [formState?.isDirty, mode, onFormStateChange]);

    const businessEmail = watch('business_email');
    useEffect(() => {
        if (syncDomainWithEmail && businessEmail && typeof businessEmail === 'string' && !DISABLE_FORM) {
            const extracted = businessEmail.split('@')[1] || '';
            if (extracted) {
                setValue('domain', extracted);
            }
        }
    }, [businessEmail, setValue, syncDomainWithEmail, DISABLE_FORM]);

    const toggleDomainSync = () => {
        setSyncDomainWithEmail(!syncDomainWithEmail);
    };

    const _handleFormSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (DISABLE_FORM || isSubmitting) return;
        form.handleSubmit(_handleValid, _handleOnInvalid)();
    };

    const _handleValid = (data: OrganizationUpdateType | OrganizationCreateType) => {
        console.log('Form submitted with data:', data);
        setIsSubmitting(true);

        if (IS_FORM_EDIT_MODE) {
            const { prefixedId } = route().params;
            return router.post(route('v1.sys.orgs.update:post', { prefixedId: prefixedId ?? formData?.id }), data, {
                onSuccess: () => {
                    setServerErrors({});
                    form.reset(data);
                    setIsSubmitting(false);
                },
                onError: (errors) => {
                    console.error('Form submission errors:', errors);
                    setServerErrors(errors);
                    setIsSubmitting(false);
                },
                onFinish: () => {
                    setIsSubmitting(false);
                },
            });
        }

        return router.post(route('v1.sys.orgs.add:post'), data, {
            onSuccess: () => {
                setServerErrors({});
                form.reset(data);
                setIsSubmitting(false);
            },
            onError: (errors) => {
                console.error('Form submission errors:', errors);
                setServerErrors(errors);
                setIsSubmitting(false);
            },
            onFinish: () => {
                setIsSubmitting(false);
            },
        });
    };

    const _handleOnInvalid: SubmitErrorHandler<OrganizationCreateType | OrganizationUpdateType> = (errors) => {
        console.error('Form submission errors:', errors);
    };

    const getButtonText = () => {
        if (isSubmitting) {
            return IS_FORM_EDIT_MODE ? 'Saving...' : 'Submitting...';
        }
        if (formState.isDirty && mode !== 'create') {
            return 'Save Changes';
        }
        return 'Submit';
    };

    return (
        <>
            <Form {...form}>
                <form onSubmit={_handleFormSubmit} className="space-y-8">
                    {/* Name */}
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

                    {/* Business Email */}
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

                    {/* Domain */}
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
                                        disabled={DISABLE_FORM || isSubmitting}
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
                                        disabled={isSubmitting}
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
                            disabled={fields.length >= 5 || isSubmitting}
                        >
                            <Plus className="h-4 w-4" />
                            <span className="text-sm">Domain</span>
                        </Button>
                        <FormDescription>You may add up to 5 unique domains.</FormDescription>
                        <FormMessage>{form?.formState?.errors?.alt_domains?.root?.message}</FormMessage>
                    </div>

                    {/* Submit */}
                    <div className="max-w-2xl pt-6">
                        <Button
                            type="submit"
                            className="w-full cursor-pointer"
                            role="button"
                            disabled={DISABLE_FORM || !formState?.isDirty || isSubmitting}
                        >
                            {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                            {getButtonText()}
                        </Button>
                    </div>

                    {/* Alert Message */}
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
        </>
    );
}
