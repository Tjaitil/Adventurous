<template>
  <div
    class="bg-primary-800 h-full overflow-x-hidden px-2 pt-2 text-white transition-all duration-200"
    :class="expanded ? 'w-md' : 'w-full'"
  >
    <button
      id="sidebar_button_toggle"
      class="pixelated-corners-sm float-right rounded-md border-2 bg-orange-50 p-2 text-xs font-bold text-black shadow outline-hidden"
      :class="expanded ? '' : 'invisible'"
      @click="hide"
    >
      {{ expanded ? '<<' : '>>' }}
    </button>
    <div class="space-y-4">
      <p>{{ jsUcfirst(username) }}</p>
      <p class="mt-1 mb-1">{{ jsUcfirst(profiency) }}</p>
      <p>{{ jsUcfirst(location) }}</p>
      <UTabs
        v-model="activeTab"
        :items="items"
        :unmount-on-hide="false"
        orientation="vertical"
        :ui="{
          list: 'pixelated-corners-sm',
          root: 'items-start',
          indicator: activeTab === '' ? 'invisible' : '',
        }"
      >
        <template #log>
          <GameLogTab :init-messages />
        </template>
        <template #countdown>
          <ProfiencyStatusTab :profiency-statuses />
        </template>
        <template #adventure>
          <div>Adventure</div>
        </template>
        <template #diplomacy>
          <DiplomacyTab :data="diplomacyResource" />
        </template>
        <template #skills><SkillInfoList /></template>
        <template #help><HelpTab /></template>
        <template #settings><ClientSettingsTab /></template>
      </UTabs>
    </div>
  </div>
</template>

<script setup lang="ts">
import { jsUcfirst } from '@/utilities/uppercase';
import { TabsItem } from '@nuxt/ui';
import { ref, watch } from 'vue';
import DiplomacyTab from '../sidebar/DiplomacyTab.vue';
import { useI18n } from 'vue-i18n';
import GameLogTab from '../sidebar/GameLogTab.vue';
import ProfiencyStatusTab from '../sidebar/ProfiencyStatusTab.vue';
import SkillInfoList from '../skills/SkillInfoList.vue';
import HelpTab from '../sidebar/HelpTab.vue';
import ClientSettingsTab from '../sidebar/ClientSettingsTab.vue';
import type { UserLevels } from '@/types/UserLevels';
import type { GameLog } from '@/types/GameLog';
import type { DiplomacyResource } from '@/types/Diplomacy';
import type { ProficiencyStatuses } from '@/types/ProficiencyStatuses';
import { useSkillsStore } from '@/ui/stores/SkillsStore';

interface Props {
  username: string;
  profiency: string;
  location: string;
  initMessages: GameLog[];
  initLevels: UserLevels;
  profiencyStatuses: ProficiencyStatuses;
  diplomacyResource: DiplomacyResource;
}

const { initLevels } = defineProps<Props>();

const { t } = useI18n();

const items = [
  {
    label: t('Log'),
    slot: 'log' as const,
  },
  {
    label: t('Countdown'),
    slot: 'countdown' as const,
  },
  {
    label: t('Adventure'),
    slot: 'adventure' as const,
  },
  {
    label: t('Diplomacy'),
    slot: 'diplomacy' as const,
  },
  {
    label: t('Skills'),
    slot: 'skills' as const,
  },
  {
    label: t('Help'),
    slot: 'help' as const,
  },
  {
    label: t('Settings'),
    slot: 'settings' as const,
  },
] satisfies TabsItem[];

const activeTab = ref('');

watch(
  activeTab,
  newVal => {
    console.log(newVal !== '');
    if (newVal !== '') {
      expanded.value = true;
    }
  },
  { immediate: true },
);

const hide = () => {
  activeTab.value = '';
  expanded.value = false;
};

const expanded = ref(false);

const store = useSkillsStore();
store.setUserLevelsResource(initLevels);
</script>
