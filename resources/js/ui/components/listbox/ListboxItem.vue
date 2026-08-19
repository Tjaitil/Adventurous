<template>
  <RekaListboxItem :value="value" :disabled="disabled" :class="$attrs.class">
    <slot />
  </RekaListboxItem>
</template>

<script setup lang="ts" generic="T extends AcceptableValue = string">
import { type AcceptableValue, ListboxItem as RekaListboxItem } from 'reka-ui';

defineOptions({ inheritAttrs: false });

interface Props {
  value: T;
  disabled?: boolean;
}

defineProps<Props>();
</script>
