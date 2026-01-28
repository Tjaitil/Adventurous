<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Armory') }}</h1>
    <template v-if="selectedWarrior === null">
      <p class="mb-4">
        {{ $t('Click on a soldier to change their equipment') }}
      </p>
      <div
        id="warrior_container"
        class="auto-fit-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] justify-center gap-6"
      >
        <template v-if="!isLoading">
          <WarriorArmoryCard
            v-for="warrior in warriors"
            :key="warrior.warrior_id"
            :warrior="warrior"
            @select="handleSelectWarrior"
          />
        </template>
        <BaseLoadingIcon v-else class="h-6 w-6 justify-self-center" />
      </div>
    </template>

    <template v-else>
      <UButton
        icon="i-heroicons-arrow-left-20-solid"
        color="gray"
        variant="ghost"
        @click="handleBackToOverview"
      >
        {{ $t('Back to Overview') }}
      </UButton>
      <CurrentWarriorForm
        v-if="selectedWarriorData != null"
        :selected-warrior="selectedWarriorData"
        @back-to-overview="handleBackToOverview"
        @update-warrior-armory="updateWarriorArmory"
      />
    </template>
  </div>
</template>

<script setup lang="ts">
import type { MinimalWarriorWithArmory } from '@/types/WarriorArmory';
import { computed, ref } from 'vue';
import WarriorArmoryCard from '../components/armory/WarriorArmoryCard.vue';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { ArmoryDataLoader } from '@/buildingScripts/armory';
import CurrentWarriorForm from '../components/armory/SelectedWarriorForm.vue';
import BaseLoadingIcon from '../components/base/BaseLoadingIcon.vue';

const isLoading = ref(false);
const warriors = ref<MinimalWarriorWithArmory[]>([]);

const selectedWarrior = ref<number | null>(null);

const selectedWarriorData = computed(() =>
  warriors.value.find(w => w.warrior_id === selectedWarrior.value),
);

const fetchWarriors = async () => {
  const cachedData = buildingDataPreloader.getArmoryData();
  if (cachedData) {
    warriors.value = cachedData.warriors;
    return;
  }

  try {
    isLoading.value = true;
    warriors.value = await ArmoryDataLoader.warriors();
    isLoading.value = false;
  } catch {
    isLoading.value = false;
  }
};
void fetchWarriors();
const handleSelectWarrior = (id: number) => {
  selectedWarrior.value = id;
};
const handleBackToOverview = () => {
  selectedWarrior.value = null;
};

const updateWarriorArmory = (warrior: MinimalWarriorWithArmory) => {
  const index = warriors.value.findIndex(
    w => w.warrior_id === warrior.warrior_id,
  );

  if (index === -1) {
    return;
  }

  warriors.value[index] = warrior;
};
</script>
