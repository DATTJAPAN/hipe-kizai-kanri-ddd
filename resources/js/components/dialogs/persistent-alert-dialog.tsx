import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { router } from '@inertiajs/react';
import { ReactNode, useEffect, useState } from 'react';
type PersistentAlertDialogProps = {
    show?: boolean;
    title?: string;
    description?: string;
    persistCondition?: boolean;
    maxCloseClicks?: number;
    showRedirect?: boolean;
    redirectPath?: string;
    closeText?: string;
    redirectText?: string;
    children?: ReactNode;
};
export default function PersistentAlertDialog({
    show = false,
    title = 'Something went wrong',
    description = 'This entry appears invalid or no longer exists.',
    persistCondition = false,
    maxCloseClicks = 0,
    showRedirect = false,
    redirectPath = '/dashboard',
    closeText = 'Close',
    redirectText = 'Go to Dashboard',
    children,
}: PersistentAlertDialogProps) {
    const [isOpen, setIsOpen] = useState(show);
    const [closeCount, setCloseCount] = useState(0);

    useEffect(() => {
        console.log('PersistentAlertDialog mounted with show:', show, 'and persistCondition:', persistCondition);
        if (persistCondition) {
            setIsOpen(show);
        }
    }, [show, persistCondition]);

    // Ensure the dialog remains open if persistCondition is true
    useEffect(() => {
        if (!isOpen) {
            if (maxCloseClicks > 0 && closeCount >= maxCloseClicks) {
                setIsOpen(false);
            } else if (persistCondition) {
                setTimeout(() => {
                    setIsOpen(true);
                }, 500); // Reopen after 500ms if closed
            }
        }
    }, [isOpen, maxCloseClicks, closeCount, persistCondition]);

    const handleClose = () => {
        setCloseCount(closeCount + 1);
        setIsOpen(false);
    };

    const handleRedirect = () => {
        router.visit(redirectPath);
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                </DialogHeader>

                <DialogFooter className="flex flex-col-reverse gap-2 sm:flex-row sm:justify-between">
                    {children ? (
                        children
                    ) : (
                        <>
                            <DialogClose asChild>
                                <Button type="button" variant="secondary" onClick={handleClose} className="cursor-pointer">
                                    {closeText}
                                </Button>
                            </DialogClose>
                            {showRedirect && (
                                <Button type="button" variant="default" onClick={handleRedirect} className="cursor-pointer">
                                    {redirectText}
                                </Button>
                            )}
                        </>
                    )}
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
