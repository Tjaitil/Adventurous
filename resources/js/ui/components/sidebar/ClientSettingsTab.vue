<template>
  <div id="client_settings_container">
    <label class="label-container">
      Minimal Controls? <br />
      This will remove "P" and "C" section
      <input
        id="client-settings-minimal-control"
        type="checkbox"
        name="client-settings-minimal-control"
      />
      <span class="checkmark"></span>
    </label>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
const set = (settingName: string) => {
  const targetSetting = list.find(setting => setting.name === settingName);
  // Throw error if settingName doesn't exists
  if (!targetSetting) return false;

  switch (targetSetting.type) {
    case 'switch':
      targetSetting.value = !targetSetting.value;
      break;
    default:
      break;
  }
  targetSetting.update();
};
const checkLocalStorage = () => {
  list.forEach(setting => {
    const storedValue = localStorage.getItem(setting.name);
    if (storedValue) setting.setup(storedValue);
    else setting.setup();
  });
};
const list = [
  {
    name: 'minimalControls',
    type: 'switch',
    value: false,
    targetElement: null,
    setup(storedValue: string | null = null) {
      // Check for storedValue
      if (storedValue !== null)
        this.value = storedValue != 'false' ? true : false;
      this.targetElement = document.getElementById(
        'client-settings-minimal-control',
      );
      this.targetElement.checked = this.value;
      this.targetElement.addEventListener('change', () =>
        set('minimalControls'),
      );
      this.update();
    },
    update() {
      const controlPara = [
        ...document.querySelectorAll('.extendedControls'),
      ] as HTMLElement[];
      let style;
      if (this.value) {
        style = 'hidden';
      } else {
        style = 'visible';
      }
      controlPara.forEach(element => (element.style.visibility = style));
      localStorage.setItem('minimalControls', this.value.toString());
    },
  },
];

onMounted(() => {
  checkLocalStorage();
});
</script>
