import type { BuildingName, VuePage } from '@/types/Building';
import type { GameLog } from '@/types/GameLog';

export interface GameEvent {
  name: string;
  handle: () => void;
}

export type AdvClientEvents = {
  CHANGED_LOCATION: { locationName: string };
};

export interface GameLoggerEvents {
  GAMELOGGER_MESSAGE_LOGGED: { message: GameLog };
}

export interface InputHandlerEvents {
  RENDER_BUILDING:
    | {
        content: string;
        building: Exclude<BuildingName, VuePage>;
      }
    | { building: VuePage }
    | { loading: true };
  HUD_BUILDING_PROMPT_UPDATE: {
    buildingName: string | null;
  };
  HUD_CONVERSATION_PROMPT_UPDATE: {
    characterName: string | null;
  };
}

export type GameQueryMap = {
  PLAYER_POSITION: {
    params: undefined;
    result: { x: number; y: number; currentMap: string };
  };
};
