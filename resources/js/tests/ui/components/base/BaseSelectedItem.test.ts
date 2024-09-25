import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import BaseSelectedItem from '@/ui/components/base/BaseSelectedItem.vue';

describe('BaseSelectedItem component', () => {
    test('item is rendered correctly when item is passed', () => {
        const wrapper = mount(BaseSelectedItem, {
            props: {
                item: 'iron sword',
                amount: 0,
            },
        });

        const image = wrapper.find('img');
        expect(image.element.src).toContain('iron%20sword.png');
    });

    test('item is removed when image is clicked', async () => {
        const wrapper = mount(BaseSelectedItem, {
            props: {
                item: 'iron sword',
                amount: 0,
            },
        });

        const image = wrapper.find('img');
        await image.trigger('click');

        const updateEvent = wrapper.emitted('update:item');
        expect(updateEvent && updateEvent[0]).toEqual([null]);
    });

    // test('item is rendered correctly when item is not passed', () => {

    test('amount input is rendered when prop is passed', () => {
        const wrapper = mount(BaseSelectedItem, {
            props: {
                item: 'iron sword',
                amount: 0,
                showAmountInput: true,
            },
        });

        const image = wrapper.find('img');
        expect(image.element.src).toContain('iron%20sword.png');

        const input = wrapper.find('#selected-item-amount');

        expect(input.exists()).toBe(true);
    });

    test('item is empty when no item', () => {
        const wrapper = mount(BaseSelectedItem, {
            props: {
                item: null,
                amount: 0,
            },
        });

        expect(wrapper.find('img').exists()).toBe(false);
    });

    test('amount input is not rendered when prop is not passed', () => {
        const wrapper = mount(BaseSelectedItem, {
            props: {
                item: 'iron sword',
                amount: 0,
            },
        });

        const image = wrapper.find('img');
        expect(image.element.src).toContain('iron%20sword.png');

        const input = wrapper.find('#selected-item-amount');

        expect(input.exists()).toBe(false);
    });

    test('amount input is updated when amount is changed', async () => {
        const wrapper = mount(BaseSelectedItem, {
            props: {
                item: 'iron sword',
                amount: 0,
                showAmountInput: true,
            },
        });

        const input = wrapper.find('#selected-item-amount');

        await input.setValue(10);

        expect(wrapper.emitted()).toHaveProperty('update:amount');
    });
});
