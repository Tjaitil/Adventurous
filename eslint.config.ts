import { globalIgnores } from 'eslint/config';
import {
  defineConfigWithVueTs,
  vueTsConfigs,
} from '@vue/eslint-config-typescript';
import prettierRecommended from 'eslint-plugin-prettier/recommended';
import pluginVue from 'eslint-plugin-vue';
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting';
import pluginOxlint from 'eslint-plugin-oxlint';

export default defineConfigWithVueTs(
  {
    files: ['**/*.{vue,ts,mts,tsx}'],
    name: 'app/files-to-lint',
  },
  globalIgnores([
    '**/node_modules/**',
    '**/vendor/**',
    '**/app/**',
    '**/dist/**',
  ]),

  pluginVue.configs['flat/recommended'],
  vueTsConfigs.strictTypeChecked,
  prettierRecommended,
  {
    rules: {
      '@typescript-eslint/consistent-type-imports': 'error',
      '@typescript-eslint/no-useless-default-assignment': 'off',
      /**
       * VueTsConfigs.strictTypeChecked sets allowNumber to false, but it is very strict
       */
      '@typescript-eslint/restrict-template-expressions': [
        'error',
        {
          allowNumber: true,
        },
      ],
      'prettier/prettier': ['off'],
    },
  },

  ...pluginOxlint.buildFromOxlintConfigFile('.oxlintrc.json'),

  skipFormatting,
);
