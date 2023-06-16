import { StartMiningRequest, UpdateMiningRequest } from './../types/requests/MineRequests';
import { FinishMiningResponse, MineCountdownResponse, StartMiningResponse } from './../types/responses/MineResponses';
import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface.js';
import { AdvApi } from './../AdvApi.js';
import { Inventory } from '../clientScripts/inventory.js';
import countdown from '../utilities/countdown.js';
import { commonMessages, gameLogger } from '../utilities/gameLogger.js';
import { SkillActionContainer } from '../SkillActionContainer.js';
import { updateHunger } from '../clientScripts/hunger.js';


class MineModule extends SkillActionContainer {

    constructor() {
        super();
        this.init();
    }

    public init() {
        this.cancelActionButton.addEventListener("click", () => this.updateMine(true));
        this.finishActionButton.addEventListener("click", () => this.updateMine(false));
        this.startActionButton.addEventListener("click", () => this.setMine());
        this.addSelectEvent();
        this.fetchData('mine');
    }

    private getCountdown() {
        this.infoActionElement.innerHTML = "No miners at work";

        AdvApi.get<MineCountdownResponse>('/mine').then((response) => {
            let endTime = response.data.mining_countdown * 1000;

            if (response.data.mining_type !== 'none') {
                this.infoActionElement.innerHTML = "Currently mining " + response.data.mining_type;
            }
            this.startCountdownAndUpdateUI({
                actionText: "Mining for" + response.data.mining_type,
                noActionText: "No miners at work",
                endTime,
                type: response.data.mining_type
            });

            setTimeout(() => ClientOverlayInterface.adjustWrapperHeight(), 1100);
        })
    }

    private setMine() {
        let mineral = this.getSelectedType();
        let workforce_amount = this.getWorkforceAmount();
        if (workforce_amount === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        } else if (mineral.length === 0) {
            gameLogger.addMessage("You need to select at least one mineral", true);
            return false;
        }
        let data: StartMiningRequest = {
            mineral,
            workforce_amount,
        }

        AdvApi.post<StartMiningResponse>('/', data).then((response) => {
            updateHunger(response.data.new_hunger);
            this.updateUI(response.data.availWorkforce);
            // updateCountdownTab();
            document.getElementById("total_permits").innerHTML =
                "Total permits:" + response.data.new_permits;
        })
    }

    private updateMine(cancel: boolean) {
        if (Inventory.isFull() && !cancel) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }

        let data: UpdateMiningRequest = {
            is_cancelling: cancel
        }


        AdvApi.post<FinishMiningResponse>('/', data).then((response) => {
            this.updateUI(response.data.avail_workforce);
            Inventory.update();
            // updateCountdownTab();
        });
    }

    // private cancelMining() {
    //     AdvApi.post<CancelMiningResponse>('/', {}).then((response) => {
    //         this.updateUI(response.data.availWorkforce);
    //         // updateCountdownTab();
    //     });
    // }

    public updateUI(availWorkforce: number) {
        this.getCountdown();
        clearInterval(this.intervalID);
        this.setAvailableWorkforce(availWorkforce);
    }
}

export default MineModule;