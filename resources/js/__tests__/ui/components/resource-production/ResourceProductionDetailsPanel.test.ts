import { mount } from '@vue/test-utils';
import { describe, expect, test } from 'vitest';
import { createPinia } from 'pinia';
import { i18n } from '@/ui/main';
import { useSkillsStore } from '@/ui/stores/SkillsStore';
import ResourceProductionDetailsPanel from '@/ui/components/resource-production/ResourceProductionDetailsPanel.vue';

const baseProps = {
  actionTypeLabel: 'Crops',
  skill: 'farmer' as const,
  selectedType: 'wheat',
  levelRequired: 5,
  time: 100,
  experience: 20,
  location: 'towhar',
  efficiencyLevel: 10,
};

describe('ResourceProductionDetailsPanel component', () => {
  test('renders nothing when no type is selected', () => {
    const pinia = createPinia();
    const wrapper = mount(ResourceProductionDetailsPanel, {
      global: { plugins: [pinia, i18n] },
      props: { ...baseProps, selectedType: null },
    });

    expect(wrapper.find('p').exists()).toBe(false);
  });

  test('renders the selected type details and computed reduction', () => {
    const pinia = createPinia();
    const wrapper = mount(ResourceProductionDetailsPanel, {
      global: { plugins: [pinia, i18n] },
      props: baseProps,
    });

    expect(wrapper.text()).toContain('Wheat');
    expect(wrapper.text()).toContain('- 10.00');
    expect(wrapper.text()).toContain('- 0.50');
    expect(wrapper.text()).toContain('Towhar');
    expect(wrapper.text()).toContain('20');
  });

  test('flags not-able-color when the player lacks the required level', () => {
    const pinia = createPinia();
    const wrapper = mount(ResourceProductionDetailsPanel, {
      global: { plugins: [pinia, i18n] },
      props: baseProps,
    });

    const skillsStore = useSkillsStore();
    skillsStore.setUserLevelsResource({
      ...skillsStore.UserLevelsResource,
      farmer_level: 1,
    });

    expect(wrapper.find('.not-able-color').exists()).toBe(true);
  });

  test('renders the cost slot content', () => {
    const pinia = createPinia();
    const wrapper = mount(ResourceProductionDetailsPanel, {
      global: { plugins: [pinia, i18n] },
      props: baseProps,
      slots: { cost: '<p class="cost-slot">Seeds: 3</p>' },
    });

    expect(wrapper.find('.cost-slot').text()).toBe('Seeds: 3');
  });
});
