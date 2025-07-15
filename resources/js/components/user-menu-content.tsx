import { Button } from '@/components/ui/button';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { UserInfo } from '@/components/user-info';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { ApiResponse, type User } from '@/types';
import { Link } from '@inertiajs/react';
import axios from 'axios';
import { LogOut, Settings } from 'lucide-react';

interface UserMenuContentProps {
    user: User;
}

export function UserMenuContent({ user }: UserMenuContentProps) {
    const cleanup = useMobileNavigation();

    const handleLogout = () => {
        cleanup();

        axios
            .post<ApiResponse>(route('post.system_logout'), {
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            })
            .then((response) => {
                console.log('Success:', response);
            })
            .catch((error) => {
                if (axios.isAxiosError(error)) {
                    console.error('Error:', error.response?.data);
                    // Handle validation errors or other API errors
                } else {
                    console.error('Error:', error);
                }
            });
    };

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <UserInfo user={user} showEmail={true} />
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem asChild>
                    <Link className="block w-full" href={'#'} as="button" prefetch onClick={cleanup}>
                        <Settings className="mr-2" />
                        Settings
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem asChild>
                <Button
                    className="mt-4 w-full"
                    tabIndex={4}
                    onClick={(e) => {
                        e.preventDefault();
                        handleLogout();
                    }}
                >
                    <LogOut className="mr-2" />
                    Log out
                </Button>
            </DropdownMenuItem>
        </>
    );
}
