/** Properties from json maps */
export interface GameObject {
  id: number;
  src: string;
  diameterUp: number;
  diameterLeft: number;
  diameterRight: number;
  diameterDown: number;
  x: number;
  y: number;
  width: number;
  height: number;
  noCollision: boolean;
  type: GameObjectType;
  visible: boolean;
}

export type GameObjectType =
  | 'building'
  | 'character'
  | 'object'
  | 'start_point'
  | 'teleport'
  | ''
  | 'conversation';

/**
 * Custom properties for our game engine
 */
export type AdvClientMapProperties = {
  sprite: HTMLImageElement;
  src: string;
  drawX: number;
  drawY: number;
};

export type AdvGameObject = GameObject & AdvClientMapProperties;

export type StaticGameObject = GameObject &
  AdvClientMapProperties & {
    type: 'start_point' | 'teleport' | '' | 'conversation';
  };

export type IBuildingGameObject = GameObject &
  AdvClientMapProperties & {
    type: 'building';
    id: number;
    displayName: string;
  };

export type ICharacterGameObject = GameObject &
  AdvClientMapProperties & {
    type: 'character';
    displayName: string;
    hasConversation: boolean;
  };
