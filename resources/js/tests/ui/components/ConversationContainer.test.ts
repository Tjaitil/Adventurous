import { render, screen, waitFor } from '@testing-library/vue';
import { describe, test, expect, vi, beforeEach, Mock } from 'vitest';
import '@testing-library/jest-dom';
import ConversationContainer from '@/ui/components/ConversationContainer.vue';
import { createTestingPinia } from '@pinia/testing';
import { CustomFetchApi } from '@/CustomFetchApi';
import { useConversationStore } from '@/ui/stores/ConversationStore';
import { GamePieces } from '@/clientScripts/gamePieces';
import { gameTravel } from '@/clientScripts/gameTravel';

vi.mock('@/CustomFetchApi', () => {
    return {
        CustomFetchApi: {
            post: vi.fn(),
        },
    };
});

vi.mock('@/advclient', () => ({
    Game: {
        setGameState: vi.fn(),
    },
}));

vi.mock('@/clientScripts/clientOverlayInterface', () => {
    return {
        ClientOverlayInterface: {
            show: vi.fn(),
            hide: vi.fn(),
        },
    };
});

vi.mock('@/clientScripts/gameTravel', () => {
    return {
        gameTravel: {
            travel: vi.fn(),
        },
    };
});

describe('ConversationContainer.vue', () => {
    beforeEach(() => {
        GamePieces.characters = [
            {
                height: 32,
                id: false,
                type: 'character',
                visible: true,
                width: 32,
                x: 1728.5,
                y: 2145,
                sprite: new Image(),
                drawX: 0,
                drawY: 0,
                diameterDown: 2177,
                diameterLeft: 1728.5,
                diameterRight: 1760.5,
                diameterUp: 0,
                displayName: 'kapys',
                noCollision: false,
                src: 'kapys.png',
                conversation: true,
            },
        ];
    });
    test('renders the component correctly', () => {
        const { container } = render(ConversationContainer, {
            global: {
                plugins: [createTestingPinia()],
            },
        });

        expect(
            container
                .querySelector('#conversation-container')
                ?.classList.contains('invisible'),
        ).toBe(true);
    });

    test('calls loadConversation when store.isActive is true', async () => {
        const { container } = render(ConversationContainer, {
            global: {
                plugins: [createTestingPinia({ stubActions: false })],
            },
        });

        const conversationStore = useConversationStore();
        conversationStore.triggerLoadConversation('kapys');

        (CustomFetchApi.post as Mock).mockResolvedValue({
            data: {
                conversation_segment: {
                    index: 'kps',
                    options: [
                        {
                            id: 1,
                            text: 'Option 1',
                            person: 'Test Character',
                            container: 'A',
                        },
                    ],
                },
            },
        });

        await waitFor(() => {
            expect(
                container
                    .querySelector('#conversation-container')
                    ?.classList.contains('invisible'),
            ).toBe(false);

            expect(screen.getByText('Option 1')).toBeInTheDocument();
        });
    });

    test('calls conversation with multiple options hides button', async () => {
        const { container } = render(ConversationContainer, {
            global: {
                plugins: [createTestingPinia({ stubActions: false })],
            },
        });

        const conversationStore = useConversationStore();
        conversationStore.triggerLoadConversation('kapys');

        (CustomFetchApi.post as Mock).mockResolvedValue({
            data: {
                conversation_segment: {
                    index: 'kps',
                    options: [
                        {
                            id: 1,
                            text: 'Option 1',
                            person: 'Test Character',
                            container: 'A',
                        },
                        {
                            id: 2,
                            text: 'Option 2',
                            person: 'Test Character',
                            container: 'A',
                        },
                    ],
                },
            },
        });

        await waitFor(() => {
            expect(
                container
                    .querySelector('#conversation-container')
                    ?.classList.contains('invisible'),
            ).toBe(false);

            expect(screen.getByText('Option 1')).toBeInTheDocument();
            expect(screen.getByText('Option 2')).toBeInTheDocument();
            expect(screen.queryByText('Click here to continue')).toBeNull();
        });
    });

    test('select conversation option calls travel function', async () => {
        const { container } = render(ConversationContainer, {
            global: {
                plugins: [createTestingPinia({ stubActions: false })],
            },
        });
        const conversationStore = useConversationStore();
        conversationStore.triggerLoadConversation('kapys');
        (CustomFetchApi.post as Mock).mockResolvedValue({
            data: {
                conversation_segment: {
                    index: 'prrr',
                    header: 'Where would you like to travel to?',
                    options: [
                        {
                            person: '',
                            text: 'Golbak',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'golbak',
                            },
                            id: 0,
                        },
                        {
                            person: '',
                            text: 'Khanz',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'khanz',
                            },
                            id: 1,
                        },
                        {
                            person: '',
                            text: 'Krasnur',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'krasnur',
                            },
                            id: 2,
                        },
                        {
                            person: '',
                            text: 'Tasnobil',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'tasnobil',
                            },
                            id: 3,
                        },
                        {
                            person: '',
                            text: 'Fagna',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'fagna',
                            },
                            id: 4,
                        },
                        {
                            person: '',
                            text: 'Snerpiir',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'snerpiir',
                            },
                            id: 5,
                        },
                        {
                            person: '',
                            text: 'Cruendo',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'cruendo',
                            },
                            id: 6,
                        },
                        {
                            person: '',
                            text: 'Ter',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'ter',
                            },
                            id: 7,
                        },
                        {
                            person: '',
                            text: 'Towhar',
                            next_key: 'end',
                            client_callback: 'GameTravelCallback',
                            option_values: {
                                location: 'towhar',
                            },
                            id: 8,
                        },
                    ],
                },
            },
        });
        await waitFor(() => {
            expect(
                container
                    .querySelector('#conversation-container')
                    ?.classList.contains('invisible'),
            ).toBe(false);
        });

        const option = screen.getByText('Khanz');
        await option.click();
        await waitFor(() => {
            expect(gameTravel.travel).toHaveBeenCalled();
            expect(
                container
                    .querySelector('#conversation-container')
                    ?.classList.contains('invisible'),
            ).toBe(true);
        });
    });

    // test('ends conversation correctly', async () => {
    //     wrapper.vm.endConversation();

    //     expect(wrapper.vm.currentConversationSegment).toBeNull();
    //     expect(wrapper.vm.showConversationContainer).toBe(false);
    //     expect(Game.setGameState).toHaveBeenCalledWith('playing');
    // });

    // test('displays loading message when isLoading is true', async () => {
    //     wrapper.vm.isLoading = true;
    //     await wrapper.vm.$nextTick();

    //     expect(wrapper.find('#loading_message').text()).toBe('Loading...');
    // });

    // test('renders conversation options correctly', async () => {
    //     wrapper.vm.currentConversationSegment = {
    //         options: [
    //             {
    //                 id: 1,
    //                 text: 'Option 1',
    //                 person: 'Test Character',
    //                 container: 'A',
    //             },
    //             {
    //                 id: 2,
    //                 text: 'Option 2',
    //                 person: 'Test Character',
    //                 container: 'B',
    //             },
    //         ],
    //     };
    //     wrapper.vm.isLoading = false;
    //     await wrapper.vm.$nextTick();

    //     const options = wrapper.findAll('.conv-link');
    //     expect(options.length).toBe(2);
    //     expect(options[0].text()).toBe('Option 1');
    //     expect(options[1].text()).toBe('Option 2');
    // });

    // test('toggles visibility of elements based on state', async () => {
    //     wrapper.vm.showPersonAContainer = true;
    //     await wrapper.vm.$nextTick();

    //     expect(wrapper.find('#conversation-a').isVisible()).toBe(true);

    //     wrapper.vm.showPersonAContainer = false;
    //     await wrapper.vm.$nextTick();

    //     expect(wrapper.find('#conversation-a').isVisible()).toBe(false);
    // });

    // test('emits correct event when button is clicked', async () => {
    //     wrapper.vm.showButton = true;
    //     await wrapper.vm.$nextTick();

    //     const button = wrapper.find('#conv_button');
    //     await button.trigger('click');

    //     expect(CustomFetchApi.post).toHaveBeenCalled();
    // });
});
