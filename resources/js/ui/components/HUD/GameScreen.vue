<template>
  <div
    id="canvas-border"
    class="border-primary-700 pixelated-corners absolute top-[-6px] z-7 h-[400px] w-[700px] border-8 after:pointer-events-none after:absolute after:top-[-1px] after:left-[-1px] after:h-[calc(100%+2px)] after:w-[calc(100%+2px)] after:border-4 after:border-solid after:border-gray-950/60 after:content-['']"
  ></div>
  <ScreenLayer id="game_canvas" />
  <ScreenLayer id="game_canvas2" />
  <ScreenLayer id="game_canvas3" />
  <ScreenLayer id="game_canvas4" />
  <ScreenLayer id="text_canvas" />
  <ScreenLayer id="hud_canvas" />
</template>

<script setup lang="ts" vapor>
import ScreenLayer from './ScreenLayer.vue';
</script>

<style scoped></style>
