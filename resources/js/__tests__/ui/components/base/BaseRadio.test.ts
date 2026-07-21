import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import BaseRadio from '@/ui/components/base/BaseRadio.vue';

describe('BaseRadio component', () => {
  test('checks the input matching the current model value', () => {
    const wrapper = mount(BaseRadio, {
      props: {
        id: 'right',
        name: 'hand',
        value: 'right_hand',
        modelValue: 'right_hand',
      },
    });

    expect((wrapper.find('input').element as HTMLInputElement).checked).toBe(
      true,
    );
  });

  test('emits update:modelValue with its value when selected', async () => {
    const wrapper = mount(BaseRadio, {
      props: {
        id: 'left',
        name: 'hand',
        value: 'left_hand',
        modelValue: 'right_hand',
      },
    });

    await wrapper.find('input').setValue();

    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['left_hand']);
  });
});
