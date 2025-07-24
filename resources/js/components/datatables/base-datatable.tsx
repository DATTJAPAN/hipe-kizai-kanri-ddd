import { MantineReactTable, type MRT_RowData, type MRT_TableOptions } from 'mantine-react-table';

type BaseDataTableProps<T extends MRT_RowData> = MRT_TableOptions<T> & {
    title?: string;
};

export default function BaseDataTable<T extends MRT_RowData>({ title, ...tableOptions }: BaseDataTableProps<T>) {
    return (
        <div>
            {title && <h1 className="mb-4 text-xl font-semibold">{title}</h1>}
            <MantineReactTable {...tableOptions} />
        </div>
    );
}
