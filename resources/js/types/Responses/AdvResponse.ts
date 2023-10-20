import { LevelUpSkill } from './../LevelUpSkill';
export interface advAPIResponse {
    gameMessage?: string[];
    errorGameMessage?: string[];
    data?: {};
    levelUp?: LevelUpSkill[],
    html?: {
        [key: string]: string;
    }
}