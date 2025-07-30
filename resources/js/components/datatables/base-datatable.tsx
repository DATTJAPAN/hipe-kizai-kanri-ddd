import { MantineReactTable, type MRT_RowData, type MRT_TableOptions } from 'mantine-react-table';
import { ReactNode } from 'react';

type BaseDataTableProps<T extends MRT_RowData> = MRT_TableOptions<T> & {
    outsideToolbar?: ReactNode;
};

export default function BaseDataTable<T extends MRT_RowData>({ outsideToolbar, ...tableOptions }: BaseDataTableProps<T>) {
    return (
        <div>
            {outsideToolbar && <div className="mb-4">{outsideToolbar}</div>}
            <MantineReactTable {...tableOptions} />
        </div>
    );
}
