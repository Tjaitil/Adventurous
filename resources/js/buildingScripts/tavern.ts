import { Inventory } from './../clientScripts/inventory';
import { selectItemEvent } from "../ItemSelector.js";
import { gameLogger } from '../utilities/gameLogger.js';
import { AdvApi } from './../AdvApi';
import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface.js';
import { ProgressBar } from './../progressBar.js';
import { RecruitWorkerRequest, RestoreHealthRequest } from './../types/requests/TavernRequests';
import { GetHealDataResponse, RestoreHealthResponse } from './../types/responses/TavernResponses';
import { HUD } from '../clientScripts/HUD';


const tavernModule = {
    init() {
        document
            .getElementById("tavern-eat-button")
            .addEventListener("click", () => this.eat());
        selectItemEvent.addSelectEvent();
        [...document.getElementsByClassName("tavern-worker-recrute")].forEach(
            (element) =>
                element.addEventListener("click", (event) =>
                    this.recruitWorker(event)
                )
        );
        let hungerBar = document.getElementById("hunger_progressBar");
        let tavernHungerBar = hungerBar.cloneNode(true);
        let eatDiv = document.getElementById("eat");
        eatDiv.insertBefore(tavernHungerBar, eatDiv.children[1]);
    },
    recruitWorker(event) {
        let element = event.target.closest(".tavern-worker");
        let type = element
            .querySelectorAll(".tavern-worker-type")[0]
            .innerText.trim();
        if (!type) return false;
        let level = element.querySelectorAll(".tavern-worker-level")[0]
            ? element
                .querySelectorAll("tavern-worker-level")[0]
                .innerText.trim()
            : false;

        let data: RecruitWorkerRequest = {
            type,
            level
        }

        AdvApi.post('/tavern/recruit', data).then((response) => {
            let container = event.target.closest(".tavern-worker");
            document
                .getElementById("tavern-workers-grid-container")
                .removeChild(container);
        })
    },
    getHealingAmount(item) {
        if (item.length == 0) return false;

        AdvApi.get<GetHealDataResponse>('/hunger/item/get' + new URLSearchParams().set('item', item)).then((response) => {
            if (response.data.heal === 0) {
                document.getElementById("item_healing_amount").innerText =
                    "No healing from this item";
            } else {
                document.getElementById("item_healing_amount").innerText =
                    "Healing per item " + response.data.heal;
            }
            ClientOverlayInterface.adjustWrapperHeight();
        });
    },
    eat() {
        let item = document
            .getElementById("selected")
            .querySelectorAll("figure")[0]
            .children[1].innerHTML.toLowerCase();
        if (item.length == 0) {
            gameLogger.addMessage("ERROR: Select a item to eat!", true);
            return false;
        }
        let inputElement = <HTMLInputElement>document.getElementById("healing-item-amount");

        let amount = parseInt(inputElement.value);

        if (amount == 0 || amount == null) {
            gameLogger.addMessage("ERROR: Select a amount", true);
            return false;
        }
        let data: RestoreHealthRequest = {
            item,
            amount
        }

        AdvApi.post<RestoreHealthResponse>('/hunger/restore', data).then((response) => {

            HUD.elements.hungerProgressBar.setCurrentValue(response.data.new_hunger);
            Inventory.update();

            document.getElementById("selected").innerHTML = "";
            let inputElement = <HTMLInputElement>document.getElementById("healing-item-amount");

            inputElement.value = "";
        });
    },
};
export default tavernModule;
