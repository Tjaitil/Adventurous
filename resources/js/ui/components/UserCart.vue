<template>
  <UCard
    id="inventory"
    variant="soft"
    class="relative z-20 float-right mt-0 w-full text-white"
  >
    <p class="mb-4">
      {{ $t('User Cart') }}
      <span
        id="inventory-status"
        :class="{
          'not-able-color': userCartStore.isCartFull,
        }"
      >
        {{ userCartStore.cartCurrentLoad }} /
        {{ userCartStore.cartCapacity }}
      </span>
    </p>
    <BaseItem
      v-for="item in userCartStore.cartItems"
      :key="item.item_id"
      :item="item.item.name"
      :amount="item.amount"
    />
  </UCard>
</template>

<script lang="ts" setup>
import BaseItem from '@/ui/components/base/BaseItem.vue';
import { useUserCartStore } from '@/ui/stores/UserCartStore';

const userCartStore = useUserCartStore();
</script>
