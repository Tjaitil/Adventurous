import { MineralResource } from './types/MineResource';
import { GetSkillActionDataRequest } from './types/requests/SkillActionContainerRequests';
import { AdvApi } from "./AdvApi.js";
import { Game } from "./advclient.js";
import { ClientOverlayInterface } from "./clientScripts/clientOverlayInterface.js";
import { jsUcfirst } from "./utilities/uppercase.js";
import { CropResource } from './types/CropResource';
import { LevelManager } from './LevelManager.js';
import countdown from './utilities/countdown.js';

export class SkillActionContainer {
    private typeData = <CropResource[] | MineralResource[]>[];
    public workforceData: {};
    private workforceElement = <HTMLInputElement>document.getElementById("workforce_amount");
    public intervalID: number;
    public cancelActionButton = <HTMLElement>null;
    public startActionButton = <HTMLElement>null;
    public finishActionButton = <HTMLElement>null;
    public infoActionElement = <HTMLElement>null;

    constructor() {
        this.cancelActionButton = document.getElementById("cancel-action");
        this.startActionButton = document.getElementById("do-action");
        this.finishActionButton = document.getElementById("finish-action");
        this.infoActionElement = document.getElementById("info-action-element");

    }

    public addSelectEvent() {
        [...document.getElementsByClassName("item-type")].forEach(element =>
            element.addEventListener('click', (event) => this.showSelect(event))
        );
    }

    public getWorkforceAmount(): number {
        if (!this.workforceElement) {
            return 0;
        }
        return parseInt(this.workforceElement.value);
    }

    public getSelectedType(): string {
        let selected_type = <HTMLInputElement>document.getElementById("selected-action-type");
        return selected_type.value;
    }

    public setAvailableWorkforce(amount: number) {
        let available_workforce_input = document.getElementById("data_container_avail_workforce");

        available_workforce_input.innerText = '(' + amount + ')';
    }

    public showSelect(event) {
        let targetElement = event.currentTarget;
        let clone = targetElement.cloneNode(true);
        clone.removeAttribute("onclick");
        let item = targetElement.getAttribute("alt");
        let className;
        if (ClientOverlayInterface.getInterfacePageTitle() === "crops") {
            className = "selected-action-type";
        } else {
            className = "selected-action-type";
        }
        [...document.getElementsByClassName(className)].forEach((element) => {
            if (targetElement === element) {
                element.classList.add("item_selected");
            } else {
                element.classList.remove("item_selected");
            }
        });

        clone.style.border = "none";
        let div = document.getElementById("data_form");
        div.style.visibility = "visible";
        document.getElementById("data").style.visibility = "visible";
        let div_inputs = div.querySelectorAll("input");
        div_inputs[0].value = item

        let baseReduction;
        let perWorkforce;
        let level;
        let levelRequired: number;
        let time: number;

        const levelInputField = <HTMLInputElement>document.getElementsByName("level")[0];

        let matchedType: CropResource | MineralResource;

        // Check wether or not the player are in crops or mine
        if (Game.getProperty("building") === "crops") {
            let data = this.typeData as CropResource[];
            matchedType = data.find((type) => type.crop_type === item);

            let seedInputField = <HTMLInputElement>document.getElementsByName("seeds")[0];

            seedInputField.value = matchedType.seed_required + "";

            levelInputField.value = matchedType.farmer_level + "";

            level = LevelManager.getFarmerlevel();
        } else if (Game.getProperty("building") === "mine") {
            let data = this.typeData as MineralResource[];
            matchedType = data.find((type) => type.mineral_type === item);

            let permitsInputField = <HTMLInputElement>document.getElementsByName("permits")[0];
            permitsInputField.value = matchedType.permit_cost + "";

            levelInputField.value = matchedType.miner_level + "";

            level = LevelManager.getMinerLevel();
        } else {
            console.log("Error: No building selected");
            return;
        }

        baseReduction = Number(matchedType.time * (this.workforceData["efficiency_level"] * 0.01)
        ).toFixed(2);
        perWorkforce = (Number(matchedType.time) * 0.005).toFixed(2);

        let input = document.getElementsByName("level")[0];
        LevelManager.showHasLevelRequired("miner", levelRequired, input);

        document.getElementById("reduction_time").innerText =
            "- " + baseReduction + "s " + "& - " + perWorkforce + "s each worker";

        let experienceInputField = <HTMLInputElement>document.getElementsByName("experience")[0];
        let locationInputField = <HTMLInputElement>document.getElementsByName("location")[0];

        experienceInputField.value = matchedType.experience + "";
        locationInputField.value = jsUcfirst(matchedType.location);

        let timeInputField = <HTMLInputElement>document.getElementsByName("time")[0];
        timeInputField.value = matchedType.time + "";

        let selectedFigure = document.getElementById("selected_item");
        if (selectedFigure.children.length == 0) {
            selectedFigure.appendChild(clone);
        } else {
            let img = <HTMLImageElement>selectedFigure.children[0];
            if (Game.getProperty("building") === "mine") {
                item = item + " ore";
            }
            img.src = "public/images/" + item + ".png";
            img.alt = item;
            img.style.border = "";
        }
    }
    public fetchData(site: 'crops' | 'mine') {
        let url = site === 'crops' ? '/crops/data' : '/mine/data';

        AdvApi.get<GetSkillActionDataRequest>(url).then((response) => {
            this.workforceData = response.data.workforce_data;
            this.typeData = response.data.crops;
            if (Game.getProperty("building") === "crops") {
                console.log(this);
                // for (let i = 0; i < response.data.crops.length; i++) {
                //     this.typeData[response.data.crops[i].crop_type] = response.data.crops[i];
                // }
            } else {
                // for (let i = 0; i < response.data.minerals.length; i++) {
                //     this.typeData[response.data.minerals[i]["mineral_type"]] = response["mineral"][i];
                // }
            }
            document
                .getElementById("workforce_amount")
                .setAttribute("max", response.data.workforce_data.avail_workforce + "");
        });
    }


    public startCountdownAndUpdateUI({ actionText, noActionText, endTime, type }: SkillActionCountdownData) {

        this.intervalID = setInterval(() => {
            let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);

            if (document.getElementById("time") == null) {

                clearInterval(this.intervalID);
            } else if (remainder < 0 && type) {

                clearInterval(this.intervalID);
                this.cancelActionButton.style.display = "none";
                this.finishActionButton.style.display = "inline-block";
                document.getElementById("time").innerHTML = "";
            } else if (remainder < 0) {

                clearInterval(this.intervalID);
                this.cancelActionButton.style.display = "none";
                this.finishActionButton.style.display = "none";
                this.infoActionElement.innerHTML = actionText;
                document.getElementById("time").innerHTML = "";
            } else {

                document.getElementById("cancel_action").style.display = "inline-block";
                this.finishActionButton.style.display = "none";
                this.infoActionElement.innerHTML = noActionText;
                document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
            }
        }, 1000);
    }
};

export interface SkillActionCountdownData {
    actionText: string,
    noActionText: string,
    endTime: number,
    type: string
}
