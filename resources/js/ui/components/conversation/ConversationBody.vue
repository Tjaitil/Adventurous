<template>
    <div class="flex grow-1 justify-center overflow-hidden">
        <div class="w-16">
            <img
                id="conversation-a"
                :src="personASource"
                class="block h-16 w-16"
                :class="{ hidden: !showPersonAContainer }"
                alt="conversation character a"
            />
        </div>
        <div id="conversation-text-wrapper" class="w-80 overflow-y-scroll py-2">
            <h2 v-if="isLoading" id="loading_message">Loading...</h2>
            <ul v-else class="mb-1 list-none">
                <template
                    v-if="
                        currentConversationSegment != null &&
                        currentConversationSegment?.options.length > 1
                    "
                >
                    <li
                        v-for="option in currentConversationSegment.options"
                        :key="option.id"
                        class="conv-link cursor-pointer"
                        @click="handleUserEvent(option.id)"
                    >
                        {{ option.text }}
                    </li>
                </template>
                <template v-else>
                    <li class="conv-link cursor-auto">
                        {{ currentConversationSegment?.options[0].text }}
                    </li>
                </template>
            </ul>
        </div>
        <div class="w-16">
            <img
                id="conversation-b"
                :src="personBSource"
                class="block h-16 w-16"
                :class="{ hidden: !showPersonBContainer }"
                alt="conversation character b"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { AssetPaths } from '@/clientScripts/ImagePath';
import type {
    ConversationOption,
    ConversationSegment,
} from '@/types/Conversation';
import { computed, ref, watch } from 'vue';

interface Props {
    currentConversationSegment: ConversationSegment | null;
    selectedConversationOption: ConversationOption | null;
    isLoading: boolean;
}

const { currentConversationSegment, selectedConversationOption } =
    defineProps<Props>();

const personASource = computed(() => {
    if (selectedConversationOption === null) {
        return;
    } else if (!showPersonAContainer.value) {
        return;
    }

    if (selectedConversationOption.person) {
        return AssetPaths.getImagePath(
            selectedConversationOption.person + '.png',
        );
    }

    return '';
});
const personBSource = computed(() => {
    return AssetPaths.getImagePath('character image.png');
});

const showPersonAContainer = ref(false);
const showPersonBContainer = ref(false);

watch(
    () => selectedConversationOption,
    newValue => {
        if (newValue == null) {
            showPersonAContainer.value = false;
            showPersonBContainer.value = false;
            return;
        } else if (newValue.person === null || newValue.person.length === 0) {
            showPersonAContainer.value = false;
            showPersonBContainer.value = false;
            return;
        }

        if (newValue.container === 'A' || newValue.person !== 'player') {
            showPersonAContainer.value = true;
            showPersonBContainer.value = false;
        } else {
            showPersonBContainer.value = true;
            showPersonAContainer.value = false;
        }
    },
);

const emit = defineEmits<{
    optionClick: [number];
}>();

const handleUserEvent = (optionId: number) => {
    emit('optionClick', optionId);
};
</script>
