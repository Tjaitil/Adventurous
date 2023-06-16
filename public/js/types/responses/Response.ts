export interface Response {
    html?: string[],
    data?: {
        [key: string]: any,
    },
    gameMessage?: string[],
    erroGgameMessage?: string[],
    levelUp?: {
        skill: "farmer" | "miner" | "trader" | "warrior",
        new_level: number;
    }
}
