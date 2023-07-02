import { AdvApi } from "../../AdvApi";

export interface GetHealDataResponse extends AdvApi {
    data: {
        heal: number;
    }
}

export interface RestoreHealthResponse extends AdvApi {
    data: {
        new_hunger: number;
    }
}