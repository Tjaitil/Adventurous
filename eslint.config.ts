import {
  defineConfigWithVueTs,
  vueTsConfigs,
} from '@vue/eslint-config-typescript';
import prettierRecommended from 'eslint-plugin-prettier/recommended';
import pluginVue from 'eslint-plugin-vue';
import pluginVitest from '@vitest/eslint-plugin';
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting';

export default defineConfigWithVueTs(
  {
    name: 'app/files-to-lint',
    files: ['**/*.{ts,mts,tsx,vue}'],
  },
  pluginVue.configs['flat/recommended'],
  vueTsConfigs.strictTypeChecked,
  skipFormatting,
  prettierRecommended,
  {
    rules: {
      'prettier/prettier': ['off'],
      /**
       * vueTsConfigs.strictTypeChecked sets allowNumber to false, but it is very strict
       */
      '@typescript-eslint/restrict-template-expressions': [
        'error',
        {
          allowNumber: true,
        },
      ],
      '@typescript-eslint/consistent-type-imports': 'error',
    },
  },
  {
    files: ['**/*.vue'],
    rules: {
      '@typescript-eslint/no-useless-default-assignment': 'off',
    },
  },
  {
    files: ['**/__tests__/**/*.{ts,tsx}', '**/*.test.{ts,tsx}'],
    plugins: {
      vitest: pluginVitest,
    },
    rules: {
      /**
       * Referencing a mocked method (e.g. `expect(axios.post).toHaveBeenCalled()`)
       * always trips the base rule, even though it's never called unbound.
       * vitest/unbound-method knows to allow that case while still catching
       * genuine unbound-method usage elsewhere in test files.
       */
      '@typescript-eslint/unbound-method': 'off',
      'vitest/unbound-method': 'error',
    },
  },
);
