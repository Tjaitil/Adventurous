<template>
  <div class="w-full">
    <img
      class="cont_exit absolute h-4 w-4"
      src="images/exit.png"
      alt="exit symbol"
      @click="emit('exit-conversation')"
    />
    <h3
      id="conversation-header"
      class="border-primary-900 min-h-10 border-b-2 py-1 text-xl"
    >
      {{ headerText }}
    </h3>
  </div>
</template>

<script setup lang="ts">
import type {
  ConversationOption,
  ConversationSegment,
} from '@/types/Conversation';
import { jsUcWords } from '@/utilities/uppercase';
import { computed } from 'vue';

interface Props {
  currentConversationSegment: ConversationSegment | null;
  selectedConversationOption: ConversationOption | null;
}

const { currentConversationSegment, selectedConversationOption } =
  defineProps<Props>();

const headerText = computed(() => {
  if (selectedConversationOption == null) {
    return currentConversationSegment?.header ?? 'Select one';
  }

  if (
    (selectedConversationOption.container === 'A' ||
      selectedConversationOption.person !== 'player') &&
    selectedConversationOption.person != null
  ) {
    return jsUcWords(selectedConversationOption.person);
  } else {
    return 'You';
  }
});

const emit = defineEmits<{
  (e: 'exit-conversation'): void;
}>();
</script>
