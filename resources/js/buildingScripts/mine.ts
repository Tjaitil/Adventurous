import { AdvApi } from './../AdvApi';
import { Inventory } from '../clientScripts/inventory';
import { GameLogger } from '../utilities/GameLogger';
import { SkillActionContainer } from '../SkillActionContainer';
import { updateHunger } from '../clientScripts/hunger';
import { advAPIResponse } from '../types/Responses/AdvResponse';
import { CustomFetchApi } from '../CustomFetchApi';

class MineModule extends SkillActionContainer {
    constructor() {
        super('Mining for', 'No miners at work');
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

    private getCountdown() {
        this.infoActionElement.innerHTML = 'No miners at work';

        CustomFetchApi.get<MineCountdownResponse>('/mine/countdown').then(
            response => {
                this.startCountdownAndUpdateUI({
                    endTime: response.mining_finishes_at * 1000,
                    type: response.mineral_ore,
                });
            },
        );
    }

    private setMine() {
        const mineral_ore = this.getSelectedType();
        const workforce_amount = this.getWorkforceAmount();

        if (workforce_amount === 0) {
            GameLogger.addMessage(
                'You need to select the amount of workers',
                true,
            );
            return false;
        } else if (mineral_ore.length === 0) {
            GameLogger.addMessage(
                'You need to select at least one mineral',
                true,
            );
            return false;
        }

        const data: StartMiningRequest = {
            mineral_ore,
            workforce_amount,
        };

        AdvApi.post<StartMiningResponse>('/mine/start', data)
            .then(response => {
                updateHunger(response.new_hunger);
                this.setAvailableWorkforce(response.avail_workforce);
                this.getCountdown();
                document.getElementById('total_permits').innerHTML =
                    '' + response.new_permits;
            })
            .catch(() => false);
    }

    private updateMine(cancel: boolean) {
        const data: UpdateMiningRequest = {
            is_cancelling: cancel,
        };

        AdvApi.post<FinishMiningResponse>('/mine/end', data)
            .then(response => {
                this.setAvailableWorkforce(response.avail_workforce);
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

interface MineCountdownResponse {
    mining_finishes_at: number;
    mineral_ore: string;
}

interface StartMiningResponse extends advAPIResponse {
    avail_workforce: number;
    new_permits: number;
    new_hunger: number;
}

interface FinishMiningResponse extends advAPIResponse {
    avail_workforce: number;
}
