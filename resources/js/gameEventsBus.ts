import type { GameLoggerEvents } from '@/utilities/GameLogger';
export type GameEventMap = {
  PLAYER_HEALTH_UPDATE: { health: number };
  PLAYER_HUNTED_UPDATE: { isHunted: boolean };
  RENDER_BUILDING: {
    content: string;
  };
} & GameLoggerEvents;

type GameEventType = keyof GameEventMap;

type GameEventListener<K extends GameEventType> = (
  payload: GameEventMap[K],
) => void;

class GameEventBus {
  private listeners: {
    [K in GameEventType]: Array<GameEventListener<K>>;
  } = {
    RENDER_BUILDING: [],
    PLAYER_HEALTH_UPDATE: [],
    PLAYER_HUNTED_UPDATE: [],
    GAMELOGGER_MESSAGE_LOGGED: [],
  };

  subscribe<K extends GameEventType>(event: K, listener: GameEventListener<K>) {
    // TypeScript can't guarantee at runtime, but we trust the typing at call site
    this.listeners[event].push(listener);
  }

  emit<K extends GameEventType>(event: K, payload: GameEventMap[K]) {
    this.listeners[event].forEach(listener => {
      listener(payload);
    });
  }
}

export const gameEventBus = new GameEventBus();

gameEventBus.subscribe('GAMELOGGER_MESSAGE_LOGGED', () => {});
