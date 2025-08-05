import { type NavItem } from '@/types';
import { Building2, IdCard, LayoutGrid, Tag } from 'lucide-react';

const organizationSideBarNavigation: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('v1.org.dashboard:get'),
        icon: LayoutGrid,
    },
    {
        title: 'Organization Unit',
        href: route('v1.org.units.dashboard:get'),
        icon: IdCard,
    },
    {
        title: 'Organization Tag',
        href: route('v1.org.tags.dashboard:get'),
        icon: Tag,
    },
];

const systemSideBarNavigation: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('v1.sys.dashboard:get'),
        icon: LayoutGrid,
    },
    {
        title: 'Organization',
        href: route('v1.sys.orgs.dashboard:get'),
        icon: Building2,
    },
];

const getSidebarNavigation = (sidebarFor?: 'org' | 'web' | 'sys' | 'system'): NavItem[] => {
    if (sidebarFor === 'org' || sidebarFor === 'web') {
        return organizationSideBarNavigation;
    }

    if (sidebarFor === 'sys' || sidebarFor === 'system') {
        return systemSideBarNavigation;
    }

    return [];
};

export { getSidebarNavigation, organizationSideBarNavigation, systemSideBarNavigation };
