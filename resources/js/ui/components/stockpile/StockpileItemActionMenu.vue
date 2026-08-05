<template>
  <div
    v-if="stockpileStore.menu.isOpen"
    id="stck_menu"
    ref="actionMenuRef"
    role="menu"
    :aria-label="t('stockpile action menu')"
    class="absolute z-30 w-36 max-w-[130px] overflow-hidden rounded-md border-2 border-stone-600 bg-stone-700 py-2 text-center text-xs text-white drop-shadow-xl"
    :style="menuStyle"
    tabindex="-1"
    @click.stop
    @keydown="handleKeydown"
  >
    <p id="stck-current-item" class="mt-0 mb-2 font-bold">
      {{ selectedMenuItemLabel }}
    </p>
    <ul id="stck-menu-option-list" class="z-50">
      <template v-if="!showCustomAmountInput">
        <li
          v-for="option in menuOptions"
          :key="option.label"
          class="bg-primary-750 w-auto cursor-pointer p-1 hover:bg-stone-600"
        >
          <button
            ref="menuItemRefs"
            role="menuitem"
            tabindex="-1"
            class="w-full"
            @click="onSelect(option.value)"
          >
            {{ option.label }}
          </button>
        </li>
      </template>

      <li
        v-else
        class="bg-primary-750 w-auto p-1"
        role="menuitem"
        tabindex="-1"
      >
        <input
          id="stck_menu_custom_amount"
          v-model="customAmount"
          tabindex="-1"
          class="custom-input w-full border-0 bg-transparent p-0 text-center text-xs"
          type="number"
          min="1"
          :placeholder="menuCustomLabel"
          @keydown.stop
          @keydown.enter.stop="handleCustomAmountEnter"
        />
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { useItemActionMenuStore } from '@/ui/stores/ItemActionMenuStore';
import { jsUcWords } from '@/utilities/uppercase';
import { computed, nextTick, ref, useTemplateRef, onUnmounted } from 'vue';
import { onClickOutside } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import { handleArrowKeys } from '@/utilities/arrowKeyNavigation';

type action = number | 'all';

const emit = defineEmits<{
  (e: 'select', amount: action): void;
}>();

const { t } = useI18n();

const stockpileStore = useItemActionMenuStore();
const actionMenuEl = useTemplateRef('actionMenuRef');
const menuItemEls = useTemplateRef<HTMLButtonElement[]>('menuItemRefs');

const showCustomAmountInput = ref<boolean>(false);

onClickOutside(actionMenuEl, () => {
  stockpileStore.closeMenu();
});

const menuStyle = computed(() => {
  const target = stockpileStore.anchorTarget;
  if (!target) {
    return {};
  }

  const { left, top } = target.getBoundingClientRect();

  return {
    left: `${left}px`,
    top: `${top + window.scrollY + 20}px`,
  };
});

const selectedMenuItemLabel = computed(() =>
  stockpileStore.menu.item ? jsUcWords(stockpileStore.menu.item) : '',
);

const customAmount = ref<number | null>(null);

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    stockpileStore.closeMenu();
    stockpileStore.anchorTarget?.focus();
    return;
  }

  if (e.key === 'Tab') {
    stockpileStore.closeMenu();
    return;
  }

  if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
    if (
      e.target instanceof HTMLInputElement ||
      !(document.activeElement instanceof HTMLButtonElement)
    ) {
      return;
    }
    e.preventDefault();
    handleArrowKeys(e, menuItemEls.value, 'vertical');
  }
};

const onSelect = (amount: number | 'all' | 'x') => {
  if (amount === 'x') {
    showCustomAmountInput.value = true;
    void nextTick(() => {
      const input =
        actionMenuEl.value?.querySelector<HTMLInputElement>('input');
      input?.focus();
    });
    return;
  }

  emit('select', amount);
};

const handleCustomAmountEnter = () => {
  const val = customAmount.value;
  if (typeof val === 'number') {
    emit('select', val);
  }
};

stockpileStore.$subscribe((mutation, state) => {
  if (state.menu.isOpen) {
    void nextTick(() => {
      menuItemEls.value?.[0]?.focus();
    });
  }
});

const menuOptions = computed(() => {
  const actionName = stockpileStore.menu.insert ? 'Insert' : 'Withdraw';

  return [
    { label: `${actionName} 1`, value: 1 as const },
    { label: `${actionName} 5`, value: 5 as const },
    { label: `${actionName} x`, value: 'x' as const },
    { label: `${actionName} all`, value: 'all' as const },
  ];
});

const menuCustomLabel = computed(() =>
  stockpileStore.menu.insert ? 'Insert x' : 'Withdraw x',
);

onUnmounted(() => {
  stockpileStore.closeMenu();
});
</script>
