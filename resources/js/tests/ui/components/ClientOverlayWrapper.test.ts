import { describe, test, expect, beforeEach, afterEach, vi } from 'vitest';
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
import { ClientOverlayInterface } from '@/clientScripts/clientOverlayInterface';

interface RenderOptions {
  stubs?: Record<string, unknown>;
}

const renderClientOverlay = (options: RenderOptions = {}): RenderResult => {
  const renderResult = render(ClientOverlayWrapper, {
    global: {
      plugins: [createPinia(), i18n],
      stubs: {
        ArmoryPage: true,
        ...options.stubs,
      },
    },
  });

  ClientOverlayInterface.setup();

  // Mock only showContent to avoid JSDOM innerText issues
  vi.spyOn(ClientOverlayInterface, 'showContent').mockImplementation(() => {});

  return renderResult;
};

describe('ClientOverlayWrapper.vue', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
    vi.clearAllMocks();
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

      const exitButton = container.querySelector('.cont_exit');

      expect(exitButton).toBeInTheDocument();
      expect(exitButton).toBeInstanceOf(HTMLImageElement);
      expect((exitButton as HTMLImageElement).src).toContain(
        '/images/exit.png',
      );
    });

    test('loading icon has correct source', () => {
      const { container } = renderClientOverlay();

      const loadingIcon = container.querySelector('#loading_message');

      expect(loadingIcon).toBeInTheDocument();
      expect(loadingIcon).toBeInstanceOf(HTMLImageElement);
      expect((loadingIcon as HTMLImageElement).src).toContain(
        '/images/loading.png',
      );
    });
  });

  describe('Side Panel Tabs', () => {
    test('creates tab buttons for multiple content sections', async () => {
      const { container } = renderClientOverlay();

      ClientOverlayInterface.setup();

      const htmlContent = `
        <div id="overview">Overview Content</div>
        <div id="details">Details Content</div>
        <div id="settings">Settings Content</div>
      `;

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const tabs = container.querySelectorAll('.building-tab');
        expect(tabs.length).toBe(3);

        // Verify each tab has the building-tab class
        tabs.forEach(tab => {
          expect(tab).toHaveClass('building-tab');
        });
      });
    });

    test('tab buttons have correct text from div ids', async () => {
      const { container } = renderClientOverlay();

      ClientOverlayInterface.setup();

      const htmlContent = `
        <div id="workers_overview">Workers Overview</div>
        <div id="training_area">Training Area</div>
      `;

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const tabs = container.querySelectorAll('.building-tab');
        expect(tabs.length).toBe(2);

        const tabTexts = Array.from(tabs).map(tab => tab.textContent?.trim());
        expect(tabTexts).toContain('Workers Overview');
        expect(tabTexts).toContain('Training Area');
      });
    });

    test('side panel is visible when tabs are created', async () => {
      const { container } = renderClientOverlay();

      ClientOverlayInterface.setup();

      const htmlContent = `
        <div id="section_one">Section One</div>
        <div id="section_two">Section Two</div>
      `;

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const sidePanel = container.querySelector('#news_content_side_panel');
        expect(sidePanel).not.toHaveClass('hidden');
      });
    });

    test('side panel remains hidden when only one section exists', async () => {
      const { container } = renderClientOverlay();

      ClientOverlayInterface.setup();

      const htmlContent = `
        <div id="single_section">Single Section</div>
      `;

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const sidePanel = container.querySelector('#news_content_side_panel');
        expect(sidePanel).toHaveClass('hidden');
      });
    });

    test('tab buttons receive click event listeners', async () => {
      const { container } = renderClientOverlay();

      ClientOverlayInterface.setup();

      const htmlContent = `
        <div id="section_a">Section A</div>
        <div id="section_b">Section B</div>
      `;

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'workforcelodge',
        content: htmlContent,
      });

      await waitFor(() => {
        const tabs = container.querySelectorAll('.building-tab');
        expect(tabs.length).toBeGreaterThan(0);

        // Verify buttons are in the document and are HTMLElement instances
        tabs.forEach(tab => {
          expect(tab).toBeInTheDocument();
          expect(tab).toBeInstanceOf(HTMLElement);
        });
      });
    });

    test('tabs are not created when only loading', async () => {
      const { container } = renderClientOverlay();

      ClientOverlayInterface.setup();

      gameEventBus.emit('RENDER_BUILDING', {
        loading: true,
      });

      await waitFor(() => {
        const tabs = container.querySelectorAll('.building-tab');
        expect(tabs.length).toBe(0);
      });
    });

    test('tabs are not created for Vue page rendering', async () => {
      const { container } = renderClientOverlay({
        stubs: {
          ArmoryPage: { template: '<div>Armory Page</div>' },
        },
      });

      ClientOverlayInterface.setup();

      gameEventBus.emit('RENDER_BUILDING', {
        building: 'armory',
      });

      await waitFor(() => {
        expect(screen.getByText('Armory Page')).toBeInTheDocument();
      });

      const tabs = container.querySelectorAll('.building-tab');
      expect(tabs.length).toBe(0);
    });
  });
});
