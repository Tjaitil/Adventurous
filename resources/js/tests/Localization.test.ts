import { getLanguageBundle } from '@/ui/localeSetup';
import { mount } from '@vue/test-utils';
import { createPinia } from 'pinia';
import { expect, test } from 'vitest';
import { createI18n, useI18n } from 'vue-i18n';

const LocalizationComponent = {
    template: `<div id='test'>{{ $t('passwords.sent') }}</div>`,
    setup() {
        const { t } = useI18n();
        return {
            t,
        };
    },
};

test('Test Localization works', async () => {
    const pinia = createPinia();
    const i18n = createI18n({
        locale: 'en',
        messages: {
            en: await getLanguageBundle(),
        },
        legacy: false,
    });

    const wrapper = mount(LocalizationComponent, {
        global: {
            plugins: [pinia, i18n],
        },
    });

    expect(wrapper.find('#test').text()).toBe(
        'We have emailed your password reset link.',
    );
});
