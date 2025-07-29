'use client';

import PersistentAlertDialog from '@/components/dialogs/persistent-alert-dialog';
import LabelConditional, { type BadgePreset } from '@/components/labels/label-coditional';
import AppLayout from '@/layouts/app-layout';
import SysOrgManageForm from '@/pages/v1/sys/org/_form';
import { type BreadcrumbItem, SharedData } from '@/types';
import { FormMode } from '@/types/app';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function SysOrgManageOrg() {
    const { context } = usePage<SharedData>().props;
    const { prefixedId } = route().params;

    const CURRENT_PAGE_NAME: string = ' Manage Organization';
    const ROUTE: string = route('v1.sys.orgs.manage:get', prefixedId ?? 'unknown');
    let BADGE_PRESET: BadgePreset = 'create';
    let MODE: FormMode = 'create';

    const [formIsDirty, setFormIsDirty] = useState(false);
    const formMode = context?.form;

    console.log(formMode);

    if (formMode) {
        if (formMode?.mode === 'edit' || formMode?.mode === 'manage' || formMode?.data) {
            BADGE_PRESET = 'manage';
            MODE = 'edit';

            if (!formMode?.data || !formMode?.key) {
                BADGE_PRESET = 'unknown';
                MODE = 'unknown';
            }
        }
    }
    console.log(MODE === 'unknown');
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Organization',
            href: route('v1.sys.orgs.dashboard:get'),
        },
        {
            title: CURRENT_PAGE_NAME,
            href: ROUTE,
        },
    ];

    const checkFormState = (isDirty: boolean): void => {
        setFormIsDirty(isDirty);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={CURRENT_PAGE_NAME} />

            <div className="min-h-screen w-full px-12 py-12">
                <LabelConditional badgePreset={BADGE_PRESET} className="max-w-2xl" showDirtyBadge={formIsDirty}>
                    <h2 className="mb-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{CURRENT_PAGE_NAME}</h2>
                </LabelConditional>

                <p className="mb-8 text-sm text-muted-foreground">Fill in the details about your organization.</p>

                {/* Form */}
                <SysOrgManageForm mode={MODE} formData={formMode?.data} onFormStateChange={checkFormState} />

                <PersistentAlertDialog
                    show={MODE === 'unknown'}
                    persistCondition={MODE === 'unknown'}
                    title={'Something went wrong'}
                    description="This entry appears invalid or no longer exists."
                    showRedirect
                    redirectPath={route('v1.sys.orgs.dashboard:get')}
                />
            </div>
        </AppLayout>
    );
}
