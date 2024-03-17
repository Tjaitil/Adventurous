import { HUD } from './HUD';
import { AdvApi } from '../AdvApi';
import { advAPIResponse } from '../types/Responses/AdvResponse';

export function getHunger() {
    AdvApi.get<GetHungerResponse>('/hunger/get')
        .then(response => {
            updateHunger(response.data.current_hunger);
        })
        .catch(error => false);
}

export function updateHunger(newHunger: number) {
    HUD.elements.hungerProgressBar.setCurrentValue(newHunger);
}

export interface GetHungerResponse extends advAPIResponse {
    data: {
        current_hunger: number;
    };
}
