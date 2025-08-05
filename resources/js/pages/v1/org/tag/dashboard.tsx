import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import OrgTagDashboardDataTable from '@/pages/v1/org/tag/_datatable';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { FilePlus } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organization Unit',
        href: route('v1.org.units.dashboard:get'),
    },
];

export default function OrgTagDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Organization Tag" />
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
                                        <Link href={route('v1.org.tags.manage:get')}>
                                            <FilePlus className="mr-2 h-4 w-4 text-white" />
                                            Add New Tag
                                        </Link>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Add New Tag</TooltipContent>
                            </Tooltip>
                        </div>
                    </TooltipProvider>
                </div>

                {/* --- Data Table --- */}
                <OrgTagDashboardDataTable />
            </div>
        </AppLayout>
    );
}
