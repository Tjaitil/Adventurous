export interface Response {
    html?: string[];
    data?: {
        [key: string]: any;
    };
    game_message?: string[];
    error_game_message?: string[];
    level_up?: {
        skill: 'farmer' | 'miner' | 'trader' | 'warrior';
        new_level: number;
    };
}
