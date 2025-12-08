<template>
  <div
    class="skill-level-wrapper relative w-1/2 max-w-[80px] border-2 border-black bg-orange-50 p-2 text-center text-black"
    data-wrapper-skill="{{ $skill }}"
    :class="{ 'animate-pulse-custom animate-pulse': isLevelUp }"
    @click="toggleTooltip()"
  >
    <img
      class="mx-auto h-12 w-12 pb-1"
      :src="`images/${$props.skill.toLowerCase()} icon.png`"
      :alt="t('{skill} icon', { skill })"
    />
    {{ level }}
    <span
      v-if="isTooltipToggled && skill !== 'adventurer'"
      class="skill_tooltip absolute bottom-0 left-0 z-20 float-right inline-block w-auto border-2 border-black bg-orange-50 p-1 text-center text-xs font-bold text-black shadow-2xl"
      >{{ t('Current experience') }} {{ experience }}
      <br />
      {{ t('Next level') }} {{ nextLevelXp }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { LevelUpAbleSkills } from '@/types/Skill';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  skill: LevelUpAbleSkills | 'adventurer';
  level: number | string;
  experience: number;
  nextLevelXp: number;
  isLevelUp: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  toggleTooltip: [skill: string];
}>();

const { t } = useI18n();
const isTooltipToggled = ref(false);

const toggleTooltip = () => {
  isTooltipToggled.value = !isTooltipToggled.value;
  emit('toggleTooltip', props.skill);
};
</script>

<style scoped>
.animate-pulse-custom {
  animation-duration: 1.5s;
}
</style>
