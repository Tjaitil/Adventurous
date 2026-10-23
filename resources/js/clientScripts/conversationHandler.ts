import { Game } from '@/advclient';
import { ClientOverlayInterface } from '@/clientScripts/clientOverlayInterface';
import { gameTravel } from '@/clientScripts/gameTravel';
import { pauseManager } from '@/clientScripts/pause';
import { loadBuildingCallback } from '@/conversationCallbacks/loadBuilding';
import { gameEventBus } from '@/gameEventsBus';
import { GameLogger } from '@/utilities/GameLogger';

type ConversationCallbackHandler = (
  values: Record<string, unknown> | null,
  person: string,
) => void;

const callbackHandlers: Record<string, ConversationCallbackHandler> = {
  GameTravelCallback: (values, person) => {
    const location = values?.['location'];
    if (typeof location !== 'string') {
      GameLogger.addMessage('An error occured', true);
      return;
    }
    gameTravel.travel(location, person);
  },
  LoadZinsStoreCallback: () => {
    loadBuildingCallback('zinsstore');
  },
};

let unsubscribers: Array<() => void> = [];

export const unregisterConversationHandlers = () => {
  unsubscribers.forEach(unsubscribe => {
    unsubscribe();
  });
  unsubscribers = [];
};

export const registerConversationHandlers = () => {
  unregisterConversationHandlers();

  unsubscribers.push(
    gameEventBus.subscribe('CONVERSATION_STARTED', () => {
      Game.properties.inConversation = true;
      Game.setGameState('conversation');
      ClientOverlayInterface.hide();
    }),
    gameEventBus.subscribe('UI_CONVERSATION_ENDED', () => {
      if (!Game.properties.inConversation) {
        return;
      }
      Game.properties.inConversation = false;
      pauseManager.resumeGame();
    }),
    gameEventBus.subscribe(
      'UI_CONVERSATION_CALLBACK',
      ({ callback, values, person }) => {
        const handler = Object.hasOwn(callbackHandlers, callback)
          ? callbackHandlers[callback]
          : undefined;
        if (handler === undefined) {
          Game.reportError(
            new Error(`Unknown conversation callback: ${callback}`),
          );
          return;
        }
        handler(values, person);
      },
    ),
  );
};
