import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import { i18n } from '@/ui/main';
import ResourceProductionCountdownPanel from '@/ui/components/resource-production/ResourceProductionCountdownPanel.vue';

const remainder = { days: 0, hours: 1, minutes: 2, seconds: 3 };

describe('ResourceProductionCountdownPanel component', () => {
  test('shows info text with no buttons when no action is active', () => {
    const wrapper = mount(ResourceProductionCountdownPanel, {
      global: { plugins: [i18n] },
      props: {
        infoText: 'No crops growing',
        remainder,
        isFinished: false,
        isActionActive: false,
        cancelActionText: 'Cancel growing',
        finishActionText: 'Harvest',
      },
    });

    expect(wrapper.text()).toContain('No crops growing');
    expect(wrapper.findComponent({ name: 'UButton' }).exists()).toBe(false);
  });

  test('shows the cancel button and countdown while the action is running', () => {
    const wrapper = mount(ResourceProductionCountdownPanel, {
      global: { plugins: [i18n] },
      props: {
        infoText: 'Growing Wheat',
        remainder,
        isFinished: false,
        isActionActive: true,
        cancelActionText: 'Cancel growing',
        finishActionText: 'Harvest',
      },
    });

    expect(wrapper.text()).toContain('Growing Wheat');
    expect(wrapper.text()).toContain('Cancel growing');
    expect(wrapper.text()).not.toContain('Harvest');
  });

  test('shows the finish button once the action is finished', () => {
    const wrapper = mount(ResourceProductionCountdownPanel, {
      global: { plugins: [i18n] },
      props: {
        infoText: 'Finished',
        remainder,
        isFinished: true,
        isActionActive: true,
        cancelActionText: 'Cancel growing',
        finishActionText: 'Harvest',
      },
    });

    expect(wrapper.text()).toContain('Harvest');
    expect(wrapper.text()).not.toContain('Cancel growing');
  });

  test('emits cancel and finish when the buttons are clicked', async () => {
    const wrapper = mount(ResourceProductionCountdownPanel, {
      global: { plugins: [i18n] },
      props: {
        infoText: 'Growing Wheat',
        remainder,
        isFinished: false,
        isActionActive: true,
        cancelActionText: 'Cancel growing',
        finishActionText: 'Harvest',
      },
    });

    await wrapper.find('button').trigger('click');
    expect(wrapper.emitted('cancel')).toBeTruthy();
  });
});
