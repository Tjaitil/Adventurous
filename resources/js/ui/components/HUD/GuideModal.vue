<template>
  <UModal
    v-model:open="isOpen"
    scrollable
    :overlay="false"
    :ui="{
      content: 'max-w-4xl bg-orange-50',
    }"
    @close="close"
  >
    <template #title>
      <VisuallyHidden>{{ modalTitle }}</VisuallyHidden>
    </template>
    <template #description>
      <VisuallyHidden>{{ modalDescription }}</VisuallyHidden>
    </template>
    <template #body>
      <section class="min-w-2xl">
        <GuideContainer
          v-if="selectedGuide !== null"
          :guide="selectedGuide"
          :in-modal="true"
          @back-to-guides="selectedGuide = null"
        />
        <div v-else-if="categories.length > 0">
          <GuidesContainer
            :in-modal="true"
            :categories="categories"
            :category-guides="categoryGuides"
            @select-guide="selectGuide"
          />
        </div>
        <Link href="/guides" target="_blank" class="mt-10 block underline">
          {{ t('Read in new tab') }}
        </Link>
      </section>
    </template>
  </UModal>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import {
  GUIDE_IDENTIFIER_MAP,
  type Guide,
  type GuideIdentifier,
} from '@/types/Guide';
import GuidesContainer from '../guide/GuidesContainer.vue';
import axios from 'axios';
import GuideContainer from '../guide/GuideContainer.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { gameEventBus } from '@/gameEventsBus';
import { VisuallyHidden } from 'reka-ui';

interface Props {
  open?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  open: false,
});

const emit = defineEmits<{
  'update:open': [value: boolean];
}>();

const { t } = useI18n();
const isOpen = ref(props.open);
const selectedGuide = ref<Guide | null>(null);
const isLoadingGuides = ref(false);
const categories = ref<string[]>([]);
const categoryGuides = ref<Record<string, Guide[]>>({});
const modalTitle = computed(() =>
  selectedGuide.value?.title ? selectedGuide.value.title : t('Guides'),
);
const modalDescription = computed(() =>
  selectedGuide.value?.description
    ? selectedGuide.value.description
    : t('Browse and read guides.'),
);

watch(isOpen, newVal => {
  emit('update:open', newVal);
});

watch(
  () => props.open,
  newVal => {
    isOpen.value = newVal;
  },
);

const fetchGuidesList = async (): Promise<void> => {
  isLoadingGuides.value = true;
  try {
    const response = await axios.get('/guides');

    categories.value = response.data.categories || [];
    categoryGuides.value = response.data.categoryGuides || {};
  } catch {
  } finally {
    isLoadingGuides.value = false;
  }
};
void fetchGuidesList();

const fetchGuide = async (
  category: string,
  slug: string,
): Promise<Guide | null> => {
  try {
    const response = await axios.get<Guide>(`/guides/${category}/${slug}`);

    if (response.status !== 200) {
      throw new Error(`Failed to fetch guide: ${response.statusText}`);
    }

    return response.data;
  } catch {
    return null;
  }
};

const selectGuide = async (category: string, slug: string): Promise<void> => {
  const guide = await fetchGuide(category, slug);
  if (guide) {
    selectedGuide.value = guide;
  }
};

const openModal = async (): Promise<void> => {
  if (categories.value.length === 0) {
    await fetchGuidesList();
  }
  selectedGuide.value = null;
  isOpen.value = true;
};

const openGuideBySlug = async (
  category: string,
  slug: string,
): Promise<void> => {
  if (categories.value.length === 0) {
    await fetchGuidesList();
  }

  const guide = await fetchGuide(category, slug);
  selectedGuide.value = guide;
  isOpen.value = true;
};

const openGuide = async (identifier: GuideIdentifier): Promise<void> => {
  const guideRoute = GUIDE_IDENTIFIER_MAP[identifier];

  if (identifier === 'overview') {
    await openModal();
    return;
  }

  await openGuideBySlug(guideRoute.category, guideRoute.slug);
};

const close = (): void => {
  isOpen.value = false;
  selectedGuide.value = null;
};

defineExpose({
  openGuide,
  close,
});

onMounted(() => {
  gameEventBus.subscribe('GUIDE_OPEN', payload => {
    void openGuide(payload.guide);
  });
});
</script>
