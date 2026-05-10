<template>
  <div class="relative flex flex-col gap-4">
    <div class="mb-2.5 flex items-center justify-between">
      <h1 class="page_title">
        <slot name="title" />
      </h1>
      <div class="mt-4 self-start">
        <slot name="header-actions" />
      </div>
    </div>
    <slot />
  </div>
</template>
<style>
.page_title {
  background: radial-gradient(#f1e6da 33%, #d6b38f);
  border-top-left-radius: 25px 50%;
  border-bottom-left-radius: 25px 50%;
  border-top-right-radius: 25px 50%;
  border-bottom-right-radius: 25px 50%;
  width: 55%;
  height: 70px;
  margin: auto;
  color: black;
}
</style>
