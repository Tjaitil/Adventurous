<template>
  <img
    class="armory_view_part pixelated-corners-sm h-12 w-12 border-2 border-gray-950/60 hover:brightness-95"
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
