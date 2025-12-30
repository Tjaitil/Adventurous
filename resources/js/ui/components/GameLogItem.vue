<template>
  <component
    :is="component"
    v-if="log"
    class="my-0 h-full"
    :class="getColorFromType(log.type)"
  >
    <template v-if="Array.isArray(log.message)">
      <template v-for="(part, index) in log.message" :key="index">
        <span v-if="part !== '{gold}'">{{ part }}</span>
        <img
          v-else
          alt=""
          class="gold inline"
          :src="`/images/gold.png`"
          style="margin-left: -6px; margin-top: -4px"
        />
      </template>
    </template>
    <template v-else>
      <span>{{ log.message }}</span>
    </template>
  </component>
</template>

<script setup lang="ts">
import {
  GameLogTypes,
  type ParsedGameLog,
  type GameLogType,
} from '@/types/GameLog';

interface Props {
  component?: 'li' | 'p';
  log: ParsedGameLog;
}
const { component = 'li' } = defineProps<Props>();
const getColorFromType = (type: GameLogType) => {
  switch (type) {
    case GameLogTypes.ERROR:
      return 'text-red-600';
    case GameLogTypes.WARNING:
      return 'text-yellow-600';
    case GameLogTypes.SUCCESS:
      return 'text-green-600';
    default:
      return 'text-black-600';
  }
};
</script>
