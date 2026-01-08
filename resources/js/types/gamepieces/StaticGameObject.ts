import type { GameObject } from './GameObject';

export interface StaticGameObject extends GameObject {
  type: string;
  sprite: HTMLImageElement;
  src: string;
  drawX: number;
  drawY: number;
  visible: boolean;
  id;
}
