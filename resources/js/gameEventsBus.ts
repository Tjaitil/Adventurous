import type { GameLoggerEvents } from '@/utilities/GameLogger';
import type { AdvClientEvents } from '@/advclient';
import type { InputHandlerEvents } from './clientScripts/inputHandler';
import { reportError } from '@/ui/errorReporting';
export type GameEventMap = {
  PLAYER_HEALTH_UPDATE: { health: number };
  PLAYER_HUNTED_UPDATE: { isHunted: boolean };
} & InputHandlerEvents &
  GameLoggerEvents &
  AdvClientEvents;

type GameEventType = keyof GameEventMap;

type GameEventListener<K extends GameEventType> = (
  payload: GameEventMap[K],
) => void;

class GameEventBus {
  private listeners: {
    [K in GameEventType]: Array<GameEventListener<K>>;
  } = {
    RENDER_BUILDING: [],
    HUD_BUILDING_PROMPT_UPDATE: [],
    HUD_CONVERSATION_PROMPT_UPDATE: [],
    PLAYER_HEALTH_UPDATE: [],
    PLAYER_HUNTED_UPDATE: [],
    GAMELOGGER_MESSAGE_LOGGED: [],
    CHANGED_LOCATION: [],
  };

  subscribe<K extends GameEventType>(event: K, listener: GameEventListener<K>) {
    this.listeners[event].push(listener);
    return () => {
      this.unsubscribe(event, listener);
    };
  }

  unsubscribe<K extends GameEventType>(
    event: K,
    listener: GameEventListener<K>,
  ) {
    const listeners = this.listeners[event];
    const index = listeners.indexOf(listener);
    if (index !== -1) listeners.splice(index, 1);
  }

  emit<K extends GameEventType>(event: K, payload: GameEventMap[K]) {
    this.listeners[event].forEach(listener => {
      try {
        listener(payload);
      } catch (error) {
        reportError(error);
      }
    });
  }
}

export const gameEventBus = new GameEventBus();

gameEventBus.subscribe('GAMELOGGER_MESSAGE_LOGGED', () => {});
