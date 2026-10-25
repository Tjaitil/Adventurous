import { gameTravel } from '@/clientScripts/gameTravel';
import { loadBuildingCallback } from '@/conversationCallbacks/loadBuilding';
import { reportError } from '@/ui/errorReporting';
import { GameLogger } from '@/utilities/GameLogger';

type ConversationCallbackHandler = (
  values: Record<string, unknown> | null,
  person: string,
) => void;

const conversationCallbackHandlers: Record<
  string,
  ConversationCallbackHandler
> = {
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

export const handleConversationCallback = (
  callback: string,
  values: Record<string, unknown> | null,
  person: string,
) => {
  const handler = Object.hasOwn(conversationCallbackHandlers, callback)
    ? conversationCallbackHandlers[callback]
    : undefined;
  if (handler === undefined) {
    reportError(new Error(`Unknown conversation callback: ${callback}`));
    return;
  }
  handler(values, person);
};
