<template>
  <teleport to="body">
    <div id="item_tooltip" class="absolute top-0 left-0 hidden">
      <ul
        class="bg-primary-800 absolute z-50 max-w-[125px] border-2 border-black py-2 text-center text-xs text-white"
      >
        <li class="w-[120px]"></li>
        <li class="flex w-[120px] flex-row items-center justify-center gap-1">
          <span id="tooltip_item_price"></span>
          <img class="gold" src="/images/gold.png" :alt="$t('gold icon')" />
        </li>
      </ul>
    </div>
  </teleport>
</template>

<script setup lang="ts" vapor>
/**
 * @deprecated Avoid using. Legacy tooltip driven by imperative DOM
 * manipulation in `utilities/itemTitle.ts`. Prefer using popover instead
 */
defineOptions({});
</script>
