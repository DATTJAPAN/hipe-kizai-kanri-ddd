import { useQuery } from '@tanstack/react-query';
import axios from 'axios';

function useGetAllOrganizations(queryKey?: string[]) {
    const QUERY_KEY = ['orgs', 'datatables'];
    const QUERY_URL = route('v1.req.sys.orgs.datatable:post');
    const QUERY_DATA = {};
    const QUERY_HEADERS = {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    return useQuery({
        queryKey: queryKey ? queryKey : QUERY_KEY,
        queryFn: async () => {
            const response = await axios.post(QUERY_URL, QUERY_DATA, QUERY_HEADERS);
            return response.data.data;
        },
    });
}

export { useGetAllOrganizations };
