import { useQuery } from '@tanstack/react-query';
import axios from 'axios';

function useGetAllOrganizations() {
    return useQuery({
        queryKey: ['orgs_all'],
        queryFn: async () => {
            const response = await axios.post(
                route('v1.req.sys.orgs.get_all:post'),
                {},
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );
            return response.data.data;
        },
    });
}

export { useGetAllOrganizations };
