<template>
  <label class="flex flex-col gap-1 text-sm font-medium">
    {{ $t('Select workers') }} <span class="font-normal">({{ max }})</span>
    <UInput v-model.number="amount" type="number" :max="max" min="0" />
  </label>
</template>

<script setup lang="ts">
interface Props {
  max: number;
}

defineProps<Props>();

const amount = defineModel<number>({ default: 0 });
</script>
