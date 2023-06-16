export interface StartMiningRequest {
    mineral: string;
    workforce_amount: number;
}

export interface UpdateMiningRequest {
    is_cancelling: boolean;
}

export interface BuyPermitsRequest {
    location: string;
}