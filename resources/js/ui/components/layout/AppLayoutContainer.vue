<template>
  <div>
    <slot></slot>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped>
div {
  background-color: #120e07;
  /*font-family: 'Source Sans Pro', 'bold';*/
  font-family: serif;
  font-size: 16px;
  padding: 10px 5px;
  display: grid;
  grid-gap: 10px;
  width: 100%;
  /*grid-templates-areas: 'header header header aside section section section footer footer footer';*/
  grid-template-rows: 200px auto 100px;
  grid-template-columns: 12% 87%;
  box-sizing: border-box;
  overflow-x: hidden;
}
</style>
