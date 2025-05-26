<template>
  <label
    class="user-select-none relative my-1 flex cursor-pointer flex-row-reverse justify-end gap-2"
    :for="id"
  >
    <span>
      <slot></slot>
    </span>
    <input
      :id
      type="radio"
      :name
      :value
      class="peer absolute h-0 w-0 cursor-pointer opacity-0"
      :checked="isChecked === value"
    />
    <span
      class="relative inline-block h-5 w-5 rounded-full border-2 border-transparent bg-neutral-100 peer-checked:bg-orange-600 peer-checked:drop-shadow-xl after:absolute after:top-0.5 after:left-0.5 after:hidden after:h-3 after:w-3 after:rounded-full after:bg-white after:content-[''] peer-checked:after:block hover:bg-gray-200"
    ></span>
  </label>
</template>

<script setup lang="ts">
interface Props {
  name: string;
  id: string;
  variant?: string;
  value: string | number;
}

const isChecked = defineModel<string | number>();

defineProps<Props>();
</script>

<style scoped></style>
