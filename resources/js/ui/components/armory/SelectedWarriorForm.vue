<template>
  <UCard
    variant="soft"
    class="w-fit"
    :ui="{
      body: 'flex flex-row gap-6',
    }"
  >
    <div class="flex w-60 flex-col gap-y-4">
      <div class="flex items-center gap-2">
        <img
          class="type_icon max-h-12 max-w-12 basis-2/4"
          :src="AssetPaths.getImagePngPath(selectedWarrior.type + ' icon')"
          alt="warrior-icon"
        />
        <div>
          <span class="text-left">
            {{ $t('Soldier') }} #{{ selectedWarrior.warrior_id }}</span
          >
        </div>
      </div>
      <WarriorArmoryPartContainer
        :warrior-armory="selectedWarrior.armory"
        :preview="false"
        @remove-armor="handleRemoveArmor"
      />
      <div>
        <h3 class="mb-1 text-left text-lg font-bold">{{ $t('Stats') }}</h3>
        <div class="grid grid-cols-[60px_40px]">
          <span class="text-left">{{ $t('Attack') }}</span
          ><span class="font-semibold">{{
            selectedWarrior.armory.attack
          }}</span>
          <span class="text-left">{{ $t('Defence') }}</span
          ><span class="font-semibold">{{
            selectedWarrior.armory.defence
          }}</span>
        </div>
      </div>
    </div>
    <div class="border-primary-700 border-l"></div>
    <div class="w-60">
      <form
        class="flex flex-col items-center gap-4"
        @submit.prevent="wearArmor()"
      >
        <h3 class="text-lg font-bold">{{ $t('Add Equipment') }}</h3>

        <div>
          <label class="mb-2 block text-sm font-medium">{{
            $t('Selected item')
          }}</label>
          <BaseSelectedItem
            v-model:amount="ammunitionAmount"
            v-model:item="inventoryStore.selectedItem"
          />
        </div>

        <div v-if="showWarriorHandOption">
          <p class="mb-2 text-sm font-medium">
            {{ $t('Select hand') }}
          </p>
          <BaseRadio
            id="warrior-hand-right"
            v-model="warriorHand"
            class="justify-start"
            name="warrior-hand"
            value="right_hand"
          >
            {{ $t('Right hand') }}
          </BaseRadio>
          <BaseRadio
            id="warrior-hand-left"
            v-model="warriorHand"
            class="justify-start"
            name="warrior-hand"
            value="left_hand"
          >
            {{ $t('Left hand') }}
          </BaseRadio>
        </div>

        <div v-if="showAmountOption">
          <label
            for="ammunition-amount"
            class="mb-2 block text-sm font-medium"
            >{{ $t('Select Amount') }}</label
          >
          <input
            id="ammunition-amount"
            name="ammunition-amount"
            type="number"
            min="1"
            class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-800"
          />
        </div>

        <span v-if="hasSelectedItemError" class="block text-sm text-red-600">{{
          $t('Please select an item')
        }}</span>

        <div class="flex gap-2">
          <UButton
            color="primary"
            :disabled="inventoryStore.selectedItem === null"
            :loading="isLoading"
            type="submit"
          >
            {{ $t('Wear') }}
          </UButton>
          <UButton color="gray" @click.prevent="$emit('backToOverview')">
            {{ $t('Cancel') }}
          </UButton>
        </div>
      </form>
    </div>
  </UCard>
</template>

<script setup lang="ts">
import type {
  ItemParts,
  MinimalWarriorWithArmory,
} from '@/types/WarriorArmory';
import { onUnmounted, ref, watch } from 'vue';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { CustomFetchApi } from '@/CustomFetchApi';
import BaseSelectedItem from '../base/BaseSelectedItem.vue';
import BaseRadio from '../base/BaseRadio.vue';
import WarriorArmoryPartContainer from './WarriorArmoryPartContainer.vue';
import { AssetPaths } from '@/clientScripts/ImagePath';

interface Props {
  selectedWarrior: MinimalWarriorWithArmory;
}
const { selectedWarrior } = defineProps<Props>();

const emit = defineEmits<{
  backToOverview: [];
  updateWarriorArmory: [warrior: MinimalWarriorWithArmory];
}>();

watch(
  () => selectedWarrior,
  () => {
    inventoryStore.resetSelectedItem();
    hasSelectedItemError.value = false;
  },
);

const hasSelectedItemError = ref(false);

const showAmountOption = ref(false);
const isLoading = ref(false);
const warriorHand = ref<string>('right_hand');
const ammunitionAmount = ref<number>(1);

const inventoryStore = useInventoryStore();
inventoryStore.setInventoryItemEvent('selectItem');
inventoryStore.$onAction(({ name, after }) => {
  if (name === 'setSelectedItem') {
    after(() => {
      toggleItemOptions();
    });
  }
});
onUnmounted(() => {
  inventoryStore.setInventoryItemEvent(null);
});

const wearArmor = async () => {
  hasSelectedItemError.value = false;
  if (inventoryStore.selectedItem === null) {
    hasSelectedItemError.value = true;
    return;
  }

  let hand;
  if (showWarriorHandOption.value) {
    hand = warriorHand.value;
  } else {
    hand = null;
  }

  try {
    const response = await CustomFetchApi.post<
      MinimalWarriorWithArmory,
      WearArmorRequest
    >('/armory/soldier/add', {
      item: inventoryStore.selectedItem,
      warrior_id: selectedWarrior.warrior_id,
      hand,
      amount: ammunitionAmount.value,
    });

    emit('updateWarriorArmory', response.data);

    inventoryStore.resetSelectedItem();
    void inventoryStore.setShouldUpdateInventory(true);
    isLoading.value = false;
  } catch {
    isLoading.value = false;
  }
};

const showWarriorHandOption = ref(false);
const toggleItemOptions = () => {
  if (inventoryStore.selectedItem === null) {
    showWarriorHandOption.value = false;
    return;
  }

  if (
    inventoryStore.selectedItem.includes('sword') ||
    inventoryStore.selectedItem.includes('dagger')
  ) {
    showWarriorHandOption.value = true;
    showAmountOption.value = false;
  } else if (
    inventoryStore.selectedItem.includes('arrow') ||
    inventoryStore.selectedItem.includes('knives')
  ) {
    showWarriorHandOption.value = false;
    showAmountOption.value = true;
  } else {
    showWarriorHandOption.value = false;
    showAmountOption.value = false;
  }
};

const handleRemoveArmor = async (part: ItemParts) => {
  try {
    isLoading.value = true;

    const response = await CustomFetchApi.post<
      MinimalWarriorWithArmory,
      RemoveArmorRequest
    >('/armory/soldier/remove', {
      warrior_id: selectedWarrior.warrior_id,
      is_removing: true,
      part,
    });

    emit('updateWarriorArmory', response.data);

    void inventoryStore.setShouldUpdateInventory(true);
    isLoading.value = false;
  } catch {
    isLoading.value = false;
  }
};

interface WearArmorRequest {
  item: string;
  warrior_id: number;
  hand: string | null;
  amount: number;
}

interface RemoveArmorRequest {
  warrior_id: number;
  is_removing: boolean;
  part: ItemParts;
}
</script>
