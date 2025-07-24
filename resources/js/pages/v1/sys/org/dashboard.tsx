import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import SysOrgDataTable from './datatable';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organization',
        href: route('v1.org.dashboard:get'),
    },
];

export default function SysOrgDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Organization Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <SysOrgDataTable />

                
            </div>
        </AppLayout>
    );
}
