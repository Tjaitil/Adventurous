import { createI18n } from 'vue-i18n';
import { getLanguageBundle } from './localeSetup';

export const i18n = createI18n({
  locale: 'en',
  messages: { en: await getLanguageBundle() },
  legacy: false,
  missingWarn: false,
  silentFallbackWarn: true,
  silentTranslationWarn: true,
});