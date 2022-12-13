import { Game } from "./advclient.js";
import { ajaxJS } from "./ajax.js";
import { clientOverlayInterface } from "./clientScripts/clientOverlayInterface.js";
import { jsUcfirst } from "./utilities/uppercase.js";

const skillContainer = {
    typeData: {},
    workforceData: {},
    workforceElement: null as HTMLInputElement,
    site: null,
    getWorkforceAmount(): number {
        if (!this.workforceElement) {
            return 0;
        }
        return parseInt(this.workforceElement.value);
    },
    getSelectedType(): string {
        if (this.site === 'crops') {
            let crop_type = <HTMLInputElement>document.getElementById("selected_crop_type");
            return crop_type.value;
        } else if (this.site === 'mine') {
            return "";
        }
    },
    setAvailableWorkforce(amount: number) {
        let available_workforce_input = document.getElementById("data_container_avail_workforce");
        available_workforce_input.innerText = '(' + amount + ')';
    },
    showSelect(event) {
        let targetElement = event.currentTarget;
        let clone = targetElement.cloneNode(true);
        clone.removeAttribute("onclick");
        let item = targetElement.getAttribute("alt");
        let className;
        if (clientOverlayInterface.getInterfacePageTitle() === "crops") {
            className = "crop_type";
        } else {
            className = "mineral";
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
        div_inputs[0].value = jsUcfirst(item);
        let timeInputField = <HTMLInputElement>document.getElementsByName("time")[0];
        timeInputField.value = this.typeData[item].time;

        let baseReduction;
        let perWorkforce;
        let imgSrc;
        let level;

        const checkSkillLevel = (skillLevel, level) => {
            console.log(skillLevel, level);
            let input = document.getElementsByName("level")[0];
            if (parseInt(level) > parseInt(skillLevel)) {
                input.classList.add("not-able-color");
            } else {
                input.classList.remove("not-able-color");
            }
        };

        const levelInputField = <HTMLInputElement>document.getElementsByName("level")[0];

        // Check wether or not the player are in crops or mine
        if (Game.properties.building === "crops") {
            let seedInputField = <HTMLInputElement>document.getElementsByName("seeds")[0];
            seedInputField.value = this.typeData[item].seed_required;

            levelInputField.value = this.typeData[item].farmer_level;

            imgSrc = this.typeData[item].crop_type + ".png";
            // Calculate reduction times
            baseReduction = Number(
                parseInt(this.typeData[item].time) * (this.workforceData["efficiency_level"] * 0.01)
            ).toFixed(2);
            perWorkforce = Number(parseInt(this.typeData[item].time) * 0.005).toFixed(2);
            // Retrieve skill level
            level = document.querySelectorAll(".skill_level")[0].innerHTML.trim();
            checkSkillLevel(level, this.typeData[item].farmer_level);
        } else {
            let permitsInputField = <HTMLInputElement>document.getElementsByName("permits")[0];
            permitsInputField.value = this.typeData[item].permit_cost;

            levelInputField.value = this.typeData[item].miner_level;
            imgSrc = this.typeData[item].mineral_type + ".png";
            // Calculate reduction times
            baseReduction = Number(
                parseInt(this.typeData[item].time) * (this.workforceData["efficiency_level"] * 0.01)
            ).toFixed(2);
            perWorkforce = Number(parseInt(this.typeData[item].time) * 0.005).toFixed(2);
            // Retrieve skill level
            level = document.querySelectorAll(".skill_level")[1].innerHTML.trim();
            checkSkillLevel(level, this.typeData[item].miner_level);
        }

        document.getElementById("reduction_time").innerText =
            "- " + baseReduction + "s " + "& - " + perWorkforce + "s each worker";

        let experienceInputField = <HTMLInputElement>document.getElementsByName("experience")[0];
        let locationInputField = <HTMLInputElement>document.getElementsByName("location")[0];

        experienceInputField.value = this.typeData[item].experience;
        locationInputField.value = jsUcfirst(this.typeData[item].location);

        let selectedFigure = document.getElementById("selected_item");
        if (selectedFigure.children.length == 0) {
            selectedFigure.appendChild(clone);
        } else {
            let img = <HTMLImageElement>selectedFigure.children[0];
            if (Game.properties.building === "mine") {
                item = item + " ore";
            }
            img.src = "public/images/" + item + ".png";
            img.alt = item;
            img.style.border = "";
        }
    },
    fetchData(site: 'crops' | 'mine') {
        let data;
        if (Game.properties.building === "crops") {
            data = "model=Crops" + "&method=getData";
        } else {
            data = "model=Mine" + "&method=getData";
        }
        ajaxJS(data, (response) => {
            let responseText = response[1].data;
            this.workforceData = responseText.workforce_data;
            if (Game.properties.building === "crops") {
                for (let i = 0; i < responseText["crop_types"].length; i++) {
                    this.typeData[responseText["crop_types"][i]["crop_type"]] = responseText["crop_types"][i];
                }
            } else {
                responseText["mineral_types"];
                for (let i = 0; i < responseText["mineral_types"].length; i++) {
                    this.typeData[responseText["mineral_types"][i]["mineral_type"]] = responseText["mineral_types"][i];
                }
            }
            document
                .getElementById("workforce_amount")
                .setAttribute("max", responseText.workforce_data.avail_workforce);
        });
    },
};
export default skillContainer;
