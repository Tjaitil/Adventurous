<!-- eslint-disable vue/no-v-html -->
<template>
  <div v-show="isOpen" id="news"></div>
  <div v-show="isOpen" id="news_content" class="gap-x-2">
    <img
      class="cont_exit absolute top-4 right-4"
      src="/images/exit.png"
      :alt="$t('Close icon')"
      width="20px"
      height="20px"
      @click="internalClose"
    />
    <div class="flex grow">
      <div id="news_content_side_panel" class="hidden w-1/4"></div>
      <div id="news_content_main_content" class="mt-2 mb-2 grow">
        <img
          id="loading_message"
          ref="loadingIcon"
          :alt="$t('Loading icon')"
          src="/images/loading.png"
          class="loading-icon mx-auto mt-5 hidden"
        />
        <template v-if="!externalRendering">
          <component :is="internalComponent" />
        </template>
        <template v-else>
          <div
            v-if="externalContent"
            id="news_content_main_content_inner"
            ref="externalContent"
            class="news_content"
            v-html="externalContent.innerHTML"
          ></div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { gameEventBus } from '@/gameEventsBus';
import {
  computed,
  ref,
  shallowRef,
  useTemplateRef,
  watch,
  nextTick,
} from 'vue';
import ArmoryPage from '../buildings/ArmoryPage.vue';
import { ClientOverlayInterface } from '@/clientScripts/clientOverlayInterface';

const isOpen = ref(false);
const loadingElement = useTemplateRef('loadingIcon');
const externalContent = ref<HTMLElement | null>(null);

// eslint-disable-next-line @typescript-eslint/no-redundant-type-constituents
const internalComponent = shallowRef<typeof ArmoryPage | null>(null);

const currentComponent = computed({
  get() {
    return internalComponent.value;
  },
  set(value) {
    internalComponent.value = value;
  },
});

const externalRendering = ref(false);

watch(externalContent, () => {
  if (externalContent.value && externalRendering.value) {
    void nextTick(() => {
      ClientOverlayInterface.createSidePanelTabs();
      ClientOverlayInterface.adjustWrapperHeight();
    });
  }
});

gameEventBus.subscribe('RENDER_BUILDING', obj => {
  isOpen.value = true;

  if (!('content' in obj) && !('loading' in obj)) {
    // Add logic once we have more buildings as VuePages
    currentComponent.value = ArmoryPage;

    externalRendering.value = false;
    return;
  } else {
    externalRendering.value = true;
    if ('loading' in obj) {
      loadingElement.value?.classList.remove('hidden');
      return;
    }
    loadingElement.value?.classList.add('hidden');

    const content = new DOMParser().parseFromString(obj.content, 'text/html');
    externalContent.value = content.body;
  }
});

const internalClose = () => {
  isOpen.value = false;
  externalContent.value = null;
  internalComponent.value = null;
};
</script>

<style>
#news {
  box-sizing: border-box;
  position: absolute;
  height: 100%;
  width: 101%;
  border: 1px solid black;
  z-index: var(--newsZIndex);
  margin-left: -2%;
  color: #ffffff;
  background-color: black;
  opacity: 0.8;
}

#news_content {
  top: 0px;
  left: 0;
  right: 0;
  position: absolute;
  margin: 0 auto;
  background: radial-gradient(
    var(--layoutBgColor),
    var(--layoutBgColorShadowPerc),
    var(--layoutBgColorShadow)
  );
  z-index: var(--newsZIndex);
  width: 98%;
  padding: 16px;
  transition: top 0.5s ease-out;
  box-shadow: 0px 0px 30px 15px;
  box-sizing: border-box;
}

#news_content table {
  margin: 0px auto;
  margin-bottom: 15px;
}

#news_content_side_panel {
  height: auto;
  box-sizing: border-box;
}
</style>
