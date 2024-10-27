import { AdvEventsType } from '@/events/AdvEventManager';
import { LevelUpSkill } from '../Skill';
import { GameLog } from '../GameLog';
export interface advAPIResponse<T = object> {
    logs: GameLog[];
    data: T;
    levelUp?: LevelUpSkill[];
    html?: {
        [key: string]: string;
    };
    events: AdvEventsType[];
}
