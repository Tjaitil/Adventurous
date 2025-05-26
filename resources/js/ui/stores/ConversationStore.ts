import { defineStore } from 'pinia';
import { ref } from 'vue';
export const useConversationStore = defineStore('conversation', () => {
  const isActive = ref(false);
  const currentPerson = ref<string | null>(null);

  const triggerLoadConversation = (person: string) => {
    isActive.value = true;
    currentPerson.value = person;
  };
  const triggerEndConversation = () => {
    isActive.value = false;
  };
  return {
    isActive,
    currentPerson,
    triggerLoadConversation,
    triggerEndConversation,
  };
});
