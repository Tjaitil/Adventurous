<template>
  <UCard
    variant="soft"
    class="ucard-effect cursor-pointer"
    :ui="{
      body: 'flex flex-col gap-6',
    }"
    @click="$emit('select', warrior.warrior_id)"
  >
    <div class="flex items-center gap-2">
      <img
        class="type_icon max-h-12 max-w-12 basis-2/4"
        :src="AssetPaths.getImagePngPath(warrior.type + ' icon')"
        alt="warrior-icon"
      />
      <div>
        <span class="text-left">
          {{ $t('Soldier') }} #{{ warrior.warrior_id }}</span
        >
      </div>
    </div>
    <WarriorArmoryPartContainer
      :warrior-armory="warrior.armory"
      :preview="true"
    />
    <div class="grid grid-cols-[60px_40px]">
      <span class="text-left">{{ $t('Attack') }}</span
      ><span class="font-semibold">{{ warrior.armory.attack }}</span>
      <span class="text-left">{{ $t('Defence') }}</span
      ><span class="font-semibold">{{ warrior.armory.defence }}</span>
    </div>
  </UCard>
</template>

<script setup lang="ts">
import type { MinimalWarriorWithArmory } from '@/types/WarriorArmory';
import { AssetPaths } from '@/clientScripts/ImagePath';
import WarriorArmoryPartContainer from './WarriorArmoryPartContainer.vue';

interface Props {
  warrior: MinimalWarriorWithArmory;
}

defineProps<Props>();

defineEmits<{
  select: [warrior_id: number];
}>();
</script>
