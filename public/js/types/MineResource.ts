export interface MineralResource {
    mineral_type: string;
    mineral_ore: string;
    miner_level: number;
    experience: number;
    time: number;
    min_per_period: number;
    max_per_period: number;
    permit_cost: number;
    location: string;
}