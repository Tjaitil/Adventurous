import { describe, test, expect, beforeEach, afterEach } from 'vitest';
import {
  render,
  screen,
  waitFor,
  fireEvent,
  RenderResult,
} from '@testing-library/vue';
import { createPinia } from 'pinia';
import '@testing-library/jest-dom';
import ClientOverlayWrapper from '@/ui/components/ClientOverlayWrapper.vue';
import { gameEventBus } from '@/gameEventsBus';
import { i18n } from '@/ui/main';

interface RenderOptions {
  stubs?: Record<string, unknown>;
}

const renderClientOverlay = (options: RenderOptions = {}): RenderResult => {
  return render(ClientOverlayWrapper, {
    global: {
      plugins: [createPinia(), i18n],
      stubs: {
        ArmoryPage: true,
        ...options.stubs,
      },
    },
  });
};

describe('ClientOverlayWrapper.vue', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
  });

  afterEach(() => {
    document.body.innerHTML = '';
  });

  describe('Initial State', () => {
    test('overlay is hidden initially', () => {
      const { container } = renderClientOverlay();

      const overlay = container.querySelector('#news_content');
      expect(overlay).toHaveStyle('display: none');
    });

    test('content container is hidden initially', () => {
      const { container } = renderClientOverlay();

      const newsContent = container.querySelector('#news_content');
      expect(newsContent).toHaveStyle('display: none');
    });

    test('loading icon is not visible initially', () => {
      const { container } = renderClientOverlay();

      const loadingIcon = container.querySelector('#loading_message');
      expect(loadingIcon).toHaveClass('hidden');
    });
  });

  describe('Vue Page Rendering', () => {
    test('overlay becomes visible when Vue page is rendered', async () => {
      const { container } = renderClientOverlay();

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'armory',
      });

      await waitFor(() => {
        const overlay = container.querySelector('#news_content');
        expect(overlay).not.toHaveStyle('display: none');
      });
    });

    test('displays component content for Vue pages', async () => {
      renderClientOverlay({
        stubs: {
          ArmoryPage: { template: '<div>Armory Page</div>' },
        },
      });

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'armory',
      });

      await waitFor(() => {
        expect(screen.getByText('Armory Page')).toBeInTheDocument();
      });
    });
  });

  describe('External Content Rendering', () => {
    test('overlay becomes visible when external content is provided', async () => {
      const { container } = renderClientOverlay();

      const htmlContent = '<div id="test-content">Test</div>';

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const overlay = container.querySelector('#news_content');
        expect(overlay).not.toHaveStyle('display: none');
      });
    });

    test('renders external HTML content in the overlay', async () => {
      const { container } = renderClientOverlay();

      const htmlContent =
        '<div id="workers-overview" class="flex gap-2">Test Content</div>';

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const contentDiv = container.querySelector(
          '#news_content_main_content_inner',
        );
        expect(contentDiv).toBeInTheDocument();
        expect(contentDiv?.textContent).toContain('Test Content');
      });
    });

    test('displays content in the main content area', async () => {
      const { container } = renderClientOverlay();

      const htmlContent = '<div class="test-section">Workforce Data</div>';

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const mainContent = container.querySelector(
          '#news_content_main_content',
        );
        expect(mainContent?.textContent).toContain('Workforce Data');
      });
    });
  });

  describe('Loading State', () => {
    test('shows loading icon when loading is triggered', async () => {
      const { container } = renderClientOverlay();

      gameEventBus.emit('RENDER_BUILDING', {
        loading: true,
      });

      await waitFor(() => {
        const loadingIcon = container.querySelector('#loading_message');
        expect(loadingIcon).not.toHaveClass('hidden');
      });
    });

    test('overlay is visible during loading', async () => {
      const { container } = renderClientOverlay();

      gameEventBus.emit('RENDER_BUILDING', {
        loading: true,
      });

      await waitFor(() => {
        const overlay = container.querySelector('#news_content');
        expect(overlay).not.toHaveStyle('display: none');
      });
    });

    test('hides loading icon when content finishes loading', async () => {
      const { container } = renderClientOverlay();

      gameEventBus.emit('RENDER_BUILDING', {
        loading: true,
      });

      await waitFor(() => {
        const loadingIcon = container.querySelector('#loading_message');
        expect(loadingIcon).not.toHaveClass('hidden');
      });

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: '<div>Content</div>',
      });

      await waitFor(() => {
        const loadingIcon = container.querySelector('#loading_message');
        expect(loadingIcon).toHaveClass('hidden');
      });
    });
  });

  describe('Closing Overlay', () => {
    test('closes overlay when exit button is clicked', async () => {
      const { container } = renderClientOverlay();

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'armory',
      });

      await waitFor(() => {
        const overlay = container.querySelector('#news_content');
        expect(overlay).not.toHaveStyle('display: none');
      });

      const exitButton = screen.getByAltText('Close icon');
      await fireEvent.click(exitButton);

      await waitFor(() => {
        const overlay = container.querySelector('#news_content');
        expect(overlay).toHaveStyle('display: none');
      });
    });

    test('clears content from overlay after closing', async () => {
      const { container } = renderClientOverlay();

      const htmlContent = '<div id="test-div">Test</div>';

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        expect(container.querySelector('#test-div')).toBeInTheDocument();
      });

      const exitButton = screen.getByAltText('Close icon');
      await fireEvent.click(exitButton);

      await waitFor(() => {
        expect(container.querySelector('#test-div')).not.toBeInTheDocument();
      });
    });

    test('exit button is always present in the DOM', () => {
      const { container } = renderClientOverlay();

      const exitButton = container.querySelector('.cont_exit');
      expect(exitButton).toBeInTheDocument();
    });
  });

  describe('HTML Rendering', () => {
    test('renders complex HTML structures correctly', async () => {
      const { container } = renderClientOverlay();

      const complexHtml = `
        <div id="overview">Overview</div>
        <div id="details">Details</div>
        <div id="settings">Settings</div>
      `;

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: complexHtml,
      });

      await waitFor(() => {
        expect(container.querySelector('#overview')).toBeInTheDocument();
        expect(container.querySelector('#details')).toBeInTheDocument();
        expect(container.querySelector('#settings')).toBeInTheDocument();
      });
    });

    test('handles and displays malformed HTML', async () => {
      const { container } = renderClientOverlay();

      const malformedHtml = '<div id="test"><p>Unclosed paragraph</p>';

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: malformedHtml,
      });

      await waitFor(() => {
        const overlay = container.querySelector('#news_content');
        expect(overlay).not.toHaveStyle('display: none');
      });
    });

    test('preserves nested HTML structure in display', async () => {
      const { container } = renderClientOverlay();

      const htmlContent = '<div id="parent"><span id="child">Text</span></div>';

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const parentDiv = container.querySelector('#parent');
        const childSpan = container.querySelector('#child');

        expect(parentDiv).toBeInTheDocument();
        expect(childSpan).toBeInTheDocument();
        expect(childSpan?.textContent).toBe('Text');
      });
    });
  });

  describe('Layout and Structure', () => {
    test('overlay container has correct structure', () => {
      const { container } = renderClientOverlay();

      expect(container.querySelector('#news')).toBeInTheDocument();
      expect(container.querySelector('#news_content')).toBeInTheDocument();
      expect(
        container.querySelector('#news_content_side_panel'),
      ).toBeInTheDocument();
      expect(
        container.querySelector('#news_content_main_content'),
      ).toBeInTheDocument();
    });

    test('side panel is present in the layout', () => {
      const { container } = renderClientOverlay();

      const sidePanel = container.querySelector('#news_content_side_panel');
      expect(sidePanel).toBeInTheDocument();
    });

    test('exit button has correct attributes', () => {
      const { container } = renderClientOverlay();

      const exitButton = container.querySelector(
        '.cont_exit',
      ) as HTMLImageElement;

      expect(exitButton.src).toContain('/images/exit.png');
    });

    test('loading icon has correct source', () => {
      const { container } = renderClientOverlay();

      const loadingIcon = container.querySelector(
        '#loading_message',
      ) as HTMLImageElement;

      expect(loadingIcon.src).toContain('/images/loading.png');
    });
  });
});
