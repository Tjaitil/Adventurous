import SkillInfoList from '@/ui/components/skills/SkillInfoList.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { expect, test } from 'vitest';
import { useSkillsStore } from '@/ui/stores/SkillsStore';
import { createPinia } from 'pinia';
import SkillInfoListItem from '@/ui/components/skills/SkillInfoListItem.vue';
import { UserLevels } from '@/types/UserLevels';
import { MockedUpdateSkillsResponse } from '@/mocks/responses/UpdateSkillsResponse';
import { i18n } from '@/ui/main';

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
      plugins: [pinia, i18n],
    },
    props: {
      initLevels: initLevels,
    },
  });

  const skillStore = useSkillsStore();
  skillStore.setUserLevelsResource(initLevels);
  await wrapper.vm.$nextTick();

  const listItems = wrapper.findAllComponents(SkillInfoListItem);
  expect(listItems.length).toBe(5);
  expect(listItems[1].find('.skill_tooltip').exists()).toBe(false);

  expect(listItems[1].text()).toContain(initLevels.farmer_level.toString());
});

test('wrapper is shown when clicking skillInfoListItem', async () => {
  const pinia = createPinia();

  const wrapper = mount(SkillInfoList, {
    global: {
      plugins: [pinia, i18n],
    },
    props: {
      initLevels: initLevels,
    },
  });

  const skillStore = useSkillsStore();
  skillStore.setUserLevelsResource(initLevels);
  await wrapper.vm.$nextTick();

  const listItems = wrapper.findAllComponents(SkillInfoListItem);
  expect(listItems.length).toBe(5);

  await listItems[1].trigger('click');
  await wrapper.vm.$nextTick();
  await flushPromises();

  expect(listItems[1].find('.skill_tooltip').exists()).toBe(true);

  expect(listItems[1].text()).toContain(
    `Current experience ${initLevels.farmer_xp.toString()}`,
  );
});

test('new data is retrieved when xpGainedEvent happens', async () => {
  const pinia = createPinia();

  const wrapper = mount(SkillInfoList, {
    global: {
      plugins: [pinia, i18n],
    },
    props: {
      initLevels: initLevels,
    },
  });

  // Initialize store after mounting with pinia
  const skillStore = useSkillsStore();
  skillStore.setUserLevelsResource(initLevels);

  skillStore.setHandleXpGainedEvent(true);

  await flushPromises();

  const listItems = wrapper.findAllComponents(SkillInfoListItem);
  expect(listItems.length).toBe(5);

  expect(listItems[1].find('.skill_tooltip').exists()).toBe(false);

  expect(listItems[1].text()).toContain(
    MockedUpdateSkillsResponse.user_levels.farmer_level.toString(),
  );
});
