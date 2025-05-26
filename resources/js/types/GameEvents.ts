export interface GameEvent {
  name: string;
  handle: () => void;
}
