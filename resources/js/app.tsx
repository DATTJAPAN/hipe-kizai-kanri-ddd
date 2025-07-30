import '@mantine/core/styles.css';
import '@mantine/dates/styles.css';
import 'mantine-react-table/styles.css';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { MantineProvider, type ColorScheme } from '@mantine/core';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { initializeTheme, useAppearance } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const queryClient = new QueryClient();

// 👇 Applies .dark class based on appearance logic (including system)
initializeTheme();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        const Main = () => {
            const { appearance } = useAppearance();

            const prefersDark = () => typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches;

            const colorScheme: ColorScheme = appearance === 'dark' || (appearance === 'system' && prefersDark()) ? 'dark' : 'light';

            return (
                <StrictMode>
                    <QueryClientProvider client={queryClient}>
                        <ReactQueryDevtools initialIsOpen={false} />
                        <MantineProvider forceColorScheme={colorScheme}>
                            <App {...props} />
                        </MantineProvider>
                    </QueryClientProvider>
                </StrictMode>
            );
        };

        root.render(<Main />);
    },
    progress: {
        color: '#4B5563',
    },
});
