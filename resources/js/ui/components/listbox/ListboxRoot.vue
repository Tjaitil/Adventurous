<template>
  <RekaListboxRoot
    ref="listboxRootRef"
    v-model="selected"
    selection-behavior="replace"
    :orientation="layout === 'grid' ? 'horizontal' : orientation"
    @keydown.capture="onKeydownCapture"
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
import { useTemplateRef } from 'vue';

defineOptions({ inheritAttrs: false });

interface Props {
  /**
   * 'list' - a single row/column of items; only the arrow-key pair matching
   * `orientation` moves the highlight (reka-ui's native behavior).
   * 'grid' - a multi-column grid of items; left/right step to the
   * previous/next item, up/down jump a full `columns` to reach the same
   * column in the row above/below.
   */
  layout?: 'list' | 'grid';
  orientation?: 'vertical' | 'horizontal';
  /** Items per row. Only used (and required to be accurate) when layout="grid". */
  columns?: number;
}

const props = withDefaults(defineProps<Props>(), {
  layout: 'list',
  orientation: 'vertical',
  columns: 1,
});

const selected = defineModel<T | null>({ default: null });

const listboxRootRef = useTemplateRef('listboxRootRef');

const moveGridHighlight = (rowOffset: 1 | -1) => {
  const instance = listboxRootRef.value;
  if (!instance) return;

  const items = instance.getItems();
  if (!items.length) return;

  const currentIndex: number = instance.highlightedElement
    ? items.findIndex(item => item.ref === instance.highlightedElement)
    : -1;

  if (currentIndex === -1) {
    instance.highlightItem(items[rowOffset === 1 ? 0 : items.length - 1].value);
    return;
  }

  const targetIndex = currentIndex + rowOffset * props.columns;
  if (targetIndex < 0 || targetIndex >= items.length) return;

  instance.highlightItem(items[targetIndex].value);
};

// In grid layout, reka-ui's underlying orientation is forced to "horizontal"
// so left/right keep their native (±1) behavior; up/down are intercepted
// here, before reka-ui's own keydown handler sees them, and moved by a full
// row (`columns`) instead of by one item.
const onKeydownCapture = (event: KeyboardEvent) => {
  if (props.layout !== 'grid') return;
  if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;

  event.preventDefault();
  event.stopPropagation();
  moveGridHighlight(event.key === 'ArrowDown' ? 1 : -1);
};
</script>
