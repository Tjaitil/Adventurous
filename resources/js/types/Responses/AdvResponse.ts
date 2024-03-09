import { LevelUpSkill } from '../Skill';
export interface advAPIResponse {
    gameMessage?: string[];
    errorGameMessage?: string[];
    data?: {};
    levelUp?: LevelUpSkill[];
    html?: {
        [key: string]: string;
    };
    events: string[];
}
