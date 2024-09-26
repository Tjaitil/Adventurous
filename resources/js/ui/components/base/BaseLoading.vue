<template>
    <div v-if="isLoading" class="flex justify-center items-center h-full">
        <slot name="loading-icon">
            <img
                src="/images/loading.png"
                alt="loading icon"
                class="loading-icon h-12 mx-auto"
            />
        </slot>
    </div>
    <div v-else>
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
interface Props {
    isLoading: boolean;
}
defineProps<Props>();
</script>
