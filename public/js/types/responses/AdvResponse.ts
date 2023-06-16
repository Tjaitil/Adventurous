export interface advAPIResponse<T extends object> {
    gameMessage?: string[];
    errorGameMessage?: string[];
    data: T;
    levelUp?: {
        skill: "farmer" | "miner" | "trader" | "warrior",
        new_level: number;
    },
    html?: string[];
}