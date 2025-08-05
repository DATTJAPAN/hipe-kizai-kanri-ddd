import { type NavItem } from '@/types';
import { Building2, IdCard, LayoutGrid, MapPinned, Network, Tag } from 'lucide-react';

const organizationSideBarNavigation: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('v1.org.dashboard:get'),
        icon: LayoutGrid,
    },
    {
        title: 'Units',
        href: route('v1.org.units.dashboard:get'),
        icon: IdCard,
    },
    {
        title: 'Tags',
        href: route('v1.org.tags.dashboard:get'),
        icon: Tag,
    },
    {
        title: 'Locations',
        href: route('v1.org.locations.dashboard:get'),
        icon: MapPinned,
    },
    {
        title: 'Network Host',
        href: route('v1.org.network_hosts.dashboard:get'),
        icon: Network,
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
