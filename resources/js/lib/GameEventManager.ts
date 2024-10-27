export const createGameEventManager = <T extends GameEventsMapping>(
    events: T,
): GameEventManager<T> => {
    return {
        events: events,
        handle(event, data: object = {}) {
            if (this.events[event]) {
                this.events[event](data);
            }
        },
        notify(event) {
            if (Array.isArray(event)) {
                event.forEach(event => {
                    this.handle(event);
                });
            } else {
                this.handle(event);
            }
        },
    };
};

interface GameEventManager<T> {
    events: T;
    handle(event: keyof T, data?: object): void;
    notify(event: keyof T | Array<keyof T>): void;
}

type GameEventsMapping = {
    [key: string]: CallableFunction;
};
