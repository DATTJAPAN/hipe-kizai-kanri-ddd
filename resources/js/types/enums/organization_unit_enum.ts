import { ComboboxOption } from '@/components/comboboxes/customizable-combobox';
import { Briefcase, Building, Building2, Layers, UserCheck, Users2 } from 'lucide-react';

export enum OrganizationUnitEnum {
    DIVISION = 'DIVISION',
    DEPARTMENT = 'DEPARTMENT',
    BRANCH = 'BRANCH',
    SECTION = 'SECTION',
    UNIT = 'UNIT',
    TEAM = 'TEAM',
}

export type OrganizationUnitType = keyof typeof OrganizationUnitEnum;

export const orgUnitTypes = Object.entries(OrganizationUnitEnum).map(([key, value]) => {
    const styleMap = {
        DIVISION: {
            label: 'Division',
            icon: Building2,
            color: 'text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-900',
            iconColor: 'text-blue-600 dark:text-blue-500',
        },
        BRANCH: {
            label: 'Branch',
            icon: Building,
            color: 'text-green-700 dark:text-green-400 border-green-200 dark:border-green-900',
            iconColor: 'text-green-600 dark:text-green-500',
        },
        SECTION: {
            label: 'Section',
            icon: Layers,
            color: 'text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-900',
            iconColor: 'text-purple-600 dark:text-purple-500',
        },
        UNIT: {
            label: 'Unit',
            icon: Briefcase,
            color: 'text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-900',
            iconColor: 'text-orange-600 dark:text-orange-500',
        },
        DEPARTMENT: {
            label: 'Department',
            icon: UserCheck,
            color: 'text-red-700 dark:text-red-400 border-red-200 dark:border-red-900',
            iconColor: 'text-red-600 dark:text-red-500',
        },
        TEAM: {
            label: 'Team',
            icon: Users2,
            color: 'text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-900',
            iconColor: 'text-yellow-600 dark:text-yellow-500',
        },
    };

    const config = styleMap[value as keyof typeof styleMap];

    return {
        value,
        label: config.label,
        icon: config.icon,
        color: config.color,
        iconColor: config.iconColor,
    };
});
export const orgUnitTypeOptions: ComboboxOption[] = [
    {
        id: 'DIVISION',
        value: 'DIVISION',
        displayName: 'Division',
        searchKeywords: ['Division'],
    },
];
