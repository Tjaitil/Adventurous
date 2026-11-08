<template>
  <ListboxRoot v-model="selected" class="grid grid-cols-4 gap-2 p-2">
    <ListboxItem
      v-for="resourceItem in items"
      :key="resourceItem.type"
      :value="resourceItem.type"
      class="data-[state=checked]:ring-2 data-[state=checked]:ring-orange-400"
    >
      <BaseItem
        :item="resourceItem.type"
        :show-amount="false"
        :is-ineractive="false"
      />
    </ListboxItem>
  </ListboxRoot>
</template>

<script setup lang="ts">
import BaseItem from '../base/BaseItem.vue';
import ListboxItem from '../listbox/ListboxItem.vue';
import ListboxRoot from '../listbox/ListboxRoot.vue';

export interface ResourceProductionGridItem {
  type: string;
}

interface Props {
  items: ResourceProductionGridItem[];
}

defineProps<Props>();

const selected = defineModel<string | null>({ default: null });
</script>
