type Handler<Args extends unknown[]> = (...args: Args) => void;

export class EventEmitter<Events extends { [K in keyof Events]: unknown[] }> {
  private listeners: { [K in keyof Events]?: Handler<Events[K]> } = {};

  on<K extends keyof Events>(event: K, handler: Handler<Events[K]>) {
    const previous = this.listeners[event];
    this.listeners[event] = (...args) => {
      previous?.(...args);
      handler(...args);
    };
  }

  emit<K extends keyof Events>(event: K, ...args: Events[K]) {
    this.listeners[event]?.(...args);
  }
}
