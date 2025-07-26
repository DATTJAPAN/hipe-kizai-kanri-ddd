import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { FilePlus } from 'lucide-react';
import SysOrgDataTable from './_datatable';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organization',
        href: route('v1.sys.dashboard:get'),
    },
];

export default function SysOrgDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Organization" />
            {/* Data Table Toolbar */}
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* --- Data Table Outside Toolbar --- */}
                <div className="mb-4">
                    <TooltipProvider delayDuration={300}>
                        <div className="flex flex-wrap items-center gap-2 border-b-1 bg-background p-2 shadow-sm">
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button asChild variant="default" size="icon" className="h-9 w-9">
                                        <Link href={route('v1.sys.orgs.add:get')}>
                                            <FilePlus className="h-4 w-4" />
                                            <span className="sr-only">Add Organization</span>
                                        </Link>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Add Organization</TooltipContent>
                            </Tooltip>
                        </div>
                    </TooltipProvider>
                </div>
                {/* --- Data Table --- */}
                <SysOrgDataTable />
            </div>
        </AppLayout>
    );
}
