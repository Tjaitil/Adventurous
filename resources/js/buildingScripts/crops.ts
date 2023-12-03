import { UpdateCropsRequest } from './../types/requests/CropsRequests';
import { StartGrowingRequest, SeedGeneratorRequest } from '../types/requests/CropsRequests';
import { SkillActionContainer } from './../SkillActionContainer';
import { commonMessages, gameLogger } from '../utilities/gameLogger';
import { ItemSelector } from '../ItemSelector';
import { updateHunger } from '../clientScripts/hunger';
import { Inventory } from '../clientScripts/inventory';
import { AdvApi } from '../AdvApi';
import { advAPIResponse } from '../types/Responses/AdvResponse';

class CropsModule extends SkillActionContainer {

    constructor() {
        super("Growing", "No crops growing");
        this.init();
    }

    public init() {
        this.cancelActionButton.addEventListener("click", () => this.updateCrop(true));
        this.finishActionButton.addEventListener("click", () => this.updateCrop(false));
        this.startActionButton.addEventListener("click", () => this.grow());
        this.addSelectEvent();
        this.fetchData('crops');
        this.getCountdown();

        document.getElementById("seed_generator_action").addEventListener("click",
            () => this.seedGenerator());
        ItemSelector.setup();
    }

    private getCountdown() {
        this.infoActionElement.innerHTML = "No crops growing";
        AdvApi.get<CropCountdownResponse>('/crops/countdown').then((response) => {
            this.startCountdownAndUpdateUI({
                endTime: response.data.crop_finishes_at * 1000,
                type: response.data.crop_type
            });
        });
    }

    public grow() {
        let workforce_amount = this.getWorkforceAmount();
        let crop_type = this.getSelectedType();

        if (workforce_amount === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        } else if (crop_type.length === 0) {
            gameLogger.addMessage("You need to select the crop you are trying to grow", true);
            return false;
        }

        let data: StartGrowingRequest = {
            workforce_amount,
            crop_type
        };

        AdvApi.post<GrowCropsResponse>('/crops/start', data).then((response) => {
            Inventory.update();
            updateHunger(response.data.new_hunger);
            this.setAvailableWorkforce(response.data.avail_workforce);
            this.getCountdown();
        });
    }

    public updateCrop(cancel: boolean) {
        if (Inventory.isFull() && !cancel) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }

        let data: UpdateCropsRequest = {
            is_cancelling: cancel
        }

        AdvApi.post<HarvestCropsResponse>('/crops/end', data).then((response) => {
            this.setAvailableWorkforce(response.data.avail_workforce);
            if (cancel) {
                this.clearCountdownAndUpdateUI();
            } else {
                this.getCountdown();
                Inventory.update();
            }
        }).catch(() => false);
    }

    public seedGenerator() {

        let item = ItemSelector.selected;

        const data: SeedGeneratorRequest = {
            item: item.name,
            amount: item.amount
        }

        AdvApi.post('/crops/generate', data).then((response) => {
            Inventory.update();
            ItemSelector.clearContainer();
        }).catch(() => false);
    }
}

export default CropsModule;

export interface CropCountdownResponse extends advAPIResponse {
    data: {
        crop_finishes_at: number;
        crop_type: string;
        date: number;
    }
}

export interface GrowCropsResponse extends advAPIResponse {
    data: {
        new_hunger: number;
        avail_workforce: number;
    }
}

export interface CropCountdownResponse {
    harvest: number,
    date: number,
}

export interface GrowCropsResponse {
    newHunger: number,
    availWorkforce: number,
}

export interface DestroyCropsResponse {
    availWorkforce: number,
}

export interface HarvestCropsResponse {
    availWorkforce: number,
}
export interface HarvestCropsResponse extends advAPIResponse {
    data: {
        avail_workforce: number;
    }
}