<template>
  <footer id="app-footer">Developed by Kjetil Baksaas</footer>
</template>

<script setup lang="ts"></script>
<style>
#app-footer {
  color: white;
  background: radial-gradient(
    var(--layoutBgColor),
    var(--layoutBgColorShadowPerc),
    var(--layoutBgColorShadow)
  );
  padding: 5px;
  margin-bottom: 5px;
  text-align: center;
}
</style>
