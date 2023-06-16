import { advAPIResponse } from './AdvResponse';

export interface UpgradeEfficiencyResponse extends advAPIResponse {
    data: {
        efficiency_level: number,
        new_efficiency_price: number
    }
}