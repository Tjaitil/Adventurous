import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import { i18n } from '@/ui/main';
import ResourceProductionGrid from '@/ui/components/resource-production/ResourceProductionGrid.vue';

describe('ResourceProductionGrid component', () => {
  test('renders a listbox option per item', () => {
    const wrapper = mount(ResourceProductionGrid, {
      global: { plugins: [i18n] },
      props: {
        items: [{ type: 'wheat' }, { type: 'corn' }],
        modelValue: null,
      },
    });

    expect(wrapper.findAll('[role="option"]').length).toBe(2);
  });

  test('emits update:modelValue with the selected type', async () => {
    const wrapper = mount(ResourceProductionGrid, {
      global: { plugins: [i18n] },
      props: {
        items: [{ type: 'wheat' }, { type: 'corn' }],
        modelValue: null,
      },
    });

    const options = wrapper.findAll('[role="option"]');
    await options[1].trigger('click');

    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['corn']);
  });
});
