import { SkillActionContainer } from './../SkillActionContainer.js';
import { commonMessages, gameLogger } from '../utilities/gameLogger.js';
import { ClientOverlayInterface } from '../clientScripts/clientOverlayInterface.js';
import { ItemSelector } from '../ItemSelector.js';
import countdown from '../utilities/countdown.js';
import { updateHunger } from '../clientScripts/hunger.js';
import { Inventory } from '../clientScripts/inventory.js';
import { AdvApi } from '../AdvApi.js';
class CropsModule extends SkillActionContainer {
    constructor() {
        super();
        this.init();
    }
    init() {
        this.cancelActionButton.addEventListener("click", () => this.updateCrop(true));
        this.finishActionButton.addEventListener("click", () => this.updateCrop(false));
        this.startActionButton.addEventListener("click", () => this.grow());
        this.getCountdown();
        this.addSelectEvent();
        document.getElementById("seed_generator_action").addEventListener("click", () => this.seedGenerator());
        this.fetchData('crops');
        ItemSelector.setup();
    }
    getCountdown() {
        this.infoActionElement.innerHTML = "No crops growing";
        AdvApi.get('/crops/countdown').then((response) => {
            let endTime = response.data.crop_countdown * 1000;
            let crop_type = response.data.crop_type;
            this.intervalID = setInterval(() => {
                let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                if (document.getElementById("time") == null) {
                    clearInterval(this.intervalID);
                }
                else if (remainder < 0 && crop_type) {
                    clearInterval(this.intervalID);
                    this.finishActionButton.style.display = "inline-block";
                    this.cancelActionButton.style.display = "none";
                    document.getElementById("time").innerText = "";
                    this.infoActionElement.innerText = "Finished";
                }
                else if (remainder < 0) {
                    clearInterval(this.intervalID);
                    this.infoActionElement.innerText = "No crops growing";
                    document.getElementById("time").innerText = "";
                    this.cancelActionButton.style.display = "none";
                    this.finishActionButton.style.display = "none";
                }
                else {
                    document.getElementById("time").innerText = hours + "h " + minutes + "m " + seconds + "s ";
                    this.cancelActionButton.style.display = "inline-block";
                    this.finishActionButton.style.display = "none";
                }
            }, 1000);
            setTimeout(() => ClientOverlayInterface.adjustWrapperHeight(), 1100);
        });
    }
    grow() {
        let workforce_amount = this.getWorkforceAmount();
        let crop_type = this.getSelectedType();
        if (workforce_amount === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        }
        else if (crop_type.length === 0) {
            gameLogger.addMessage("You need to select the crop you are trying to grow", true);
            return false;
        }
        let data = {
            workforce_amount,
            crop_type
        };
        AdvApi.post('/crops/start', data).then((response) => {
            updateHunger(response.data.new_hunger);
            Inventory.update();
            this.updateUI(response.data.avail_workforce);
        }).catch(() => false);
    }
    updateCrop(cancel) {
        if (Inventory.isFull() && !cancel) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let data = {
            is_cancelling: cancel
        };
        AdvApi.post('/crops/end', data).then((response) => {
            this.updateUI(response.data.avail_workforce);
            Inventory.update();
        }).catch(() => false);
    }
    seedGenerator() {
        let item = ItemSelector.selected;
        const data = {
            item: item.name,
            amount: item.amount
        };
        AdvApi.post('/crops/generate', data).then((response) => {
            Inventory.update();
            ItemSelector.clearContainer();
        }).catch(() => false);
    }
    updateUI(availWorkforce) {
        this.getCountdown();
        clearInterval(this.intervalID);
        this.setAvailableWorkforce(availWorkforce);
    }
}
export default CropsModule;
