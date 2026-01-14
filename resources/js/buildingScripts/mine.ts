import { AdvApi } from './../AdvApi';
import { Inventory } from '../clientScripts/inventory';
import { GameLogger } from '../utilities/GameLogger';
import { SkillActionContainer } from '../SkillActionContainer';
import { updateHunger } from '../clientScripts/hunger';
import type { advAPIResponse } from '../types/Responses/AdvResponse';
import { mineDataLoader } from './buildingLoaders';

class MineModule extends SkillActionContainer {
  constructor() {
    super('Mining for', 'No miners at work', 'mine');
    this.init();
  }

  public init() {
    this.cancelActionButton.addEventListener('click', () =>
      this.updateMine(true),
    );
    this.finishActionButton.addEventListener('click', () =>
      this.updateMine(false),
    );
    this.startActionButton.addEventListener('click', () => this.setMine());
    this.addSelectEvent();
    this.fetchData('mine');
    this.getCountdown();
  }

  private async getCountdown() {
    this.infoActionElement.innerHTML = 'No miners at work';

    await mineDataLoader.countdown().then(response => {
      this.startCountdownAndUpdateUI({
        endTime: response.mining_finishes_at
          ? response.mining_finishes_at * 1000
          : null,
        type: response.mineral_ore,
      });
    });
  }

  private async setMine() {
    const mineral_ore = this.getSelectedType();
    const workforce_amount = this.getWorkforceAmount();

    if (workforce_amount === 0) {
      GameLogger.addMessage('You need to select the amount of workers', true);
      return false;
    } else if (mineral_ore.length === 0) {
      GameLogger.addMessage('You need to select at least one mineral', true);
      return false;
    }

    const data: StartMiningRequest = {
      mineral_ore,
      workforce_amount,
    };

    await AdvApi.post<StartMiningResponse>('/mine/start', data)
      .then(response => {
        updateHunger(response.data.new_hunger);
        this.setAvailableWorkforce(response.data.avail_workforce);
        this.getCountdown();
        document.getElementById('total_permits').innerHTML =
          '' + response.data.new_permits;
      })
      .catch(() => false);
  }

  private async updateMine(cancel: boolean) {
    const data: UpdateMiningRequest = {
      is_cancelling: cancel,
    };

    await AdvApi.post<FinishMiningResponse>('/mine/end', data)
      .then(response => {
        this.setAvailableWorkforce(response.data.avail_workforce);
        if (cancel) {
          this.clearCountdownAndUpdateUI();
        } else {
          this.getCountdown();
          Inventory.update();
        }
      })
      .catch(() => false);
  }
}

export default MineModule;

interface StartMiningRequest {
  mineral_ore: string;
  workforce_amount: number;
}

interface UpdateMiningRequest {
  is_cancelling: boolean;
}

type StartMiningResponse = advAPIResponse<{
  avail_workforce: number;
  new_permits: number;
  new_hunger: number;
}>;

type FinishMiningResponse = advAPIResponse<{
  avail_workforce: number;
}>;
