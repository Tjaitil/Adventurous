import { ChangeArmorResponse } from '../types/responses/ArmoryResponses.js';
import { changeArmorRequest } from '../types/requests/ArmoryRequests.js';
import { Inventory } from './../clientScripts/inventory.js';
import { inputHandler } from '../clientScripts/inputHandler.js';
import { ItemSelector } from '../selectitem.js';
import { AdvApi } from '../AdvApi.js';


const armoryModule = {
    init() {
        document.getElementById("news_content_main_content").querySelectorAll("button")[0].addEventListener("click", function () {
            inputHandler.fetchBuilding('armycamp');
        });
        ItemSelector.setup();
        ItemSelector.addSelectEventToInventory();
        ItemSelector.hideSelectedAmountInput();
        this.addClickEvents("all");
        document.getElementById("put_on_button").addEventListener("click", () => this.wearArmor());
    },

    addClickEvents(childIndex: number | 'all') {
        let elements;
        if (typeof childIndex === 'string') {
            elements = [...document.getElementsByClassName("armory_view_part")];
        } else {
            // Find armory_view_part inside warriorDiv that has childIndex
            let warriorDiv = document.getElementById("warrior_container").getElementsByClassName("armory_view")[childIndex];
            elements = [...warriorDiv.getElementsByClassName("armory_view_part")];
        }

        elements.forEach(element =>
            element.addEventListener("click", event => this.removeArmor(event))
        );
    },

    toggleOption() {
        let element = document.getElementById("selected").children[0].children[1].innerHTML;
        if (element.search("Sword") != -1 || element.search("Dagger") != -1) {
            document.getElementById("type").style.visibility = "visible";
            ItemSelector.hideSelectedAmountInput();
        }
        else if (element.search("Arrow") != -1 || element.search("Knives") != -1) {
            ItemSelector.showSelectedAmountInput();
        }
        else {
            document.getElementById("type").style.visibility = "hidden";
            ItemSelector.hideSelectedAmountInput();
        }
    },

    wearArmor() {
        let selectElement = <HTMLSelectElement>document.getElementById("select_warrior");
        let warrior_id = selectElement.selectedIndex;

        let select = <HTMLSelectElement>document.getElementById("type");
        let hand;
        if (select.style.visibility == "visible") {
            hand = select.options[select.selectedIndex].value;
        }
        else {
            hand = "";
        }

        if (ItemSelector.isItemValid() === false) return false;

        let item = ItemSelector.selected;

        let data: changeArmorRequest = {
            warrior_id,
            item: item.name,
            hand,
            amount: item.amount,
            is_removing: false,
            part: "",
        }

        AdvApi.post<ChangeArmorResponse>('/api/armory/add', data).then((response) => {
            document.getElementById("selected").innerHTML = "";
            this.replaceWarriorContainer(response.html.warrior_armory, data.warrior_id);
            Inventory.update();
        }).catch(() => false);
    },

    removeArmor(event: Event) {
        let partElement = <HTMLElement>event.currentTarget;

        if (!partElement) return false;
        let parent = partElement.closest(".armory_view");
        let warrior_id = parseInt(parent.querySelectorAll(".armory_view_warrior_id")[0].innerHTML.trim());

        if (!parent.querySelectorAll(".armory_view_warrior_id")[0]) return false;
        let part = partElement.classList[1];
        let hand = "";

        if (part === 'left_hand') {
            hand = "left_hand";
        } else if (part === 'right_hand') {
            hand = "right_hand";
        }
        let item = partElement.title;
        if (item === 'none') return false;

        let data: changeArmorRequest = {
            warrior_id,
            item,
            hand,
            amount: 1,
            is_removing: true,
            part,
        }

        AdvApi.post<ChangeArmorResponse>('/api/armory/remove', data).then((response) => {
            document.getElementById("selected").innerHTML = "";
            this.replaceWarriorContainer(response.html.warrior_armory, data.warrior_id);
            Inventory.update();
        }).catch(() => false);
    },

    replaceWarriorContainer(newContainer: string, index: number) {
        let parentContainer = document.getElementById("warrior_container");
        let warriorContainer = document.getElementsByClassName("armory_view");
        let replaceIndex = index - 1;

        // Convert newContainer string into object
        let div = document.createElement("div");
        div.innerHTML = newContainer;
        if (!warriorContainer[replaceIndex]) return false;
        parentContainer.replaceChild(div.getElementsByClassName("armory_view")[0], warriorContainer[replaceIndex]);
        this.addClickEvents(replaceIndex);
    },
};
export default armoryModule;