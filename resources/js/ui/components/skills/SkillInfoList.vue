<template>
  <div class="mt-1 flex flex-row flex-wrap">
    <SkillInfoListItem
      skill="adventurer"
      :next-level-xp="0"
      :level="levels.adventurer_respect"
      :experience="0"
      :is-level-up="false"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="farmer"
      :next-level-xp="levels.farmer_next_level_xp"
      :level="levels.farmer_level"
      :experience="levels.farmer_xp"
      :is-level-up="levelUpStatus.farmer"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="miner"
      :next-level-xp="levels.miner_next_level_xp"
      :level="levels.miner_level"
      :experience="levels.miner_xp"
      :is-level-up="levelUpStatus.miner"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="trader"
      :next-level-xp="levels.trader_next_level_xp"
      :level="levels.trader_level"
      :experience="levels.trader_xp"
      :is-level-up="levelUpStatus.trader"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
    <SkillInfoListItem
      skill="warrior"
      :next-level-xp="levels.warrior_next_level_xp"
      :level="levels.warrior_level"
      :experience="levels.warrior_xp"
      :is-level-up="levelUpStatus.warrior"
      @toggle-tooltip="toggleSelectedSkillTooltip"
    />
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue';
import SkillInfoListItem from './SkillInfoListItem.vue';
import { useSkillsStore } from '@/ui/stores/SkillsStore';
import { UserLevels } from '@/types/UserLevels';
import { LevelUpAbleSkills } from '@/types/Skill';
import { UpdateSkillsResponse } from '@/types/Responses/UpdateSkillsResponse';
import { CustomFetchApi } from '@/CustomFetchApi';

interface Props {
  initLevels: UserLevels;
}

const props = defineProps<Props>();
const store = useSkillsStore();

const levels = ref<UserLevels>(props.initLevels);
store.setUserLevelsResource(props.initLevels);
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
      updateSkills();
    }
  },
);

const updateSkills = async () => {
  await CustomFetchApi.post<UpdateSkillsResponse>('/skills/update').then(
    response => {
      if (response.data.new_levels.length > 0) {
        response.data.new_levels.forEach(levelUpSkill => {
          levelUpStatus[levelUpSkill.skill] = true;
          levels[levelUpSkill.skill + '_level'] = levelUpSkill.new_level;
        });
        store.UserLevelsResource = levels.value;
      }
      levels.value = response.data.user_levels;
    },
  );
  store.setHandleXpGainedEvent(false);
};

const toggleSelectedSkillTooltip = (skill: LevelUpAbleSkills) => {
  if (levelUpStatus[skill] === true) {
    levelUpStatus[skill] = false;
  }
  if (selectedSkillTooltip.value === skill) {
    selectedSkillTooltip.value = null;
  } else {
    selectedSkillTooltip.value = skill;
  }
};
</script>
