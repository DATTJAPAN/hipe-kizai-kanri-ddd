import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import { useQuery } from '@tanstack/react-query';
import axios from 'axios';
import { ChevronsUpDown } from 'lucide-react';
import { ReactNode, useEffect, useState } from 'react';

export type ComboboxOption = {
    id: string | number;
    value: string | number;
    displayName: ReactNode;
    searchKeywords?: string[];
};

export interface ComboboxLabels {
    inputPlaceholder?: string;
    selectedValuePlaceholder?: string;
    empty?: string;
    loading?: string;
    notFound?: string;
    current?: string;
}

export interface DataSourceConfig {
    queryKeys: string[];
    endpoint: string;
    headers: Record<string, unknown>;
    payload?: Record<string, unknown>;
    onDataFetching?: () => void;
    onDataLoading?: () => void;
    onDataSuccess?: () => void;
}

export interface ComboboxProps {
    labels?: ComboboxLabels;
    staticOptions?: ComboboxOption[];
    dataSource?: DataSourceConfig;
    initialValue?: string | number;
    currentValue?: string | number;
    onChange?: (value: string, item?: ComboboxOption) => void;
    disableSelectingOption?: boolean;
    disableDeSelectingOption?: boolean;
}

// Base query key for caching
const BASE_QUERY_KEY = ['combobox', 'options'];

// Default XHR headers
const XHR_HEADERS = { headers: { 'X-Requested-With': 'XMLHttpRequest' } };

// Default labels for various states
const DEFAULT_LABELS = {
    inputPlaceholder: 'Search',
    selectedValuePlaceholder: '-----',
    empty: 'Empty',
    loading: 'Loading',
    notFound: 'No results found',
    current: '*',
};

export default function CustomizableCombobox({
    labels,
    staticOptions,
    dataSource,
    initialValue,
    currentValue,
    onChange,
    disableSelectingOption = false,
    disableDeSelectingOption = false,
}: ComboboxProps) {
    const [isOpen, setIsOpen] = useState(false);

    // Determine if dynamic data fetching should be enabled
    const shouldFetchData = !staticOptions && !!dataSource;

    const displayLabels = { ...DEFAULT_LABELS, ...labels };

    const {
        data = [],
        isFetching,
        isLoading,
    } = useQuery<ComboboxOption[]>({
        queryKey: [...BASE_QUERY_KEY, ...(dataSource?.queryKeys ?? [])],
        queryFn: async () => {
            if (!dataSource) return [];

            const response = await axios.post(dataSource.endpoint, dataSource.payload, { ...XHR_HEADERS, ...dataSource.headers });

            return response.data?.data || [];
        },
        enabled: shouldFetchData,
    });

    // Use static options if provided
    const options: ComboboxOption[] = staticOptions || data;

    useEffect(() => {
        if (isFetching) {
            dataSource?.onDataFetching?.();
        }

        if (isLoading) {
            dataSource?.onDataLoading?.();
        }
    }, [isFetching, isLoading, dataSource]);

    const renderSelectedValue = (options: ComboboxOption[]) => {
        if (isLoading) {
            return displayLabels.selectedValuePlaceholder;
        }

        const foundOption = options.find((option) => String(option.id) === String(currentValue));

        if (foundOption) {
            return <span className="w-full truncate text-start">{foundOption.displayName}</span>;
        }

        return displayLabels.selectedValuePlaceholder;
    };

    const renderOptions = (items: ComboboxOption[]) => {
        return items?.map((item: ComboboxOption, index: number) => {
            const itemValue = String(item?.id);
            const currentValueStr = String(currentValue || '');
            const initialValueStr = String(initialValue || '');

            const isSelected = currentValueStr !== '' && currentValueStr === itemValue;
            const isOldValue = initialValueStr && initialValueStr === itemValue;

            const getKeywords = () => {
                if (!item.searchKeywords || item.searchKeywords.length === 0) {
                    return [];
                }

                return item.searchKeywords.map((keyword: string) => {
                    return String(keyword).toLowerCase();
                });
            };

            return (
                <CommandItem
                    className={cn(
                        'py-1.5',
                        'cursor-pointer',
                        isSelected && 'selected-state bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300',
                        !isSelected && 'not-selected-state',
                    )}
                    key={`option-${item.id}-${index}`}
                    value={itemValue}
                    keywords={getKeywords()}
                    disabled={disableSelectingOption}
                    onSelect={(selectedValue: string): void => {
                        let newValue = selectedValue === currentValueStr ? '' : selectedValue;

                        if (disableDeSelectingOption) {
                            newValue = selectedValue;
                        }

                        if (onChange) {
                            onChange(
                                newValue,
                                items.find((item) => String(item.id) === String(newValue)),
                            );
                        }

                        setIsOpen(false);
                    }}
                >
                    {/* Text indicator in the options list */}
                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger className="relative cursor-pointer overflow-hidden">{item.displayName}</TooltipTrigger>
                            <TooltipContent>{item.displayName}</TooltipContent>
                        </Tooltip>
                    </TooltipProvider>

                    {/* Indicator this is the original value */}
                    {isOldValue && <Badge className="w-fit truncate">{displayLabels?.current}</Badge>}
                </CommandItem>
            );
        });
    };

    return (
        <Popover modal={true} open={isOpen} onOpenChange={setIsOpen}>
            <PopoverTrigger asChild>
                {/* Display The Current Selected Value */}
                <Button
                    variant="outline"
                    className={cn('relative w-full cursor-pointer justify-end truncate text-left', 'dark:border-stone-500 dark:bg-zinc-900')}
                    role="combobox"
                    aria-expanded={isOpen}
                    disabled={isLoading}
                >
                    <div className="absolute right-0 left-0 max-w-fit truncate ps-3 pe-10 text-left">{renderSelectedValue(options)}</div>
                    <ChevronsUpDown className="opacity-50" />
                </Button>
            </PopoverTrigger>

            <PopoverContent>
                <Command
                    filter={(value: string, search: string, keywords: string[] | undefined): 1 | 0 => {
                        const extendedValue: string = `${value} ${keywords?.join(' ')}`.toLowerCase();
                        return extendedValue.includes(search.toLowerCase()) ? 1 : 0;
                    }}
                >
                    <CommandInput placeholder={displayLabels?.inputPlaceholder} className="border-0 focus-visible:ring-0" />
                    <CommandList>
                        <CommandEmpty>{displayLabels?.notFound}</CommandEmpty>
                        <CommandGroup>{renderOptions(options)}</CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
