import { advAPIResponse } from './AdvResponse';

export interface BuyPermitsResponse extends advAPIResponse {
    data: {
        new_permits: number;
    };
}
