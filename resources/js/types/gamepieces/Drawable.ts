/** Seconds elapsed since the last frame, already divided by viewport zoom. */
export type Delta = number;

export interface Drawable {
  diameterDown: number;
  draw(delta: Delta): void;
}
