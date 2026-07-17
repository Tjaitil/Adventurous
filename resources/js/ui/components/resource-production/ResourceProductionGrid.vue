<template>
  <div class="grid grid-cols-4 gap-2 p-2">
    <BaseRadio
      v-for="resourceItem in items"
      :id="`resource-production-${resourceItem.type}`"
      :key="resourceItem.type"
      v-model="selected"
      name="resource-production-type"
      :value="resourceItem.type"
      class="flex-col items-center"
    >
      <BaseItem :item="resourceItem.type" :show-amount="false" />
    </BaseRadio>
  </div>
</template>

<script setup lang="ts">
import BaseRadio from '../base/BaseRadio.vue';
import BaseItem from '../base/BaseItem.vue';

export interface ResourceProductionGridItem {
  type: string;
}

interface Props {
  items: ResourceProductionGridItem[];
}

defineProps<Props>();

const selected = defineModel<string | null>({ default: null });
</script>
