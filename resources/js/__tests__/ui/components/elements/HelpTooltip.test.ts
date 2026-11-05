import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, test, vi } from 'vitest';
import HelpTooltip from '@/ui/components/elements/HelpTooltip.vue';

const mocks = { $t: (key: string) => key };

describe('HelpTooltip component', () => {
  const showPopover = vi.fn();
  const hidePopover = vi.fn();

  beforeEach(() => {
    showPopover.mockClear();
    hidePopover.mockClear();
    HTMLElement.prototype.showPopover = showPopover;
    HTMLElement.prototype.hidePopover = hidePopover;
  });

  const mountComponent = () =>
    mount(HelpTooltip, {
      slots: { default: 'Help text' },
      global: { mocks },
    });

  test('renders slot content in a manual popover', () => {
    const wrapper = mountComponent();
    const popover = wrapper.find('[role="tooltip"]');

    expect(popover.attributes('popover')).toBe('manual');
    expect(popover.text()).toBe('Help text');
  });

  test('button references tooltip via aria-describedby', () => {
    const wrapper = mountComponent();

    expect(wrapper.find('button').attributes('aria-describedby')).toBe(
      wrapper.find('[role="tooltip"]').attributes('id'),
    );
  });

  test('shows popover on mouseenter and hides on mouseleave', async () => {
    const wrapper = mountComponent();

    await wrapper.trigger('mouseenter');
    expect(showPopover).toHaveBeenCalledTimes(1);

    await wrapper.trigger('mouseleave');
    expect(hidePopover).toHaveBeenCalledTimes(1);
  });

  test('shows popover on focusin and hides on focusout', async () => {
    const wrapper = mountComponent();

    await wrapper.find('button').trigger('focusin');
    expect(showPopover).toHaveBeenCalledTimes(1);

    await wrapper.find('button').trigger('focusout');
    expect(hidePopover).toHaveBeenCalledTimes(1);
  });
});
