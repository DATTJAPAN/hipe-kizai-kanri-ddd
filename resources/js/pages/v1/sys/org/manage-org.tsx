'use client';

import InputCounter from '@/components/inputs/input-counter';
import FormLabelConditional from '@/components/labels/form-label-conditional';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { organizationCharacterLimits, OrganizationCreateType, organizationSchema, OrganizationUpdateType } from '@/types/schema';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router } from '@inertiajs/react';
import { AlertCircleIcon, Plus, Trash2 } from 'lucide-react';
import { FormEvent, useEffect, useState } from 'react';
import { SubmitErrorHandler, useFieldArray, useForm } from 'react-hook-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organization',
        href: route('v1.sys.dashboard:get'),
    },
    {
        title: 'Create Organization',
        href: route('v1.sys.orgs.add:get'),
    },
];

export default function SysOrgManageOrg() {
    const [serverErrors, setServerErrors] = useState<Record<string, string>>({});

    const charLimits = organizationCharacterLimits;

    const defaultValues = {
        name: '',
        business_email: '',
        domain: '',
        alt_domains: [],
    };

    const useResolveForm = () => {
        // todo: Add logic to determine if this is an update or create form
        // For now, we will assume it's a create form
        // If it's an update form, we would use `organizationPartialSchema` instead of `organizationSchema`
        return useForm<OrganizationCreateType>({
            resolver: zodResolver(organizationSchema),
            defaultValues,
        });
    };

    const form = useResolveForm();
    const { watch, setValue } = form;

    const { fields, append, remove } = useFieldArray({
        name: 'alt_domains',
        control: form.control,
    });

    const businessEmail = watch('business_email');
    const domainField = watch('domain');

    useEffect(() => {
        const extracted = businessEmail.split('@')[1] || '';
        if (extracted) {
            setValue('domain', extracted);
        }
    }, [businessEmail, domainField, setValue]);

    const _handleFormSubmit = (e: FormEvent) => {
        e.preventDefault();
        form.handleSubmit(_handleValid, _handleOnInvalid)();
    };

    const _handleValid = (data: OrganizationUpdateType | OrganizationCreateType) => {
        console.log('Form submitted with data:', data);
        // Here you would typically send the data to your backend
        router.post(route('v1.sys.orgs.add:post'), data, {
            onError: (errors) => {
                console.error('Form submission errors:', errors);
                setServerErrors(errors);
            },
        });
    };

    const _handleOnInvalid: SubmitErrorHandler<OrganizationCreateType | OrganizationUpdateType> = (errors) => {
        console.error('Form submission errors:', errors);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Organization" />

            <div className="min-h-screen w-full px-12 py-12">
                <h2 className="mb-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Create Organization</h2>
                <p className="mb-8 text-sm text-muted-foreground">Fill in the details about your organization.</p>

                {/* Form */}
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

                                    <InputCounter maxLength={charLimits.name} value={field.value}>
                                        <FormControl>
                                            <Input placeholder="Organization Name" maxLength={charLimits.name} {...field} />
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

                                    <InputCounter maxLength={charLimits.email} value={field.value}>
                                        <FormControl>
                                            <Input placeholder="contact@example.com" maxLength={charLimits.email} {...field} />
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

                                    <InputCounter maxLength={charLimits.domain} value={field.value}>
                                        <FormControl>
                                            <Input placeholder="example.com" maxLength={charLimits.domain} {...field} />
                                        </FormControl>
                                    </InputCounter>

                                    <FormDescription>Main domain used for company email.</FormDescription>
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
                                            name={`alt_domains.${index}`}
                                            render={({ field }) => (
                                                <FormItem className="flex-1">
                                                    <InputCounter maxLength={charLimits.domain} value={field.value}>
                                                        <FormControl>
                                                            <Input
                                                                placeholder={`alt-${index + 1}.example.com`}
                                                                maxLength={charLimits.domain}
                                                                {...field}
                                                            />
                                                        </FormControl>
                                                    </InputCounter>
                                                    <FormMessage />
                                                </FormItem>
                                            )}
                                        />
                                        <Button type="button" variant="ghost" size="icon" className="cursor-pointer" onClick={() => remove(index)}>
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
                                disabled={fields.length >= 5}
                            >
                                <Plus className="h-4 w-4" />
                                <span className="text-sm">Domain</span>
                            </Button>
                            <FormDescription>You may add up to 5 unique domains.</FormDescription>
                            <FormMessage>{form?.formState?.errors?.alt_domains?.root?.message}</FormMessage>
                        </div>

                        {/* Submit */}
                        <div className="max-w-2xl pt-6">
                            <Button type="submit" className="w-full cursor-pointer" role="button">
                                Submit
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
            </div>
        </AppLayout>
    );
}
