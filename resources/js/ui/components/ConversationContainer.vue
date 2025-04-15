<template>
    <div
        id="conversation-container"
        class="border-primary-700 absolute right-0 left-0 z-20 flex h-[190px] flex-col items-center rounded-sm border-4 bg-orange-50 p-1 shadow-lg transition-[scale] duration-300 ease-in after:pointer-events-none after:absolute after:top-[-1px] after:left-[-1px] after:h-[calc(100%+2px)] after:w-[calc(100%+2px)] after:rounded-sm after:border-4 after:border-solid after:border-gray-950 after:content-['']"
        :class="{ invisible: !showConversationContainer }"
    >
        <div>
            <img
                class="cont_exit absolute"
                src="images/exit.png"
                alt="exit symbol"
                @click="store.isActive = false"
            />
            <h3
                id="conversation-header"
                class="border-primary-900 border-b-2 py-1 text-xl"
            >
                {{ headerText }}
            </h3>
        </div>
        <div class="flex grow-1 justify-center overflow-hidden">
            <img
                id="conversation-a"
                :src="personASource"
                class="block h-16 w-16"
                :class="{ hidden: !showPersonAContainer }"
                alt="conversation character a"
            />
            <div
                id="conversation-text-wrapper"
                class="w-80 overflow-y-scroll py-2"
            >
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
            <img
                id="conversation-b"
                :src="personBSource"
                class="block h-16 w-16"
                :class="{ hidden: !showPersonBContainer }"
                alt="conversation character b"
            />
        </div>
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
import { AssetPaths } from '@/clientScripts/ImagePath';
import { CustomFetchApi } from '@/CustomFetchApi';
import { GameLogger } from '@/utilities/GameLogger';
import { jsUcWords } from '@/utilities/uppercase';
import { ref, watch } from 'vue';
import { useConversationStore } from '../stores/ConversationStore';
import { Game } from '@/advclient';
import { AdvEventManager } from '@/events/AdvEventManager';

const showPersonAContainer = ref(false);
const personASource = ref('');
const showPersonBContainer = ref(false);
const personBSource = ref('');
const showButton = ref(false);

const selectedConversationOption = ref<ConversationOption | null>(null);
const currentConversationSegment = ref<ConversationSegment | null>(null);

const showConversationContainer = ref(false);
const conversationContainerScale = ref(0.5);

const headerText = ref('');

const endEvents = ref<CallableFunction[]>([]);
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
    } else if (selectedConversationOption.value.option_values == null) {
        return;
    }

    switch (selectedConversationOption.value.client_callback) {
        case 'GameTravelCallback':
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

const togglePerson = () => {
    if (selectedConversationOption.value?.person == null) {
        showPersonAContainer.value = false;
        showPersonBContainer.value = false;
        return;
    }

    if (
        selectedConversationOption.value.container === 'A' ||
        selectedConversationOption.value.person !== 'player'
    ) {
        showPersonAContainer.value = true;
        personASource.value = AssetPaths.getImagePath(
            selectedConversationOption.value.person + '.png',
        );
        headerText.value = jsUcWords(selectedConversationOption.value.person);
        showPersonBContainer.value = false;
    } else {
        personBSource.value = AssetPaths.getImagePath('character image.png');
        showPersonBContainer.value = true;
        headerText.value = 'You';
        showPersonAContainer.value = false;
    }
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
    handleToggleButton();
    handleClientEvents();
    togglePerson();
};

const setButtonVisibility = (value: boolean) => {
    showButton.value = value;
};
const handleToggleButton = () => {
    if (!showButton.value) {
        headerText.value = '';
    } else {
        headerText.value =
            currentConversationSegment.value?.header ?? 'Select an answer';
    }
};

const handleClientEvents = () => {
    if (currentConversationSegment.value == null) {
        return;
    }
    if (
        currentConversationSegment.value.client_events != null &&
        currentConversationSegment.value.client_events.length > 0
    ) {
        currentConversationSegment.value.client_events.forEach(clientEvent => {
            switch (clientEvent) {
                case 'InventoryChangedEvent':
                    AdvEventManager.notify('InventoryChangedEvent');
                    break;
            }
        });
    }
};

const endConversation = () => {
    for (const i of endEvents.value) {
        i();
    }

    currentConversationSegment.value = null;
    conversationContainerScale.value = 0.5;
    window.setTimeout(() => {
        showConversationContainer.value = false;
    }, 300);

    Game.setGameState('playing');

    setButtonVisibility(false);
    showPersonAContainer.value = false;
    showPersonBContainer.value = false;

    endEvents.value = [];
};

watch(
    () => store.isActive,
    () => {
        if (store.isActive) {
            loadConversation();
        } else {
            endConversation();
        }
    },
);

interface conversationSegmentResponse {
    conversation_segment: ConversationSegment;
}

interface ConversationSegment {
    header?: string;
    index: string;
    options: ConversationOption[];
    client_events?: ConversationClientEvent[];
}
interface ConversationOption {
    person: string | null;
    text: string;
    next_key: 'Q' | 'q' | 'r' | 'S' | 'end';
    container: 'A' | 'B';
    client_callback?: ConversationCallback;
    option_values?: object;
    id: number;
}
interface ConversationRequest {
    person: string;
    is_starting: boolean;
    selected_option?: number;
}

type ConversationCallback = 'GameTravelCallback' | 'LoadZinsStoreCallback';

type ConversationClientEvent = 'InventoryChangedEvent';
</script>
<style>
#conversation-container {
    scale: v-bind(conversationContainerScale);
}
</style>
