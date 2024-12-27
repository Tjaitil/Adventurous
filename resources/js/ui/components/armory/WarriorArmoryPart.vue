<template>
    <img
        class="armory_view_part pixelated-corners-sm hover:brightness-95 border-gray-950/60 border-2 w-12 h-12"
        :title="part ?? ''"
        :src="AssetPaths.getImagePngPath(part ?? 'none')"
    />
</template>

<script lang="ts" setup>
import { AssetPaths } from '@/clientScripts/ImagePath';
interface Props {
    part: string | null;
}
defineProps<Props>();
</script>
