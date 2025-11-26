<template>
  <div class="mt-1 flex flex-row flex-wrap">
    <SkillInfoListItem
      skill="adventurer"
      :next-level-xp="0"
      :level="store.UserLevelsResource.adventurer_respect"
      :experience="0"
      :is-level-up="false"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="farmer"
      :next-level-xp="store.UserLevelsResource.farmer_next_level_xp"
      :level="store.UserLevelsResource.farmer_level"
      :experience="store.UserLevelsResource.farmer_xp"
      :is-level-up="levelUpStatus.farmer"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="miner"
      :next-level-xp="store.UserLevelsResource.miner_next_level_xp"
      :level="store.UserLevelsResource.miner_level"
      :experience="store.UserLevelsResource.miner_xp"
      :is-level-up="levelUpStatus.miner"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="trader"
      :next-level-xp="store.UserLevelsResource.trader_next_level_xp"
      :level="store.UserLevelsResource.trader_level"
      :experience="store.UserLevelsResource.trader_xp"
      :is-level-up="levelUpStatus.trader"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="warrior"
      :next-level-xp="store.UserLevelsResource.warrior_next_level_xp"
      :level="store.UserLevelsResource.warrior_level"
      :experience="store.UserLevelsResource.warrior_xp"
      :is-level-up="levelUpStatus.warrior"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue';
import SkillInfoListItem from './SkillInfoListItem.vue';
import { useSkillsStore } from '@/ui/stores/SkillsStore';
import type { LevelUpAbleSkills } from '@/types/Skill';
import type { UpdateSkillsResponse } from '@/types/Responses/UpdateSkillsResponse';
import { CustomFetchApi } from '@/CustomFetchApi';

const store = useSkillsStore();

const levelUpStatus = reactive({
  farmer: false,
  miner: false,
  trader: false,
  warrior: false,
});

const selectedSkillTooltip = ref<LevelUpAbleSkills | null>(null);

watch(
  () => store.handleXpGainedEvent,
  newValue => {
    if (newValue) {
      void updateSkills();
    }
  },
);

const updateSkills = async () => {
  await CustomFetchApi.post<UpdateSkillsResponse>('/skills/update').then(
    response => {
      if (response.data.new_levels.length > 0) {
        response.data.new_levels.forEach(levelUpSkill => {
          levelUpStatus[levelUpSkill.skill] = true;
        });
        store.UserLevelsResource = response.data.user_levels;
      }
      store.setUserLevelsResource(response.data.user_levels);
    },
  );
  store.setHandleXpGainedEvent(false);
};

const toggleSelectedSkillTooltip = (skill: LevelUpAbleSkills) => {
  if (levelUpStatus[skill]) {
    levelUpStatus[skill] = false;
  }
  if (selectedSkillTooltip.value === skill) {
    selectedSkillTooltip.value = null;
  } else {
    selectedSkillTooltip.value = skill;
  }
};
</script>
