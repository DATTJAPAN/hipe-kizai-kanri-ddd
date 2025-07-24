import BaseDataTable from '@/components/datatables/base-datatable';
import SkeletonDatatable from '@/components/skeletons/skeleton-datatable';
import { useGetAllOrganizations } from '@/hooks/queries/sys';
import { type MRT_ColumnDef } from 'mantine-react-table';
import { useMemo } from 'react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertTriangle } from 'lucide-react';

export default function SysOrgDataTable() {
  const { isPending, isFetching, error, data = [] } = useGetAllOrganizations();

  const columns = useMemo<MRT_ColumnDef<(typeof data)[number]>[]>(
    () => [
      {
        accessorKey: 'id',
        header: 'ID',
      },
    ],
    [],
  );

  return (
    <div className="space-y-4">
      <BaseDataTable
        title="Organizations"
        columns={columns}
        data={data}
        state={{ isLoading: isPending || isFetching }}
      />

      {/* Beautiful error message */}
      {error instanceof Error && (
        <Alert variant="destructive">
          <AlertTriangle className="h-4 w-4" />
          <AlertTitle>Error</AlertTitle>
          <AlertDescription>{error.message}</AlertDescription>
        </Alert>
      )}

      {/* Show skeleton *after* base table mounts */}
      {isPending && <SkeletonDatatable />}
    </div>
  );
}
