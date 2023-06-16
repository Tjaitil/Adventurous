import { advAPIResponse } from './AdvResponse';

export interface MineCountdownResponse extends advAPIResponse {
    data: {
        mining_countdown: number;
        mining_type: string;
    }
}

export interface StartMiningResponse extends advAPIResponse {
    data: {
        availWorkforce: number;
        new_permits: number;
        new_hunger: number;
    }
}

export interface FinishMiningResponse extends advAPIResponse {
    data: {
        avail_workforce: number;
    }
}

export interface BuyPermitsResponse extends advAPIResponse {
    data: {
        new_permits: number;
    }
}