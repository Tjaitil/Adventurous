<template>
  <div class="armory_view_part_grid">
    <WarriorArmoryPart
      v-for="(value, key) in armoryPartsToRender"
      :key="key"
      :class="getGridAreaClass(key)"
      :part="value"
      :show-remove-button="!preview"
      @remove-armor="$emit('remove-armor', key)"
    >
      <template v-if="key === 'ammunition'">
        <span class="absolute right-1 bottom-0 text-white">{{
          warriorArmory.ammunition_amount > 0
            ? warriorArmory.ammunition_amount
            : ''
        }}</span>
      </template>
    </WarriorArmoryPart>
  </div>
</template>

<script setup lang="ts">
import type {
  WarriorArmory,
  ArmoryPartsToRender,
  ArmoryPartsKeysToRender,
} from '@/types/WarriorArmory';
import WarriorArmoryPart from './WarriorArmoryPart.vue';
import { computed } from 'vue';

interface Props {
  warriorArmory: WarriorArmory;
  preview: boolean;
}

const { warriorArmory } = defineProps<Props>();
defineEmits<{
  'remove-armor': [item: ArmoryPartsKeysToRender];
}>();

const armoryPartsToRender = computed(
  (): ArmoryPartsToRender => ({
    helm: warriorArmory.helm,
    ammunition: warriorArmory.ammunition,
    right_hand: warriorArmory.right_hand,
    body: warriorArmory.body,
    left_hand: warriorArmory.left_hand,
    legs: warriorArmory.legs,
    boots: warriorArmory.boots,
  }),
);

const getGridAreaClass = (part: ArmoryPartsKeysToRender) => {
  return `${part}-grid-area`;
};
</script>

<style>
.armory_view_part_grid {
  display: grid;
  grid-template-columns: repeat(3, 48px);
  grid-auto-rows: 48px;
  grid-template-areas: '. helm ammunition' 'right_hand body left_hand' '. legs .' '. boots . ';
  justify-content: center;
  gap: 10px;
}

.ammunition-grid-area {
  grid-area: ammunition;
  position: relative;
}
.helm-grid-area {
  grid-area: helm;
}
.left_hand-grid-area {
  grid-area: left_hand;
}
.body-grid-area {
  grid-area: body;
}
.right_hand-grid-area {
  grid-area: right_hand;
}
.legs-grid-area {
  grid-area: legs;
}
.boots-grid-area {
  grid-area: boots;
}
</style>
