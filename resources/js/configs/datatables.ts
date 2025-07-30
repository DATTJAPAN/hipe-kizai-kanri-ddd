import { type MRT_RowData, type MRT_TableOptions } from 'mantine-react-table';

//define re-usable default table options for all tables in your app
export const getDefaultMRTOptions = <TData extends MRT_RowData>(): Partial<MRT_TableOptions<TData>> => ({
    enableGlobalFilter: false,
    enableRowPinning: false,
    initialState: { showColumnFilters: false },
    // manualFiltering: true,
    // manualPagination: true,
    // manualSorting: true,

    enableSorting: true,
    muiTableHeadCellProps: {
        sx: { fontSize: '1.1rem' },
    },
    paginationDisplayMode: 'default',
    defaultColumn: {
        //you can even list default column options here
    },
});
