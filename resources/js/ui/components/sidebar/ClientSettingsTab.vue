<template>
  <div class="space-y-2">
    <USwitch
      v-model="settingsForm.minimalControls"
      :ui="{
        label: 'text-white font-bold',
        description: 'text-white',
      }"
      :label="t('Minimal Controls')"
      :description="t('This will remove P and C section')"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const settingsForm = reactive({
  minimalControls: false,
});

const LOCAL_STORAGE_KEY = 'advclient_client_settings';
const syncFromLocalStorage = () => {
  const storedSettings = localStorage.getItem(LOCAL_STORAGE_KEY);
  if (storedSettings) {
    const parsedSettings = JSON.parse(storedSettings);
    Object.keys(parsedSettings).forEach(key => {
      if (key in settingsForm) {
        settingsForm[key] = parsedSettings[key];
      }
    });
  }
};

watch(
  () => settingsForm,
  newValue => {
    localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(newValue));
  },
);

onMounted(() => {
  syncFromLocalStorage();
});
</script>
