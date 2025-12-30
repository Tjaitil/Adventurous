<template>
  <UCard variant="soft">
    <img
      src="/images/trader icon.png"
      :alt="$t('Trader icon')"
      class="mx-auto w-12"
    />
    <div
      v-if="status.assignment_id !== 0 && status.trader_assignment != null"
      class="mt-4"
    >
      <h3 class="text-lg font-bold">{{ $t('Current assignment') }}</h3>
      <div class="mt-2 grid grid-cols-2 gap-2 gap-x-3 px-2">
        <div>
          <BaseItem :item="status.trader_assignment.cargo" />
        </div>
        <div>
          <BaseIcon icon="route" :title="$t('Route icon')" />
          <span class="font-bold">
            {{ formatLocationName(status.trader_assignment.base) }}
            ->
            {{ formatLocationName(status.trader_assignment.destination) }}
          </span>
        </div>
        <div class="col-span-2">
          <span class="mb-2 block">{{ $t('Progress') }}</span>
          <UProgress
            color="success"
            :model-value="10"
            :max="status.trader_assignment.assignment_amount"
            :ui="{
              root: 'relative',
              base: 'h-6 relative left-0 top-0 border-primary-800 border-3 pixelated-corners-sm rounded-none',
              status: 'progress-status-overlay-text',
            }"
          >
            <template #status> {{ 5 }} / {{ 100 }} </template>
          </UProgress>
        </div>
        <div>
          {{ $t('Assignment type') }}<br />
          <span class="font-bold">{{
            jsUcfirst($t(status.trader_assignment.assignment_type))
          }}</span>
        </div>
        <div>
          {{ $t('Cart Capacity') }} <br />
          <span class="font-bold">
            {{ status.cart_amount }} / {{ status.cart.capasity }}</span
          >
        </div>
      </div>
    </div>
    <div v-else class="mt-4">
      {{ $t('Nothing happening') }}
    </div>
  </UCard>
</template>

<script setup lang="ts">
import type { Trader } from '@/types/ProficiencyStatuses';
import { formatLocationName } from '@/utilities/formatters';
import BaseItem from '../base/BaseItem.vue';
import BaseIcon from '../base/BaseIcon.vue';
import { jsUcfirst } from '@/utilities/uppercase';

interface Props {
  status: Trader;
}
defineProps<Props>();
</script>
