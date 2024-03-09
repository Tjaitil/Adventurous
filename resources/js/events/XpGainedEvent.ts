import { GameEvent } from '@/types/GameEvents';
import { useSkillsStore } from '@/ui/stores/SkillsStore';

export const XpGainedEvent: GameEvent = {
    name: 'XpGainedEvent',
    handle() {
        useSkillsStore().setHandleXpGainedEvent(true);
    },
};
