<template>
  <UCard
    class="relative mx-auto box-border min-h-[250px] min-w-[600px] text-white"
    variant="soft"
    :ui="{
      body: 'flex flex-row',
    }"
  >
    <div
      id="store-container-item-list"
      class="pb-05 min-[336px] flex max-h-[600px] basis-1/2 flex-col overflow-y-scroll"
    >
      <div
        v-for="storeItem in storeItems"
        :key="storeItem.name"
        class="store-container-item border-primary-400 relative flex cursor-pointer flex-row gap-3 border-b-2 p-3 last:border-0"
        @click="selectItem(storeItem)"
      >
        <BaseItem
          :item="storeItem.name"
          :amount="storeItem.amount"
          :show-amount="false"
          disable-tooltip
        />
        <div class="flex grow flex-row items-center justify-center">
          <span>
            <span
              :class="[
                'store-container-item-price',
                storeItem.adjusted_difference !== 0 ? 'block line-through' : '',
              ]"
            >
              {{ storeItem.store_value }}
            </span>
            <span v-if="storeItem.adjusted_difference > 0" class="able-color">
              {{ storeItem.adjusted_store_value }}
            </span>
            <span
              v-else-if="storeItem.adjusted_difference < 0"
              class="not-able-color"
            >
              {{ storeItem.adjusted_store_value }}
            </span>
          </span>
          <img src="/images/gold.png" :alt="$t('gold icon')" class="gold" />
        </div>
        <span v-if="storeItem.amount > -1" class="flex items-center"
          >x {{ storeItem.amount }}</span
        >
      </div>
    </div>

    <div class="border-primary-400 basis-1/2 border-l-2 px-6">
      <div
        v-if="selectedItem !== null"
        class="flex min-w-[155px] flex-col justify-between gap-4 py-6"
      >
        <div>
          <div class="mb-2 flex">
            <BaseItem
              :item="selectedItem.name"
              :amount="selectedItem.amount"
              :show-amount="false"
              disable-tooltip
            />
            <span
              v-if="selectedItem.item_multiplier > 1"
              class="item_amount"
              style="visibility: visible"
            >
              {{ selectedItem.item_multiplier }}
            </span>
          </div>
          <p class="flex flex-row">
            <span>{{ effectivePrice }}</span>
            <img src="/images/gold.png" :alt="$t('gold icon')" class="gold" />
          </p>
        </div>

        <div
          v-if="showRequirements"
          class="bg-primary-950/60 pixelated-corners-sm flex flex-col gap-4 p-3"
        >
          <div v-if="selectedItem.required_items.length > 0">
            <p
              class="mb-2 text-left text-xs font-semibold tracking-wider uppercase"
            >
              {{ $t('Materials') }}
            </p>
            <div class="flex flex-wrap gap-3">
              <div
                v-for="req in selectedItem.required_items"
                :key="req.name"
                class="store-container-item-requirement flex flex-col items-center gap-1"
              >
                <BaseItem
                  :item="req.name"
                  :amount="req.amount * selectedAmount"
                  :show-amount="true"
                  disable-tooltip
                />
                <span v-if="!itemRequirementMet(req)">
                  {{ $t('Requirement not met') }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="selectedItem.skill_requirements.length > 0">
            <p
              class="mb-2 text-left text-xs font-semibold tracking-wider uppercase"
            >
              {{ $t('Skills') }}
            </p>
            <div class="flex flex-col gap-2">
              <div
                v-for="req in selectedItem.skill_requirements"
                :key="req.skill"
                class="relative flex flex-col gap-2"
              >
                <div class="flex flex-row space-x-2">
                  <BaseIcon :icon="req.skill" />
                  <span class="text-left text-sm capitalize">{{
                    req.level
                  }}</span>
                </div>
                <BaseIcon
                  v-if="!skillRequirementMet(req)"
                  class="absolute bottom-0 left-0 h-5 w-5"
                  :icon="'lock'"
                />
              </div>
            </div>
          </div>

          <p
            v-if="
              selectedItem.required_items.length === 0 &&
              selectedItem.skill_requirements.length === 0
            "
            class="text-xs opacity-60"
          >
            {{ $t('No requirements') }}
          </p>
        </div>

        <p
          v-if="showItemInformation && selectedItem.information"
          class="text-sm"
        >
          {{ selectedItem.information }}
        </p>

        <div v-if="showAmountInput" class="w-full">
          <label class="block text-sm" for="store-amount-input">{{
            $t('Select your Amount')
          }}</label>
          <UInput
            id="store-amount-input"
            v-model="selectedAmount"
            type="number"
            min="1"
            :ui="{ base: 'w-full' }"
          />
        </div>

        <UButton :disabled="!allRequirementsMet" @click="handleTrade">
          {{ buttonText }}
        </UButton>
      </div>

      <div v-else class="block text-center">
        <p>{{ $t('Select an item in the list') }}</p>
      </div>
    </div>
  </UCard>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import type { StoreItemResource } from '@/types/StoreItemResource';
import type { SkillRequirementResource } from '@/types/SkillRequirementResource';
import type { UserLevels } from '@/types/UserLevels';
import BaseItem from '@/ui/components/base/BaseItem.vue';
import BaseIcon from '../base/BaseIcon.vue';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { useSkillsStore } from '@/ui/stores/SkillsStore';

interface Props {
  storeItems: StoreItemResource[];
  buttonText: string;
  showItemRequirements?: boolean;
  showItemInformation?: boolean;
  showAmountInput?: boolean;
  showRequirements?: boolean;
}

const {
  storeItems,
  buttonText,
  showItemInformation = false,
  showAmountInput = true,
  showRequirements = true,
} = defineProps<Props>();

const emit = defineEmits<{
  trade: [{ item: StoreItemResource; amount: number }];
}>();

const inventoryStore = useInventoryStore();
const skillsStore = useSkillsStore();

const selectedItem = ref<StoreItemResource | null>(null);
const selectedAmount = ref(1);

const effectivePrice = computed(() => {
  if (!selectedItem.value) return 0;
  return selectedItem.value.adjusted_store_value
    ? selectedItem.value.adjusted_store_value
    : selectedItem.value.store_value;
});

const playerItemAmount = (itemName: string): number =>
  inventoryStore.inventoryItems.find(i => i.item === itemName)?.amount ?? 0;

const playerSkillLevel = (skill: string): number => {
  const levels = skillsStore.UserLevelsResource;
  if (skill === 'adventurer') return levels.adventurer_respect;
  const key = `${skill}_level` as keyof UserLevels;
  return (levels[key] as number | undefined) ?? 0;
};

const itemRequirementMet = (req: StoreItemResource): boolean =>
  playerItemAmount(req.name) >= req.amount * selectedAmount.value;

const skillRequirementMet = (req: SkillRequirementResource): boolean =>
  playerSkillLevel(req.skill) >= req.level;

const allRequirementsMet = computed(() => {
  if (!showRequirements || !selectedItem.value) return true;
  return (
    selectedItem.value.required_items.every(itemRequirementMet) &&
    selectedItem.value.skill_requirements.every(skillRequirementMet)
  );
});

// ── Actions ──────────────────────────────────────────────────────────────────

const selectItem = (item: StoreItemResource) => {
  selectedItem.value = item;
  selectedAmount.value = 1;
};

const handleTrade = () => {
  if (!selectedItem.value) return;
  emit('trade', { item: selectedItem.value, amount: selectedAmount.value });
};
</script>
