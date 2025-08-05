import { QueryOptions } from '@/hooks/queries/type';
import { useQuery } from '@tanstack/react-query';
import axios from 'axios';

function useGetAllOrganizationUnits(options: QueryOptions = {}) {
    const { queryKey, url, withTrashed = false, onlyTrashed = false, additionalData = {} } = options;

    const QUERY_KEY: string[] = queryKey || [
        'orgs',
        'datatables',
        'units',
        ...(withTrashed ? ['with-trashed'] : []),
        ...(onlyTrashed ? ['only-trashed'] : []),
    ];

    const QUERY_URL: string = url || route('v1.req.org.units.datatable:post');

    const QUERY_DATA = {
        withTrashed,
        onlyTrashed,
        ...additionalData,
    };

    const QUERY_HEADERS = {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    return useQuery({
        queryKey: QUERY_KEY,
        queryFn: async () => {
            const response = await axios.post(QUERY_URL, QUERY_DATA, QUERY_HEADERS);
            return response?.data?.data;
        },
    });
}

export { useGetAllOrganizationUnits };
