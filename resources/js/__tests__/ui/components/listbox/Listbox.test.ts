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

  test('left/right do nothing in the default "list" layout', async () => {
    const wrapper = mountListbox();

    const listbox = wrapper.find('[role="listbox"]');
    // reka-ui auto-highlights the first item shortly after mount, independent
    // of any keypress, so establish a known, keypress-driven position first.
    await listbox.trigger('keydown', { key: 'ArrowDown' });
    await listbox.trigger('keydown', { key: 'ArrowDown' });

    const options = wrapper.findAll('[role="option"]');
    expect(options[1].attributes('tabindex')).toBe('0');

    await listbox.trigger('keydown', { key: 'ArrowRight' });

    expect(options[1].attributes('tabindex')).toBe('0');
    expect(options[2].attributes('tabindex')).toBe('-1');
  });
});

describe('ListboxRoot layout="grid"', () => {
  const gridItems = ['a', 'b', 'c', 'd', 'e', 'f'];

  function mountGridListbox(columns: number, modelValue: string | null = null) {
    return mount(ListboxRoot, {
      props: { modelValue, 'onUpdate:modelValue': () => {}, layout: 'grid', columns },
      slots: {
        default: () =>
          gridItems.map(item => h(ListboxItem, { key: item, value: item }, () => item)),
      },
    });
  }

  test('ArrowRight/ArrowLeft still move by one item, same as a list', async () => {
    const wrapper = mountGridListbox(3);

    const listbox = wrapper.find('[role="listbox"]');
    await listbox.trigger('keydown', { key: 'ArrowRight' });
    await listbox.trigger('keydown', { key: 'ArrowRight' });

    const options = wrapper.findAll('[role="option"]');
    expect(options[1].attributes('tabindex')).toBe('0');
  });

  test('ArrowDown moves the highlight to the same column, one row down', async () => {
    const wrapper = mountGridListbox(3);

    const listbox = wrapper.find('[role="listbox"]');
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // highlights index 0 ("a")
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // moves to index 3 ("d")

    const options = wrapper.findAll('[role="option"]');
    expect(options[3].attributes('tabindex')).toBe('0');
  });

  test('ArrowUp moves the highlight to the same column, one row up', async () => {
    const wrapper = mountGridListbox(3);

    const listbox = wrapper.find('[role="listbox"]');
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // index 0 ("a")
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // index 3 ("d")
    await listbox.trigger('keydown', { key: 'ArrowUp' }); // back to index 0 ("a")

    const options = wrapper.findAll('[role="option"]');
    expect(options[0].attributes('tabindex')).toBe('0');
  });

  test('ArrowDown does nothing past the last row', async () => {
    const wrapper = mountGridListbox(3);

    const listbox = wrapper.find('[role="listbox"]');
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // index 0
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // index 3
    await listbox.trigger('keydown', { key: 'ArrowDown' }); // index 6 doesn't exist, no-op

    const options = wrapper.findAll('[role="option"]');
    expect(options[3].attributes('tabindex')).toBe('0');
  });
});
