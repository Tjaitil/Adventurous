<template>
    <div
        class="skill-level-wrapper relative w-1/2 max-w-[80px] border-2 border-black bg-orange-50 text-black p-2"
        data-wrapper-skill="{{ $skill }}"
        :class="{ 'animate-pulse animate-pulse-custom': isLevelUp }"
        @click="toggleTooltip()"
    >
        <img
            class="w-12 h-12 mx-auto"
            :src="`images/${$props.skill.toLowerCase()} icon.png`"
        />
        {{ props.level }}
        <span
            v-if="isTooltipToggled && skill !== 'adventurer'"
            class="skill_tooltip absolute border-black border-2 bottom-0 left-0 shadow-2xl float-right bg-orange-50 text-black font-bold text-xs text-center w-auto p-1 z-20 inline-block"
            >Current experience {{ props.experience }}
            <br />
            Next level {{ props.nextLevelXp }}
        </span>
    </div>
</template>

<script setup lang="ts">
import { LevelUpAbleSkills } from '@/types/LevelUpSkill';
import { ref } from 'vue';

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
