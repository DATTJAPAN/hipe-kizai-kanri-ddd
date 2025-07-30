import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ReactNode } from 'react';

type ConfirmDialogProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onConfirm: () => void;
    title?: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    children?: ReactNode;
};

export default function ConfirmDialog({
    open,
    onOpenChange,
    onConfirm,
    title = 'Warning',
    description = 'Are you sure you want to continue this action?',
    confirmText = 'Save changes',
    cancelText = 'Close',
    children,
}: ConfirmDialogProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                </DialogHeader>
                <DialogFooter className="sm:justify-start">
                    <DialogClose asChild>
                        <Button type="button" variant="secondary">
                            {cancelText}
                        </Button>
                    </DialogClose>
                    <Button type="button" onClick={onConfirm} className="cursor-pointer">
                        {confirmText}
                    </Button>
                </DialogFooter>
                {children}
            </DialogContent>
        </Dialog>
    );
}
