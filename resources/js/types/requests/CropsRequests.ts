export interface StartGrowingRequest {
    crop_type: string;
    workforce_amount: number;
}

export interface UpdateCropsRequest {
    is_cancelling: boolean;
}

export interface SeedGeneratorRequest {
    item: string;
    amount: number;
}
