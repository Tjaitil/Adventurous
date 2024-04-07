import SkillInfoList from '@/ui/components/skills/SkillInfoList.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { expect, test } from 'vitest';
import { useSkillsStore } from '@/ui/stores/SkillsStore';
import { createPinia } from 'pinia';
import SkillInfoListItem from '@/ui/components/skills/SkillInfoListItem.vue';
import { UserLevels } from '@/types/UserLevels';
import { MockedUpdateSkillsResponse } from '@/mocks/responses/UpdateSkillsResponse';

const initLevels: UserLevels = {
    username: 'foo',
    adventurer_respect: 1.1,
    warrior_xp: 1,
    farmer_level: 1,
    farmer_xp: 1,
    miner_level: 1,
    miner_xp: 1,
    trader_level: 1,
    trader_xp: 1,
    warrior_level: 1,
    farmer_next_level_xp: 1,
    miner_next_level_xp: 1,
    trader_next_level_xp: 1,
    warrior_next_level_xp: 1,
};

test('component renders', async () => {
    const pinia = createPinia();

    const wrapper = mount(SkillInfoList, {
        global: {
            plugins: [pinia],
        },
        props: {
            initLevels: initLevels,
        },
    });

    useSkillsStore();

    const listItems = wrapper.findAllComponents(SkillInfoListItem);
    expect(listItems.length).toBe(5);
    expect(listItems[1].find('.skill_tooltip').exists()).toBe(false);

    expect(listItems[1].text()).toContain(`${initLevels.farmer_level}`);
});

test('wrapper is shown when clicking skillInfoListItem', async () => {
    const pinia = createPinia();

    const wrapper = mount(SkillInfoList, {
        global: {
            plugins: [pinia],
        },
        props: {
            initLevels: initLevels,
        },
    });

    useSkillsStore();

    const listItems = wrapper.findAllComponents(SkillInfoListItem);
    expect(listItems.length).toBe(5);

    await listItems[1].trigger('click');

    expect(listItems[1].emitted()).toHaveProperty('toggleTooltip');

    expect(listItems[1].find('.skill_tooltip').exists()).toBe(true);

    expect(listItems[1].text()).toContain(
        `Current experience ${initLevels.farmer_xp}`,
    );
});

test('new data is retrieved when xpGainedEvent happens', async () => {
    const pinia = createPinia();

    const wrapper = mount(SkillInfoList, {
        global: {
            plugins: [pinia],
        },
        props: {
            initLevels: initLevels,
        },
    });

    const skillStore = useSkillsStore();

    skillStore.setHandleXpGainedEvent(true);

    await flushPromises();

    const listItems = wrapper.findAllComponents(SkillInfoListItem);
    expect(listItems.length).toBe(5);

    expect(listItems[1].find('.skill_tooltip').exists()).toBe(false);

    expect(listItems[1].text()).toContain(
        `${MockedUpdateSkillsResponse.user_levels.farmer_level}`,
    );
});
