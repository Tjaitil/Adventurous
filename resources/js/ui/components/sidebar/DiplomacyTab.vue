<template>
  <div>
    <div class="mt-1">
      <img src="/images/diplomacy icon.png" alt="Diplomacy icon" />
    </div>

    <table class="lightTextColor middle-align">
      <thead>
        <tr>
          <td>{{ $t('Location') }}</td>
          <td>{{ $t('Diplomacy') }}</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Hirtam</td>
          <td :class="diplomacyClass(data.hirtam)">{{ data.hirtam }}</td>
        </tr>
        <tr>
          <td>Pvitul</td>
          <td :class="diplomacyClass(data.pvitul)">{{ data.pvitul }}</td>
        </tr>
        <tr>
          <td>Khanz</td>
          <td :class="diplomacyClass(data.khanz)">{{ data.khanz }}</td>
        </tr>
        <tr>
          <td>Ter</td>
          <td :class="diplomacyClass(data.ter)">{{ data.ter }}</td>
        </tr>
        <tr>
          <td>Fansal Plains</td>
          <td :class="diplomacyClass(data.fansalplains)">
            {{ data.fansalplains }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import type { DiplomacyResource } from '@/types/Diplomacy';

interface Props {
  data: DiplomacyResource;
}

const { data } = defineProps<Props>();

const diplomacyClass = (diplomacy: number) => {
  if (typeof diplomacy !== 'number') return '';
  if (diplomacy > 1) return 'positiveDiplomacy';
  if (diplomacy < 1) return 'negativeDiplomacy';
  return '';
};
</script>
