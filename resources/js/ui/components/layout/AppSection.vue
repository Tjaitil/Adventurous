<template>
  <section>
    <slot></slot>
  </section>
</template>

<script setup lang="ts"></script>

<style scoped>
section {
  /*background: radial-gradient(#f2e6d9 50%, #986834);*/
  /*background: radial-gradient(#a9753d 75%, #4b341b);*/
  background: radial-gradient(
    var(--layoutBgColor),
    var(--layoutBgColorShadowPerc),
    var(--layoutBgColorShadow)
  );
  box-sizing: content-box;
  /*width: 82.8%;
    min-height: 700px;*/
  grid-column: 2 / 3;
  border: inset #4b341b 2px;
  /*float: right;*/
  text-align: center;
  color: #392714;
  font-weight: 200;
  position: relative;
}
</style>
