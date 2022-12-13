import { commonMessages, gameLogger } from '../utilities/gameLogger';
import { clientOverlayInterface } from '../clientScripts/clientOverlayInterface';
import selectContainer from '../selectContainer.js';
import { selectedCheck, selectItemEvent } from '../selectitem.js';
import countdown from '../utilities/countdown.js';
import { updateHunger } from '../clientScripts/hunger';
import { checkInventoryStatus, Inventory } from '../clientScripts/inventory.js';
import { AdvApi } from '../AdvApi';
const cropsModule = {
    intervalID: null,
    init() {
        this.getCountdown();
        [...document.getElementsByClassName("crop_type")].forEach(element => element.addEventListener('click', (event) => selectContainer.showSelect(event)));
        document.getElementById("data_container").querySelectorAll("button")[0].addEventListener("click", () => this.grow());
        document.getElementById("seed_generator").children[3].addEventListener("click", () => this.seedGenerator());
        document.getElementById("cancel_action").addEventListener("click", () => this.destroyCrops());
        document.getElementById("harvest_action").addEventListener("click", () => this.updateCrop());
        // selectitem.js
        selectItemEvent.addSelectEvent();
        selectContainer.fetchData('crops');
    },
    getCountdown() {
        document.getElementById("growing").innerHTML = "No crops growing";
        AdvApi.get('./').then((response) => {
            let responseText = response[1];
            let endTime = response.data.date * 1000;
            let harvest = parseInt(responseText.harvest);
            this.intervalID = setInterval(() => {
                let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                if (document.getElementById("time") == null) {
                    clearInterval(this.intervalID);
                }
                else {
                    document.getElementById("time").innerText = hours + "h " + minutes + "m " + seconds + "s ";
                    document.getElementById("cancel_action").style.display = "inline-block";
                    document.getElementById("harvest_action").style.display = "none";
                }
                // Check if countdown is finished and harvest is true
                if (remainder < 0 && harvest === 1) {
                    clearInterval(this.intervalID);
                    document.getElementById("harvest_action").style.display = "inline-block";
                    document.getElementById("cancel_action").style.display = "none";
                    document.getElementById("time").innerText = "";
                    document.getElementById("growing").innerText = "Finished";
                }
                else if (remainder < 0) {
                    clearInterval(this.intervalID);
                    document.getElementById("growing").innerText = "No crops growing";
                    document.getElementById("time").innerText = "";
                    document.getElementById("cancel_action").style.display = "none";
                    document.getElementById("harvest_action").style.display = "none";
                }
            }, 1000);
            setTimeout(() => clientOverlayInterface.adjustWrapperHeight(), 1100);
        });
    },
    grow() {
        let workforce = selectContainer.getWorkforceAmount();
        let crop_type = selectContainer.getSelectedType();
        if (workforce === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        }
        else if (crop_type.length === 0) {
            gameLogger.addMessage("You need to select the crop you are trying to grow", true);
            return false;
        }
        let data = {
            workforce,
            crop_type
        };
        AdvApi.post('/', data).then((response) => {
            updateHunger(response.data.newHunger);
            this.updateUI(response.data.availWorkforce);
        });
    },
    updateCrop() {
        if (checkInventoryStatus) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        AdvApi.post('./', {}).then((response) => {
            this.updateUI(response.data.availWorkforce);
        });
    },
    updateUI(availWorkforce) {
        this.getCountdown();
        // updateCountdownTab();
        Inventory.update();
        selectContainer.setAvailableWorkforce(availWorkforce);
    },
    destroyCrops() {
        let data = "model=Crops" + "&method=destroyCrops";
        AdvApi.post('./', {}).then((response) => {
            clearInterval(this.intervalID);
            this.updateUI(response.data.availWorkforce);
        });
    },
    // img() {
    //     let img = document.getElementById("type_img");
    //     let select = document.getElementById("form_select");
    //     let name = select.children[select.selectedIndex].value;
    //     if (name.length < 1) {
    //         return;
    //     }
    //     img.style = "display:block";
    //     img.src = "public/images/" + name;
    // },
    seedGenerator() {
        let itemData = selectedCheck();
        if (itemData === false) {
            return false;
        }
        let items = ["potato", "tomato", "corn", "carrots", "cabbages", "wheat", "sugar", "spices", "apples", "oranges", "watermelon"];
        if (!items.includes(itemData[0])) {
            gameLogger.addMessage("ERROR: Pick a valid item", true);
            return false;
        }
        let selected_amount = document.getElementById("selected_amount");
        const data = {
            type: itemData[0],
            amount: itemData[1]
        };
        AdvApi.post('./', data).then((response) => {
            Inventory.update();
        });
    }
};
export default cropsModule;
