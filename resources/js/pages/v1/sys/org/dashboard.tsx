import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { FilePlus } from 'lucide-react';
import SysOrgDashboardDataTable from './_datatable';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organization',
        href: route('v1.sys.orgs.dashboard:get'),
    },
];

export default function SysOrgDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Organization" />

            {/* Data Table Toolbar */}
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* --- Data Table Outside Toolbar --- */}
                <div className="mb-1">
                    <TooltipProvider delayDuration={300}>
                        <div className="flex flex-wrap items-center gap-2 bg-background py-2 shadow-sm">
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button
                                        asChild
                                        variant="default"
                                        size="default"
                                        className="group border-emerald-600 bg-emerald-600 text-white hover:bg-emerald-700"
                                    >
                                        <Link href={route('v1.sys.orgs.manage:get')}>
                                            <FilePlus className="mr-2 h-4 w-4 text-white" />
                                            Add Organization
                                        </Link>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Add Organization</TooltipContent>
                            </Tooltip>
                        </div>
                    </TooltipProvider>
                </div>

                {/* --- Data Table --- */}
                <SysOrgDashboardDataTable />
            </div>
        </AppLayout>
    );
}
