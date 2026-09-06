import { render, screen } from '@testing-library/vue';
import { describe, expect, test, vi } from 'vitest';
import '@testing-library/jest-dom';
import ErrorPage from '@/ui/Pages/ErrorPage.vue';
import { i18n } from '@/ui/main';

vi.mock('@inertiajs/vue3', async importOriginal => ({
  ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
  Head: { name: 'Head', render: () => null },
  usePage: () => ({ url: '/', component: '', props: {}, version: null }),
}));

const renderErrorPage = (status: number): ReturnType<typeof render> =>
  render(ErrorPage, {
    props: { status },
    global: {
      plugins: [i18n],
      stubs: {
        AppLayout: { template: '<div><slot /></div>' },
      },
    },
  });

describe('ErrorPage.vue', () => {
  test('shows the status code', () => {
    renderErrorPage(404);

    expect(screen.getByText('404')).toBeInTheDocument();
  });

  test('shows the message for a known status', () => {
    renderErrorPage(404);

    expect(screen.getByText('Page not found')).toBeInTheDocument();
    expect(
      screen.getByText('The page you were looking for does not exist.'),
    ).toBeInTheDocument();
  });

  test('shows the maintenance message for a 503', () => {
    renderErrorPage(503);

    expect(
      screen.getByText('Adventurous is down for maintenance'),
    ).toBeInTheDocument();
    expect(screen.getByText("We'll be back shortly.")).toBeInTheDocument();
  });

  test('falls back to a generic message for an unmapped status', () => {
    renderErrorPage(418);

    expect(screen.getByText('418')).toBeInTheDocument();
    expect(screen.getByText('Something went wrong')).toBeInTheDocument();
    expect(
      screen.getByText('An unexpected error occurred.'),
    ).toBeInTheDocument();
  });

  test('links back to the dashboard', () => {
    renderErrorPage(500);

    expect(
      screen.getByRole('link', { name: 'Back to dashboard' }),
    ).toHaveAttribute('href', '/');
  });
});
