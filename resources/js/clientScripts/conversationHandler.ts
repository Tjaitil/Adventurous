import { Game } from '@/advclient';
import { ClientOverlayInterface } from '@/clientScripts/clientOverlayInterface';
import { gameEventBus } from '@/gameEventsBus';

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
      Game.setGameState('conversation');
      ClientOverlayInterface.hide();
    }),
  );
};
