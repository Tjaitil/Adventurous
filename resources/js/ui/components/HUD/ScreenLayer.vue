<template>
  <canvas
    :id
    width="700"
    height="400"
    class="image-crisp absolute left-0"
  ></canvas>
</template>

<script setup lang="ts" vapor>
interface Props {
  id: string;
}

defineProps<Props>();
</script>
