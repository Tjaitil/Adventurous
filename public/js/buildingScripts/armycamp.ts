import { inputHandler } from '../clientScripts/inputHandler.js';
import { ProgressBar } from '../progressBar.js';
import { selectedCheck, selectItemEvent } from '../ItemSelector.js';
import countdown from '../utilities/countdown.js';
import { gameLogger } from '../utilities/gameLogger.js';
import { AdvApi } from './../AdvApi.js';
import { Inventory } from './../clientScripts/inventory.js';
import { BaseRunWarriorActionRequest, ChangeWarriorTypeRequest, RestWarriorsRequest, RunSingleWarriorActionRequest, StartTrainingRequest } from './../types/requests/ArmyCampRequests.js';
import { RunSingleWarriorActionResponse, RunWarriorActionResponse } from './../types/responses/ArmycampResponses.js';
import { WarriorActions } from './../types/WarriorActions.js';
import { WarriorResource } from './../types/WarriorResource.js';
import { WarriorStatus } from './../types/WarriorStatus.js';

const armycampModule = {
    singleWarriorAction: [WarriorActions.HEAL_WARRIOR, WarriorActions.CHANGE_WARRIOR_TYPE] as string[],
    warriors: <Warrior[]>[],
    actionTypeInput: HTMLSelectElement = null,
    trainingTypeInput: HTMLSelectElement = null,
    changeWarriorTypeInput: HTMLSelectElement = null,
    async init() {
        this.trainingTypeInput = document.getElementById("training-type-select-wrapper").children[1];
        this.actionTypeInput = document.getElementsByName("action")[0];
        this.changeWarriorTypeInput = document.getElementsByName("new-warrior-type")[0];
        document.getElementById("actions").children[1].addEventListener("change", () => this.toggleAdditionalActionElements());
        document.getElementById("actions").getElementsByTagName("button")[0].addEventListener("click", () => this.runWarriorAction());
        document.getElementsByName("action")[0].addEventListener("change", () => this.checkWarriorAmountForAction());

        selectItemEvent.addSelectEvent();
        await this.getWarriorData();

        let buttons = document.getElementById("news_content_main_content").querySelectorAll("button");
        buttons[0].addEventListener("click", () => inputHandler.fetchBuilding('ArmyMissions'));
        buttons[1].addEventListener("click", () => inputHandler.fetchBuilding('Armory'));
    },
    getActionType(): string {
        return this.actionTypeInput.options[this.actionTypeInput.selectedIndex].value;

    },
    toggleAdditionalActionElements() {
        let value = this.getActionType();

        let divs = {

        };
        divs[WarriorActions.HEAL_WARRIOR] = "heal";
        divs[WarriorActions.START_TRAINING] = "training-type-select-wrapper";
        divs[WarriorActions.CHANGE_WARRIOR_TYPE] = "change-warrior-type-select-wrapper";

        for (const key in divs) {
            // Get the indexed item by the key:
            const indexedItem = divs[key];
            if (key == value) {
                document.getElementById(indexedItem).style.display = "block";
            }
            else {
                document.getElementById(indexedItem).style.display = "none";
            }
        }
    },
    retrieveWarriorsIdsSelected() {
        return this.warriors.filter((element) => element.isSelected).map((element) => element.warrior_id);
    },
    async getWarriorData() {
        let warriorsDivs = [...document.querySelectorAll(".warrior")] as HTMLElement[];

        await AdvApi.get<RunWarriorActionResponse>('/armycamp/warriors').then((response) => {
            response.data.warriors.forEach((element, index) => {
                let warrior = new Warrior(response.data.warriors[index], warriorsDivs[index])
                this.warriors.push(warrior);
            });
        }).catch((err) => console.log(err));
    },
    checkWarriorAmountForAction() {
        let select = document.getElementsByName("action")[0];
        // let val = select.children[select.selectedIndex].value;
        let warning = document.getElementById("multiple-warrior-action-warning");

        if (this.singleWarriorAction.includes(this.getActionType()) && this.retrieveWarriorsIdsSelected().length > 1) {
            warning.style.visibility = "visible";
        } else {
            warning.style.visibility = "hidden";
        }
    },
    runWarriorAction() {
        let actionName = this.actionTypeInput.options[this.actionTypeInput.selectedIndex].value;

        if (actionName.length === 0) {
            gameLogger.addMessage("ERROR: Select a action to perform", true);
            return false
        }

        let selectedWarriors = this.retrieveWarriorsIdsSelected();

        if (selectedWarriors.length === 0) {
            gameLogger.addMessage("ERROR: You have not selected any warriors for action", true);
            return false;
        } else if (this.singleWarriorAction.includes(actionName) && selectedWarriors.length > 1) {
            gameLogger.addMessage("ERROR: Only 1 warrior allowed for " + actionName + " action", true);
            return false;
        }

        let data: BaseRunWarriorActionRequest = {
            warrior_ids: this.retrieveWarriorsIdsSelected()
        }


        let url = "/armycamp/";
        let urls = [];
        urls[WarriorActions.TRANSFER_WARRIOR] = "transfer";
        urls[WarriorActions.REST_WARRIOR] = "toggleRest";
        urls[WarriorActions.OFF_REST_WARRIOR] = "toggleRest";
        urls[WarriorActions.HEAL_WARRIOR] = "healWarrior";
        urls[WarriorActions.START_TRAINING] = "startTraining";
        urls[WarriorActions.CHANGE_WARRIOR_TYPE] = "changeWarriorType";

        let actionUrl = urls[actionName] ?? "";

        let updateInventory;

        if (actionName === WarriorActions.HEAL_WARRIOR) {
            let itemData = selectedCheck();
            if (!itemData) {
                return false;
            }
            let data = {
                warrior_id: this.retrieveWarriorsIdsSelected()[0],
                item: itemData.item,
                amount: itemData.amount
            };

            AdvApi.post<RunSingleWarriorActionResponse>(url + actionUrl, data).then((response) => {
                this.updatePage(actionName, [response.data.warrior]);
            }).catch(() => false);
        }
        else if (actionName === WarriorActions.CHANGE_WARRIOR_TYPE) {

            let new_warrior_type = this.changeWarriorTypeInput.options[this.changeWarriorTypeInput.selectedIndex].value;
            let data: ChangeWarriorTypeRequest = {
                warrior_id: this.retrieveWarriorsIdsSelected()[0],
                new_warrior_type
            }

            AdvApi.post<RunSingleWarriorActionResponse>(url + actionUrl, data).then((response) => {
                this.updatePage(actionName, [response.data.warrior] ?? []);
                Inventory.update();
            });
        }
        else {
            if (actionName === WarriorActions.START_TRAINING) {
                if (!this.trainingTypeInput) {
                    gameLogger.addMessage("ERROR: Please select training type!", true);
                    return false;
                }

                let selectedType = this.trainingTypeInput.options[this.trainingTypeInput.selected];

                data = <StartTrainingRequest>{
                    ...data,
                    training_type: selectedType
                }
            } else if (actionName === WarriorActions.REST_WARRIOR) {
                data = <RestWarriorsRequest>{
                    ...data,
                    is_starting_rest: true,
                }
            } else if (actionName === WarriorActions.OFF_REST_WARRIOR) {
                data = <RestWarriorsRequest>{
                    ...data,
                    is_starting_rest: false,
                }
            }

            AdvApi.post<RunSingleWarriorActionResponse>(url + actionUrl, data).then((response) => {
                this.updatePage(actionName, [response.data.warrior] ?? []);

                if (updateInventory) {
                    Inventory.update();
                }
            }).catch(() => false);

        }
    },
    updatePage(actionName: WarriorActions, resources: WarriorResource[]) {
        this.warriors.forEach((element) => {
            if (this.retrieveWarriorsIdsSelected().includes(element.warrior_id)) {
                element.setData(
                    resources.find((resource) => resource.warrior_id === element.warrior_id)
                );
                if (element) {
                    if (actionName === WarriorActions.REST_WARRIOR) {
                        element.updateRestElement(WarriorStatus.RESTING);
                    } else if (actionName === WarriorActions.OFF_REST_WARRIOR) {
                        element.updateRestElement(WarriorStatus.IDLE);
                        element.updateHealthElement();
                    } else if (actionName === WarriorActions.START_TRAINING) {
                        element.runCountdown();
                    } else if (actionName === WarriorActions.HEAL_WARRIOR) {
                        element.updateHealthElement();
                    } else if (actionName === WarriorActions.TRANSFER_WARRIOR) {
                        element.updateLocationElement();
                    } else if (actionName === WarriorActions.CHANGE_WARRIOR_TYPE) {
                        element.updateWarriorTypeElement();
                    }
                }
            }
        });
    },
    onClose() {
        this.warriors = [];
    }
}

export default armycampModule;

class Warrior {
    private data: WarriorResource;
    private intervalID: number;
    private wrapper: HTMLElement;
    private countdownElement: HTMLElement;
    private statusElement: HTMLElement;
    private staminaProgressBar: ProgressBar;
    private strengthProgressBar: ProgressBar;
    private techniqueProgressBar: ProgressBar;
    private precisionProgressBar: ProgressBar;
    public isSelected = false;

    public warrior_id: number;


    constructor(data: WarriorResource, wrapper: HTMLElement) {
        this.wrapper = wrapper;
        this.setData(data);

        let staminaProgressBarElement = this.wrapper.querySelectorAll(".stamina_skill_bar")[0] as HTMLElement;
        let strengthProgressBarElement = this.wrapper.querySelectorAll(".strength_skill_bar")[0] as HTMLElement;
        let techniqueProgressBarElement = this.wrapper.querySelectorAll(".technique_skill_bar")[0] as HTMLElement;
        let precisionProgressBarElement = this.wrapper.querySelectorAll(".precision_skill_bar")[0] as HTMLElement;

        this.staminaProgressBar = new ProgressBar(staminaProgressBarElement,
            {
                currentValue: this.data.levels.stamina_xp,
                maxValue: this.data.levels.stamina_next_level_xp,
                finishedclass: true,
            },);
        this.strengthProgressBar = new ProgressBar(strengthProgressBarElement,
            {
                currentValue: this.data.levels.strength_xp,
                maxValue: this.data.levels.strength_next_level_xp,
                finishedclass: true,
            });
        this.techniqueProgressBar = new ProgressBar(techniqueProgressBarElement,
            {
                currentValue: this.data.levels.technique_xp,
                maxValue: this.data.levels.technique_next_level_xp,
                finishedclass: true
            });
        this.precisionProgressBar = new ProgressBar(precisionProgressBarElement,
            {
                currentValue: this.data.levels.precision_xp,
                maxValue: this.data.levels.precision_next_level_xp,
                finishedclass: true
            });
        this.checkIfLevelUp();

        this.wrapper.addEventListener('click', (event) => this.toggleSelect(event));
        this.countdownElement = this.wrapper.querySelectorAll(".countdown")[0] as HTMLElement;
        this.statusElement = this.wrapper.querySelectorAll(".warrior-status")[0] as HTMLElement;
        this.runCountdown();
    }

    public setData(data: WarriorResource) {
        this.data = data;
        this.warrior_id = data.warrior_id;
    }

    public updateLocationElement() {
        document.getElementsByClassName(".warrior-location")[0].innerHTML = this.data.location;
    }

    public updateRestElement(status: string) {
        this.updateStatusElement(status);
    }

    private updateStatusElement(status: string) {
        this.statusElement.innerHTML = status;
    }

    public updateHealthElement() {
        this.wrapper.querySelectorAll(".warrior-health")[0].innerHTML = this.data.health + "";
    }

    public updateWarriorTypeElement() {
        let img = this.wrapper.querySelectorAll("img")[0];
        img.src = "public/images/" + this.data.type + " icon.png";
    }

    public checkIfLevelUp() {
        if (
            this.precisionProgressBar.isFinished ||
            this.staminaProgressBar.isFinished ||
            this.strengthProgressBar.isFinished ||
            this.techniqueProgressBar.isFinished) {

            let button = document.createElement("button");
            button.innerHTML = "Level up " + '&#9650';
            button.addEventListener("click", () => this.levelUPWarrior());
            this.wrapper.querySelectorAll(".warrior_level_up")[0].appendChild(button);
        } else {
            this.wrapper.querySelectorAll(".warrior_level_up")[0].innerHTML = "";
        }
    }

    public calculateSkillbar() {

        this.precisionProgressBar.setCurrentValue(this.data.levels.precision_xp);
        this.precisionProgressBar.setMaxValue(this.data.levels.precision_next_level_xp);
        this.precisionProgressBar.calculateProgress();

        this.staminaProgressBar.setCurrentValue(this.data.levels.stamina_xp);
        this.staminaProgressBar.setMaxValue(this.data.levels.stamina_next_level_xp);
        this.staminaProgressBar.calculateProgress();

        this.strengthProgressBar.setCurrentValue(this.data.levels.strength_xp);
        this.strengthProgressBar.setMaxValue(this.data.levels.strength_next_level_xp);
        this.strengthProgressBar.calculateProgress();

        this.techniqueProgressBar.setCurrentValue(this.data.levels.technique_xp);
        this.techniqueProgressBar.setMaxValue(this.data.levels.technique_next_level_xp);
        this.techniqueProgressBar.calculateProgress();

        this.checkIfLevelUp();
    }

    public setLevels() {
        this.wrapper.querySelectorAll(".warrior-skill-level")[0].innerHTML = this.data.levels.stamina_level + "";
        this.wrapper.querySelectorAll(".warrior-skill-level")[1].innerHTML = this.data.levels.technique_level + "";
        this.wrapper.querySelectorAll(".warrior-skill-level")[2].innerHTML = this.data.levels.precision_level + "";
        this.wrapper.querySelectorAll(".warrior-skill-level")[3].innerHTML = this.data.levels.strength_level + "";
    }

    public toggleSelect(event: Event) {
        let eventTarget = event.target as HTMLElement;
        if (eventTarget.tagName === 'BUTTON') {
            return false;
        }
        let isToggled = this.wrapper.classList.toggle("warrior-selected");
        this.isSelected = isToggled;

        armycampModule.checkWarriorAmountForAction();
    }

    public setTrainingCountdownData(value: number) {
        this.data.training_countdown = value;
    }

    private levelUPWarrior() {

        let data: RunSingleWarriorActionRequest = {
            warrior_id: this.warrior_id,
        }

        AdvApi.post<RunSingleWarriorActionResponse>('/armycamp/upgradeWarrior', data).then((response) => {
            this.setData(response.data.warrior);
            this.calculateSkillbar();
            this.setLevels();
        }).catch(() => false);
    }

    private endTraining() {
        let data: RunSingleWarriorActionRequest = {
            warrior_id: this.warrior_id,
        }

        AdvApi.post<RunSingleWarriorActionResponse>('/armycamp/endTraining', data).then((response) => {
            this.setData(response.data.warrior);
            this.runCountdown();
        }).catch(() => false);
    }

    public runCountdown() {
        let now = new Date().getTime();
        let { remainder, hours, minutes, seconds } = countdown.calculate(this.data.training_countdown);

        if (remainder < 0 && this.data.fetch_report) {
            // warriorIndex + 1 is the id of the warrior
            clearInterval(this.intervalID);
            var btn = document.createElement("BUTTON");
            var t = document.createTextNode("Get report");
            btn.appendChild(t);
            btn.addEventListener("click", () => this.endTraining());
            this.countdownElement.innerHTML = "";
            this.countdownElement.appendChild(btn);
        }
        else if (remainder < 0) {
            // warriorIndex + 1 is the id of the warrior
            clearInterval(this.intervalID);
            this.countdownElement.innerHTML = "";
        }
        else {
            this.countdownElement.innerHTML = hours + "h " + minutes + "m " + seconds + "s";;
        }
    }
}