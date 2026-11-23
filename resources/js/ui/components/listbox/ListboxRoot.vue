<template>
  <RekaListboxRoot
    ref="listboxRootRef"
    v-model="selected"
    selection-behavior="replace"
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

const selected = defineModel<T | null>({ default: null });

const listboxRootRef = useTemplateRef('listboxRootRef');

/**
 * Only the last row of a row-major grid can be ragged, so column count is
 * the number of leading elements sharing the first element's offsetTop.
 * A vertical list resolves to 1, a horizontal row to its item count.
 */
const getColumnCount = (elements: HTMLElement[]): number => {
  const firstRowTop = elements[0].offsetTop;
  const columns = elements.findIndex(
    element => element.offsetTop !== firstRowTop,
  );

  return columns === -1 ? elements.length : columns;
};

const moveHighlight = (rowOffset: number, columnOffset: number) => {
  const instance = listboxRootRef.value;
  if (!instance) return;

  const items = instance.getItems();
  if (!items.length) return;

  const currentIndex: number = instance.highlightedElement
    ? items.findIndex(item => item.ref === instance.highlightedElement)
    : -1;

  if (currentIndex === -1) {
    const isForward = rowOffset + columnOffset > 0;
    instance.highlightItem(items[isForward ? 0 : items.length - 1].value);
    return;
  }

  const columns = getColumnCount(items.map(item => item.ref));
  const nextColumn = (currentIndex % columns) + columnOffset;
  if (nextColumn < 0 || nextColumn >= columns) return;

  const targetIndex = currentIndex + rowOffset * columns + columnOffset;
  if (targetIndex < 0 || targetIndex >= items.length) return;

  instance.highlightItem(items[targetIndex].value);
};

const arrowKeyOffsets: Record<string, [row: number, column: number]> = {
  ArrowDown: [1, 0],
  ArrowUp: [-1, 0],
  ArrowRight: [0, 1],
  ArrowLeft: [0, -1],
};

const onKeydownCapture = (event: KeyboardEvent) => {
  const offsets = arrowKeyOffsets[event.key];

  event.preventDefault();
  event.stopPropagation();
  moveHighlight(...offsets);
};
</script>
