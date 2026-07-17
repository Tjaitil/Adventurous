<template>
  <div class="border-b-2 border-black py-2">
    <p>{{ infoText }}</p>
    <p v-if="isActionActive && !isFinished">
      {{ remainder.hours }}{{ $t('h') }} {{ remainder.minutes }}{{ $t('m') }}
      {{ remainder.seconds }}{{ $t('s') }}
    </p>
    <UButton
      v-if="isActionActive && !isFinished"
      color="gray"
      @click="$emit('cancel')"
    >
      {{ cancelActionText }}
    </UButton>
    <UButton
      v-else-if="isActionActive && isFinished"
      color="primary"
      @click="$emit('finish')"
    >
      {{ finishActionText }}
    </UButton>
  </div>
</template>

<script setup lang="ts">
interface Remainder {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
}

interface Props {
  infoText: string;
  remainder: Remainder;
  isFinished: boolean;
  isActionActive: boolean;
  cancelActionText: string;
  finishActionText: string;
}

defineProps<Props>();

defineEmits<{
  cancel: [];
  finish: [];
}>();
</script>
