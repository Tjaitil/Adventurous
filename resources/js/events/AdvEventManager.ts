import { createGameEventManager } from '@/lib/GameEventManager';
import { XpGainedEvent } from './XpGainedEvent';

type AdvEvents = {
    XpGainedEvent: () => void;
};

export const AdvEventManager = createGameEventManager<AdvEvents>({
    XpGainedEvent: () => XpGainedEvent.handle(),
});
