<template>
    <div
        id="conversation-container"
        class="border-primary-700 absolute right-0 left-0 z-20 flex h-[190px] flex-col items-center rounded-sm border-4 bg-orange-50 p-1 shadow-lg transition-[scale] duration-300 ease-in after:pointer-events-none after:absolute after:top-[-1px] after:left-[-1px] after:h-[calc(100%+2px)] after:w-[calc(100%+2px)] after:rounded-sm after:border-4 after:border-solid after:border-gray-950 after:content-['']"
        :class="{ invisible: !showConversationContainer }"
    >
        <ConversationHeader
            :current-conversation-segment
            :selected-conversation-option
            @exit-conversation="store.isActive = false"
        />
        <ConversationBody
            :current-conversation-segment
            :selected-conversation-option
            :is-loading
            @option-click="handleUserEvent"
        >
        </ConversationBody>
        <button
            v-if="showButton"
            id="conv_button"
            class="self-center"
            @click="handleUserEvent(null)"
        >
            Click here to continue
        </button>
    </div>
</template>

<script setup lang="ts">
import { ClientOverlayInterface } from '@/clientScripts/clientOverlayInterface';
import { GamePieces } from '@/clientScripts/gamePieces';
import { gameTravel } from '@/clientScripts/gameTravel';
import { CustomFetchApi } from '@/CustomFetchApi';
import { GameLogger } from '@/utilities/GameLogger';
import { ref, watch } from 'vue';
import { useConversationStore } from '../stores/ConversationStore';
import { Game } from '@/advclient';
import { AdvEventManager } from '@/events/AdvEventManager';
import { loadBuildingCallback } from '@/conversationCallbacks/loadBuilding';
import ConversationHeader from './conversation/ConversationHeader.vue';
import type {
    ConversationOption,
    ConversationRequest,
    ConversationSegment,
    conversationSegmentResponse,
} from '@/types/Conversation';
import ConversationBody from './conversation/ConversationBody.vue';
import { pauseManager } from '@/clientScripts/pause';

const showButton = ref(false);

const selectedConversationOption = ref<ConversationOption | null>(null);
const currentConversationSegment = ref<ConversationSegment | null>(null);

const showConversationContainer = ref(false);
const conversationContainerScale = ref(0.8);

const persons = ref<string[]>([]);

const isLoading = ref(true);

const store = useConversationStore();

const loadConversation = async () => {
    Game.setGameState('conversation');

    const characterObject = GamePieces.characters.find(object => {
        return object.displayName === store.currentPerson;
    });

    if (characterObject == undefined) {
        GameLogger.addMessage('An error occured', true);
        return;
    }
    const person = (persons.value[0] = characterObject.displayName);

    persons.value = [];
    ClientOverlayInterface.hide();

    conversationContainerScale.value = 1;
    showConversationContainer.value = true;

    const data = {
        person: person,
        is_starting: true,
    };
    await getNextSegment(data).then(() => {
        isLoading.value = false;
    });
};

const handleUserEvent = (optionId: number | null = null) => {
    if (
        currentConversationSegment.value == null ||
        store.currentPerson == null
    ) {
        return;
    }
    const selectedId =
        optionId ?? currentConversationSegment.value.options[0].id;

    const newOption = currentConversationSegment.value.options.find(
        segment => segment.id === selectedId,
    );

    if (newOption == undefined) {
        GameLogger.addMessage('An error occured', true);
        return;
    }
    selectedConversationOption.value = newOption;
    handleCallbacks();
    if (selectedConversationOption.value.next_key == 'end') {
        endConversation();
        return;
    }
    const data = {
        person: store.currentPerson,
        is_starting: false,
        selected_option: selectedConversationOption.value.id,
    };

    void getNextSegment(data);
};

const handleCallbacks = () => {
    if (store.currentPerson == null) {
        return;
    }

    if (selectedConversationOption.value == null) {
        return;
    }

    switch (selectedConversationOption.value.client_callback) {
        case 'GameTravelCallback':
            if (selectedConversationOption.value.option_values == null) {
                return;
            }

            if (
                !(
                    'location' in selectedConversationOption.value.option_values
                ) &&
                typeof selectedConversationOption.value.option_values[
                    'location'
                ] !== 'string'
            ) {
                GameLogger.addMessage('An error occured', true);
                return;
            }
            gameTravel.travel(
                selectedConversationOption.value.option_values['location'],
                store.currentPerson,
            );
            break;
        case 'LoadZinsStoreCallback':
            loadBuildingCallback('zinsstore');
            break;
    }
};

const getNextSegment = async (data: ConversationRequest): Promise<void> => {
    await CustomFetchApi.post<conversationSegmentResponse>(
        'conversation/next',
        data,
    )
        .then(response => {
            currentConversationSegment.value =
                response.data.conversation_segment;
            selectedConversationOption.value = null;
            handleNextLine();
        })
        .catch(() => {
            GameLogger.addMessage('An error occured', true);
        });
};

const handleNextLine = () => {
    if (currentConversationSegment.value === null) {
        return;
    }

    if (currentConversationSegment.value.options.length === 1) {
        setButtonVisibility(true);
        selectedConversationOption.value =
            currentConversationSegment.value.options[0];
    } else {
        setButtonVisibility(false);
    }
    handleClientEvents();
};

const setButtonVisibility = (value: boolean) => {
    showButton.value = value;
};
const handleClientEvents = () => {
    if (currentConversationSegment.value == null) {
        return;
    }
    if (
        currentConversationSegment.value.client_events != null &&
        currentConversationSegment.value.client_events.length > 0
    ) {
        AdvEventManager.notify('InventoryChangedEvent');
    }
};

const endConversation = () => {
    currentConversationSegment.value = null;
    conversationContainerScale.value = 0.5;
    store.isActive = false;
    showConversationContainer.value = false;

    pauseManager.resumeGame();

    setButtonVisibility(false);
};

watch(
    () => store.isActive,
    () => {
        if (store.isActive) {
            void loadConversation();
        } else {
            endConversation();
        }
    },
);
</script>
<style>
#conversation-container {
    scale: v-bind(conversationContainerScale);
}
</style>
