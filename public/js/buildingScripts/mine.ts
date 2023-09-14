import { AdvApi } from './../AdvApi.js';
import { Inventory } from '../clientScripts/inventory.js';
import { gameLogger } from '../utilities/gameLogger.js';
import { SkillActionContainer } from '../SkillActionContainer.js';
import { updateHunger } from '../clientScripts/hunger.js';
import { advAPIResponse } from '../types/responses/AdvResponse';


class MineModule extends SkillActionContainer {

    constructor() {
        super("Mining for", "No miners at work");
        this.init();
    }

    public init() {
        this.cancelActionButton.addEventListener("click", () => this.updateMine(true));
        this.finishActionButton.addEventListener("click", () => this.updateMine(false));
        this.startActionButton.addEventListener("click", () => this.setMine());
        this.addSelectEvent();
        this.fetchData('mine');
        this.getCountdown();
        console.log(this.setAvailableWorkforce(2));
    }

    private getCountdown() {
        this.infoActionElement.innerHTML = "No miners at work";

        AdvApi.get<MineCountdownResponse>('/mine/countdown').then((response) => {
            this.startCountdownAndUpdateUI({
                endTime: response.data.mining_finishes_at * 1000,
                type: response.data.mineral_type
            });
        })
    }

    private setMine() {
        let mineral_ore = this.getSelectedType();
        let workforce_amount = this.getWorkforceAmount();

        if (workforce_amount === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        } else if (mineral_ore.length === 0) {
            gameLogger.addMessage("You need to select at least one mineral", true);
            return false;
        }

        let data: StartMiningRequest = {
            mineral_ore,
            workforce_amount,
        }

        AdvApi.post<StartMiningResponse>('/mine/start', data).then((response) => {
            updateHunger(response.data.new_hunger);
            this.setAvailableWorkforce(response.data.avail_workforce);
            this.getCountdown();
            document.getElementById("total_permits").innerHTML = "" + response.data.new_permits;
        });
    }

    private updateMine(cancel: boolean) {

        let data: UpdateMiningRequest = {
            is_cancelling: cancel
        }

        AdvApi.post<FinishMiningResponse>('/mine/end', data).then((response) => {
            this.setAvailableWorkforce(response.data.avail_workforce);
            if (cancel) {
                this.clearCountdownAndUpdateUI();
            } else {
                this.getCountdown();
                Inventory.update();
            }
        });
    }
}

export default MineModule;

export interface StartMiningRequest {
    mineral_ore: string;
    workforce_amount: number;
}

export interface UpdateMiningRequest {
    is_cancelling: boolean;
}

export interface BuyPermitsRequest {
    location: string;
}

export interface MineCountdownResponse extends advAPIResponse {
    data: {
        mining_finishes_at: number;
        mineral_type: string;
    }
}

export interface StartMiningResponse extends advAPIResponse {
    data: {
        avail_workforce: number;
        new_permits: number;
        new_hunger: number;
    }
}

export interface FinishMiningResponse extends advAPIResponse {
    data: {
        avail_workforce: number;
    }
}