<template>
  <div class="relative">
    <slot></slot>
    <img
      class="armory_view_part pixelated-corners-sm h-12 w-12 border-2 border-gray-950/60 hover:brightness-95"
      :title="part ?? ''"
      :src="AssetPaths.getImagePngPath(part ?? 'none')"
    />
    <img
      v-if="part !== null && part !== 'none' && showRemoveButton"
      src="/images/exit.png"
      :alt="$t('remove item')"
      class="absolute top-0 right-0 h-3 w-3 cursor-pointer"
      @click="$emit('remove-armor')"
    />
  </div>
</template>

<script lang="ts" setup>
import { AssetPaths } from '@/clientScripts/ImagePath';
import type { ArmoryPartsToRenderValue } from '@/types/WarriorArmory';
interface Props {
  part: ArmoryPartsToRenderValue;
  /**
   * Show the remove button on the part if item is equipped.
   */
  showRemoveButton?: boolean;
}
defineProps<Props>();

defineEmits<{
  /**
   * Emitted when the remove button is clicked.
   */
  'remove-armor': [];
}>();
</script>
