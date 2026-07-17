import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import { i18n } from '@/ui/main';
import WorkforceInput from '@/ui/components/resource-production/WorkforceInput.vue';

describe('WorkforceInput component', () => {
  test('renders the max workforce amount', () => {
    const wrapper = mount(WorkforceInput, {
      global: { plugins: [i18n] },
      props: { max: 7, modelValue: 0 },
    });

    expect(wrapper.text()).toContain('(7)');
  });

  test('emits update:modelValue when changed', async () => {
    const wrapper = mount(WorkforceInput, {
      global: { plugins: [i18n] },
      props: { max: 7, modelValue: 0 },
    });

    const input = wrapper.find('input');
    await input.setValue(5);

    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([5]);
  });
});
