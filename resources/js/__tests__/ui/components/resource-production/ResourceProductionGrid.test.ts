import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import { i18n } from '@/ui/main';
import ResourceProductionGrid from '@/ui/components/resource-production/ResourceProductionGrid.vue';

describe('ResourceProductionGrid component', () => {
  test('renders a radio option per item', () => {
    const wrapper = mount(ResourceProductionGrid, {
      global: { plugins: [i18n] },
      props: {
        items: [{ type: 'wheat' }, { type: 'corn' }],
        modelValue: null,
      },
    });

    expect(wrapper.findAll('input[type="radio"]').length).toBe(2);
  });

  test('emits update:modelValue with the selected type', async () => {
    const wrapper = mount(ResourceProductionGrid, {
      global: { plugins: [i18n] },
      props: {
        items: [{ type: 'wheat' }, { type: 'corn' }],
        modelValue: null,
      },
    });

    const radios = wrapper.findAll('input[type="radio"]');
    await radios[1].setValue();

    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['corn']);
  });
});
