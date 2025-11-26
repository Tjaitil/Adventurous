<template>
  <div id="client_help_content">
    <div id="help_content_introduction" class="mb-1">
      {{
        $t(
          'Here is some short information to help you out. More extensive info can be found on /gameguide.',
        )
      }}
    </div>
    <div class="help_content_section">
      <h4 class="help_content_section">{{ $t('Hunger') }}</h4>
      <p class="help_content_section_p">
        {{
          $t(
            `Every time you grow crops, start mining, start a new assignment or a new army mission the hunger status will worsen. When it hits 
          below a certain point you will be prevented in doing any mentioned actions.`,
          )
        }}
        {{
          $t(`To restore your hunger status you need to eat food.
          Visit tavern found in every city to eat.`)
        }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts"></script>
