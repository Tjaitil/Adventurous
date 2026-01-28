<template>
  <component
    :is="renderButton ? 'button' : 'div'"
    class="relative mb-0 focus-visible:outline-none"
    :tabindex="renderButton ? 0 : -1"
    :class="{
      'pixelated-outline-sm after:border-primary-700': renderButton,
    }"
    @click="$emit('remove-armor')"
  >
    <slot></slot>
    <img
      class="armory_view_part pixelated-corners-sm w-full border-2 border-gray-950/60 hover:brightness-95"
      :title="part ?? ''"
      :src="AssetPaths.getImagePngPath(part ?? 'none')"
    />
    <img
      v-if="hasItemEquipped && showRemoveButton"
      src="/images/exit.png"
      :alt="$t('remove item')"
      class="absolute top-0 right-0 h-3 w-3 cursor-pointer"
    />
  </component>
</template>

<script lang="ts" setup>
import { AssetPaths } from '@/clientScripts/ImagePath';
import type { ArmoryPartsToRenderValue } from '@/types/WarriorArmory';
import { computed } from 'vue';
interface Props {
  part: ArmoryPartsToRenderValue;
  /**
   * Show the remove button on the part if item is equipped.
   */
  showRemoveButton?: boolean;
}
const { part, showRemoveButton } = defineProps<Props>();

defineEmits<{
  /**
   * Emitted when the remove button is clicked.
   */
  'remove-armor': [];
}>();

const hasItemEquipped = computed(() => {
  return part !== null && part !== 'none';
});

const renderButton = computed(() => {
  return hasItemEquipped.value && showRemoveButton;
});
</script>
