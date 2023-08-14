import { HUD } from './HUD.js';
import { AdvApi } from '../AdvApi.js';
import { advAPIResponse } from '../types/responses/AdvResponse.js';

export function getHunger() {
    AdvApi.get<GetHungerResponse>('/hunger/get').then((response) => {
        updateHunger(response.data.current_hunger);
    }).catch((error) => false);
}

export function updateHunger(newHunger: number) {
    HUD.elements.hungerProgressBar.setCurrentValue(newHunger);
}

export interface GetHungerResponse extends advAPIResponse {
    data: {
        current_hunger: number;
    }
}