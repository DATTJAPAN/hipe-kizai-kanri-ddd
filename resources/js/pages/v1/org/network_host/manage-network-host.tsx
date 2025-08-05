'use client';

import PersistentAlertDialog from '@/components/dialogs/persistent-alert-dialog';
import LabelConditional, { type BadgePreset } from '@/components/labels/label-coditional';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, SharedData } from '@/types';
import { FormMode } from '@/types/app';
import { OrganizationNetworkHost } from '@/types/schema/organization_network_host';
import { Head, usePage } from '@inertiajs/react';
import { useCallback, useMemo, useState } from 'react';
import OrgNetworkHostManageNetworkHostForm from './_form';

export default function OrgNetworkHostManageNetworkHost() {
    // ============ STATE MANAGEMENT ============
    const [formIsDirty, setFormIsDirty] = useState(false);

    // ============ DATA & ROUTE PARAMS ============
    const { context } = usePage<SharedData>().props;
    const { prefixedId } = route().params;
    const formMode = context?.form;

    // ============ CONSTANTS ============
    const PAGE_CONFIG = useMemo(
        () => ({
            title: 'Manage Network Host',
            description: 'Manage your organization network hosts.',
            route: route('v1.org.network_hosts.manage:get', prefixedId ?? 'unknown'),
        }),
        [prefixedId],
    );

    // ============ COMPUTED VALUES ============
    const formState = useMemo(() => {
        // Default state for create mode
        let badgePreset: BadgePreset = 'create';
        let mode: FormMode = 'create';

        // Determine mode based on form data
        if (formMode) {
            const hasEditableData = formMode?.mode === 'edit' || formMode?.mode === 'manage' || formMode?.data;

            if (hasEditableData) {
                badgePreset = 'manage';
                mode = 'edit';

                // Check for invalid/missing data
                if (!formMode?.data || !formMode?.key) {
                    badgePreset = 'unknown';
                    mode = 'unknown';
                }
            }
        }

        return { badgePreset, mode };
    }, [formMode]);

    const breadcrumbs = useMemo(
        (): BreadcrumbItem[] => [
            {
                title: 'Organization Network Host',
                href: route('v1.org.network_hosts.dashboard:get'),
            },
            {
                title: PAGE_CONFIG.title,
                href: PAGE_CONFIG.route,
            },
        ],
        [PAGE_CONFIG.title, PAGE_CONFIG.route],
    );

    // ============ EVENT HANDLERS ============
    const handleFormStateChange = useCallback((isDirty: boolean): void => {
        setFormIsDirty(isDirty);
    }, []);

    // ============ RENDER ============
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={PAGE_CONFIG.title} />

            <div className="min-h-screen w-full px-12 py-12">
                {/* Page Header */}
                <LabelConditional badgePreset={formState.badgePreset} className="max-w-2xl" showDirtyBadge={formIsDirty}>
                    <h2 className="mb-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{PAGE_CONFIG.title}</h2>
                </LabelConditional>

                <p className="mb-8 text-sm text-muted-foreground">{PAGE_CONFIG.description}</p>

                {/* Form Component */}
                <OrgNetworkHostManageNetworkHostForm
                    mode={formState.mode}
                    formKey={formMode?.key}
                    formData={formMode?.data as OrganizationNetworkHost}
                    onFormStateChange={handleFormStateChange}
                />

                {/* Error Dialog */}
                <PersistentAlertDialog
                    show={formState.mode === 'unknown'}
                    persistCondition={formState.mode === 'unknown'}
                    title="Something went wrong"
                    description="This entry appears invalid or no longer exists."
                    maxCloseClicks={5}
                    showRedirect
                    redirectPath={route('v1.org.network_hosts.dashboard:get')}
                />
            </div>
        </AppLayout>
    );
}
