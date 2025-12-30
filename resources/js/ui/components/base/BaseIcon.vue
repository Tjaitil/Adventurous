<template>
  <img :src="getComputedSrc" :alt="getComputedalt()" :title="imageTitle" />
</template>

<script setup lang="ts">
import { computed, useAttrs } from 'vue';

interface Props {
  icon: string;
}

const { icon } = defineProps<Props>();

const getComputedalt = () => {
  return `${icon} icon`;
};

const attrs = useAttrs();

const imageTitle = computed(() => {
  if (typeof attrs.title === 'string') {
    return attrs.title;
  }

  return `${icon} icon`;
});

const getComputedSrc = computed(() => {
  return `/images/${icon} icon.png`;
});
</script>
