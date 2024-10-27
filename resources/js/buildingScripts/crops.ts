import { UpdateCropsRequest } from './../types/requests/CropsRequests';
import {
    StartGrowingRequest,
    SeedGeneratorRequest,
} from '../types/requests/CropsRequests';
import { SkillActionContainer } from './../SkillActionContainer';
import { commonMessages, GameLogger } from '../utilities/GameLogger';
import { ItemSelector } from '../ItemSelector';
import { updateHunger } from '../clientScripts/hunger';
import { Inventory } from '../clientScripts/inventory';
import { AdvApi } from '../AdvApi';
import { advAPIResponse } from '../types/Responses/AdvResponse';

class CropsModule extends SkillActionContainer {
    constructor() {
        super('Growing', 'No crops growing');
        this.init();
    }

    public init() {
        this.cancelActionButton.addEventListener('click', () =>
            this.updateCrop(true),
        );
        this.finishActionButton.addEventListener('click', () =>
            this.updateCrop(false),
        );
        this.startActionButton.addEventListener('click', () => this.grow());
        this.addSelectEvent();
        this.fetchData('crops');
        this.getCountdown();

        document
            .getElementById('seed_generator_action')
            .addEventListener('click', () => this.seedGenerator());
        ItemSelector.setup();
    }

    private getCountdown() {
        this.infoActionElement.innerHTML = 'No crops growing';
        AdvApi.get<CropCountdownResponse>('/crops/countdown').then(response => {
            this.startCountdownAndUpdateUI({
                endTime: response.crop_finishes_at * 1000,
                type: response.crop_type,
            });
        });
    }

    public async grow() {
        const workforce_amount = this.getWorkforceAmount();
        const crop_type = this.getSelectedType();

        if (workforce_amount === 0) {
            GameLogger.addMessage(
                'You need to select the amount of workers',
                true,
            );
            return false;
        } else if (crop_type.length === 0) {
            GameLogger.addMessage(
                'You need to select the crop you are trying to grow',
                true,
            );
            return false;
        }

        const data: StartGrowingRequest = {
            workforce_amount,
            crop_type,
        };

        await AdvApi.post<GrowCropsResponse>('/crops/start', data)
            .then(response => {
                Inventory.update();
                updateHunger(response.new_hunger);
                this.setAvailableWorkforce(response.avail_workforce);
                this.getCountdown();
            })
            .catch(() => false);
    }

    public async updateCrop(cancel: boolean) {
        if (Inventory.isFull() && !cancel) {
            GameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }

        const data: UpdateCropsRequest = {
            is_cancelling: cancel,
        };

        await AdvApi.post<HarvestCropsResponse>('/crops/end', data)
            .then(response => {
                this.setAvailableWorkforce(response.data.avail_workforce);
                this.clearCountdownAndUpdateUI();
                if (!cancel) {
                    this.getCountdown();
                    Inventory.update();
                }
            })
            .catch(() => false);
    }

    public async seedGenerator() {
        const item = ItemSelector.selected;

        const data: SeedGeneratorRequest = {
            item: item.name,
            amount: item.amount,
        };

        await AdvApi.post('/crops/generate-seeds', data)
            .then(() => {
                Inventory.update();
                ItemSelector.clearContainer();
            })
            .catch(() => false);
    }
}

export default CropsModule;

interface CropCountdownResponse {
    crop_finishes_at: number;
    crop_type: string;
    date: number;
}

interface GrowCropsResponse {
    new_hunger: number;
    avail_workforce: number;
}

interface CropCountdownResponse {
    harvest: number;
    date: number;
}

interface GrowCropsResponse {
    newHunger: number;
    availWorkforce: number;
}

type HarvestCropsResponse = advAPIResponse<{
    avail_workforce: number;
}>;
