export interface advAPIResponse<T extends object> {
    gameMessage?: string[];
    errorGameMessage?: string[];
    data: T;
    levelUp?: {
        skill: "farmer" | "miner" | "trader" | "warrior",
        new_level: number;
    },
    html?: string[];
}import { LevelUpSkill } from './../LevelUpSkill';
export interface advAPIResponse {
    gameMessage?: string[];
    errorGameMessage?: string[];
    data?: {};
    levelUp?: LevelUpSkill[],
    html?: {
        [key: string]: string;
    }
}