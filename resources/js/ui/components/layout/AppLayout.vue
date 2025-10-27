<template>
  <UApp>
    <AppHeader />
    <section class="col-span-6 col-start-1 row-start-2">
      <slot></slot>
    </section>
    <footer class="col-span-6 col-start-1 row-start-3">
      Developed by Kjetil Baksaas
    </footer>
  </UApp>
</template>

<script setup lang="ts">
import AppHeader from './AppHeader.vue';
</script>
