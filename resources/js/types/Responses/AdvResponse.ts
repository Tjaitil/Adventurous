import { LevelUpSkill } from '../Skill';
export interface advAPIResponse<T = object> {
    gameMessage?: string[];
    errorGameMessage?: string[];
    data: T;
    levelUp?: LevelUpSkill[];
    html?: {
        [key: string]: string;
    };
    events: string[];
}
