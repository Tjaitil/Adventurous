<template>
  <RekaListboxRoot
    :model-value="selected ?? undefined"
    selection-behavior="replace"
    :orientation="orientation"
    @update:model-value="selected = ($event as T | undefined) ?? null"
  >
    <RekaListboxContent :class="$attrs.class">
      <slot />
    </RekaListboxContent>
  </RekaListboxRoot>
</template>

<script setup lang="ts" generic="T extends AcceptableValue = string">
import {
  type AcceptableValue,
  ListboxContent as RekaListboxContent,
  ListboxRoot as RekaListboxRoot,
} from 'reka-ui';

defineOptions({ inheritAttrs: false });

interface Props {
  orientation?: 'vertical' | 'horizontal';
}

withDefaults(defineProps<Props>(), {
  orientation: 'vertical',
});

const selected = defineModel<T | null>({ default: null });
</script>
