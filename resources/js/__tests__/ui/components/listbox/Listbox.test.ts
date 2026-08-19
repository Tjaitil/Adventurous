import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import { h } from 'vue';
import ListboxRoot from '@/ui/components/listbox/ListboxRoot.vue';
import ListboxItem from '@/ui/components/listbox/ListboxItem.vue';

const items = ['wheat', 'corn', 'barley'];

function mountListbox(modelValue: string | null = null) {
  return mount(ListboxRoot, {
    props: { modelValue, 'onUpdate:modelValue': () => {} },
    slots: {
      default: () =>
        items.map(item =>
          h(ListboxItem, { key: item, value: item }, () => item),
        ),
    },
  });
}

describe('ListboxRoot + ListboxItem', () => {
  test('renders one role="option" per item', () => {
    const wrapper = mountListbox();

    expect(wrapper.findAll('[role="option"]').length).toBe(items.length);
  });

  test('renders a role="listbox" container', () => {
    const wrapper = mountListbox();

    expect(wrapper.find('[role="listbox"]').exists()).toBe(true);
  });

  test('clicking an item emits update:modelValue with its value', async () => {
    const wrapper = mountListbox();

    const options = wrapper.findAll('[role="option"]');
    await options[1].trigger('click');

    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['corn']);
  });

  test('marks the selected item with data-state="checked"', () => {
    const wrapper = mountListbox('corn');

    const options = wrapper.findAll('[role="option"]');
    expect(options[0].attributes('data-state')).toBe('unchecked');
    expect(options[1].attributes('data-state')).toBe('checked');
    expect(options[2].attributes('data-state')).toBe('unchecked');
  });

  test('re-clicking the selected item keeps it selected instead of deselecting', async () => {
    const wrapper = mountListbox('corn');

    const options = wrapper.findAll('[role="option"]');
    await options[1].trigger('click');

    expect(options[1].attributes('data-state')).toBe('checked');
  });

  test('arrow-key navigation moves the roving highlight between items', async () => {
    const wrapper = mountListbox();

    const listbox = wrapper.find('[role="listbox"]');
    await listbox.trigger('keydown', { key: 'ArrowDown' });

    const options = wrapper.findAll('[role="option"]');
    expect(options[0].attributes('tabindex')).toBe('0');
    expect(options[1].attributes('tabindex')).toBe('-1');
  });
});
