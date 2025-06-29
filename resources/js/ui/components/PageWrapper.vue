<template>
  <component :is="page" v-bind="stateProps" />
</template>

<script setup lang="ts">
/** Wrapper for in-blade vue page components */
import { reactive } from 'vue';

interface Props {
  page: string;
  initProps?: Record<string, unknown>;
}

const { initProps } = defineProps<Props>();
const stateProps = reactive(initProps || {});
</script>
