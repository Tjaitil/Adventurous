
export interface CropCountdownResponse {
    harvest: number,
    date: number,
}

export interface GrowCropsResponse {
    newHunger: number,
    availWorkforce: number,
}

export interface DestroyCropsResponse {
    availWorkforce: number,
}

export interface HarvestCropsResponse {
    availWorkforce: number,
}