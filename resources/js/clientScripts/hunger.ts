import { HUD } from './HUD';
import { AdvApi } from '../AdvApi';
import type { advAPIResponse } from '../types/Responses/AdvResponse';
import { useHungerStore } from '../ui/stores/HungerStore';

export function getHunger() {
  AdvApi.get<GetHungerResponse>('/hunger/get')
    .then(response => {
      updateHunger(response.data.current_hunger);
    })
    .catch(() => false);
}

export function updateHunger(newHunger: number) {
  useHungerStore().setCurrentHunger(newHunger);
  HUD.elements.hungerProgressBar.setCurrentValue(newHunger);
}

export interface GetHungerResponse extends advAPIResponse {
  data: {
    current_hunger: number;
  };
}
