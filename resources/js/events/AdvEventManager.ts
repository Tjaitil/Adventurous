import { createGameEventManager } from '@/lib/GameEventManager';
import { XpGainedEvent } from './XpGainedEvent';

type AdvEvents = {
    XpGainedEvent: () => void;
};

export type AdvEventsType = keyof AdvEvents;

export const AdvEventManager = createGameEventManager<AdvEvents>({
    XpGainedEvent: () => XpGainedEvent.handle(),
});
