import { advAPIResponse } from "./AdvResponse";

export interface CropCountdownResponse extends advAPIResponse {
    data: {
        crop_countdown: number;
        crop_type: boolean;
        date: number;
    }
}

export interface GrowCropsResponse extends advAPIResponse {
    data: {
        new_hunger: number;
        avail_workforce: number;
    }
}

export interface HarvestCropsResponse extends advAPIResponse {
    data: {
        avail_workforce: number;
    }
}