import { createGameEventManager } from '@/lib/GameEventManager';
import { XpGainedEvent } from './XpGainedEvent';
import { useInventoryStore } from '@/ui/stores/InventoryStore';

type AdvEvents = {
    XpGainedEvent: () => void;
    InventoryChangedEvent: () => void;
};

export type AdvEventsType = keyof AdvEvents;

export const AdvEventManager = createGameEventManager<AdvEvents>({
    XpGainedEvent: () => XpGainedEvent.handle(),
    InventoryChangedEvent: () =>
        useInventoryStore().setShouldUpdateInventory(true),
});
