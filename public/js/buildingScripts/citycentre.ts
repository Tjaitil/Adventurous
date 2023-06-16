import { UpgradeEfficiencyResponse } from './../types/responses/CityCentreResponses';
import { UpgradeEfficiencyRequest } from './../types/requests/CityCentreRequests';
import { Inventory } from '../clientScripts/inventory';
import { AdvApi } from './../AdvApi';
import { gameLogger } from '../utilities/gameLogger';
import { BuyPermitsRequest } from '../types/requests/MineRequests';
import { BuyPermitsResponse } from '../types/responses/MineResponses';
const cityCentreModule = {
    init() {
        // let keep_buttons = document.getElementById("keep").querySelectorAll("button");
        // keep_buttons[0].addEventListener("click", this.changeArtefact);
        // keep_buttons[1].addEventListener("click", this.newArtefact);
        // document.getElementById("miner-permits-buy-permits").addEventListener("click", this.buyPermits);
        // [...document.getElementsByClassName("upgrade-efficiency-button")].forEach(element => {
        //     element.addEventListener("click", this.upgradeEffiency);
        // });
        // let unlock_items_button = document.getElementById("unlock_items").querySelectorAll("button");
        // unlock_items_button.forEach(element => {
        //     element.addEventListener('click', this.unlockArmorItems);
        // });
    },
    unlockArmorItems() {
        // let button = event.target;
        // let item = button.closest("tr").children[0].innerText.toLowerCase().trim();
        // if (["frajrite items", "wujkin items"].indexOf(item) == -1) {
        //     gameLogger.addMessage("This item cannot be unlocked", true);
        //     return false;
        // }
        // let data = "model=ArmorItems" + "&method=unlockArmorItems" + "&type=" + item;
        // ajaxP(data, function (response) {
        //     if (response[0] != false) {
        //         updateInventory();
        //         button.className = "button_disabled";
        //         button.innerText = "Unlocked";
        //     }
        // });
    },
    changeProfiency() {
        // let select = document.getElementById("profiency_select");
        // let val = select.value;
        // let profiencySelectConfirm = document.getElementsByName("city-centre-change-profiency")[0];
        // if (!profiencySelectConfirm) {
        //     return false;
        // } else if (!profiencySelectConfirm.value) {
        //     gameLogger.addMessage("Checkbox below information about profiency change must be toggled on", true);
        //     return false;
        // } else if (!val) {
        //     gameLogger.addMessage("Please select a profiency!", true);
        //     return false;
        // }
        /*var data = "model=Profiency" + "&method=changeProfiency" + "&newProfiency=" + val;
        ajaxP(data, function(response) {
            if(response[0] !== false) {

            }       
        });*/
    },
    changeArtefact() {
        // let itemData = selectedCheck(false);
        // if (itemData.length === false) {
        //     return false;
        // }
        // let artefacts = ["harvester", "prospector", "collector", "healer", "rewardist", "fighter"];
        // if (artefacts.indexOf(itemData[0]) == -1) {
        //     gameLogger.addMessage("That is not an artefact");
        //     gameLogger.logMessages();
        // }
        // else {
        //     var data = "model=Artefact" + "&method=changeArtefact" + "&artefact=" + itemData[0];
        //     ajaxP(data, function (response) {
        //         if (response[0] !== false) {
        //             var data = response[1].split("|");
        //             var artefactDiv = document.getElementById("artefact");
        //             artefact = data[0].split("|")[0].trim();
        //             artefactDiv.children[0].src = "public/images/" + data[0] + '.jpg';
        //             artefactDiv.querySelectorAll("p")[0].innerHTML = "Current Artefact:" + jsUcfirst(data[0]);
        //             updateInventory();
        //         }
        //     });
        // }
    },
    buyPermits() {
        let amount = 50;
        // let location = document.querySelector('input[name="permit_location"]:checked').value;
        let location = null;
        if (!location) {
            gameLogger.addMessage("ERROR Select a location to buy permits", true);
            return false;
        }

        let data: BuyPermitsRequest = {
            location
        };

        AdvApi.post<BuyPermitsResponse>('mine/permits/buy', {}).then((response) => {
            let spans = document.getElementById("miner_permits").querySelectorAll("span");
            if (location === "golbak") {
                spans[0].innerText = response.data.new_permits + "";
            } else {
                spans[1].innerText = response.data.new_permits + "";
            }
            Inventory.update();
        });
    },
    newArtefact() {
        gameLogger.addMessage("Currently disabled", true);
        // let data = "model=Artefact" + "&method=newArtefact";
        // ajaxP(data, function(response) {
        //     if(response[0] !== false) {
        //         var responseText = response[1].split("|");
        //         openNews('Waiting');
        //         setTimeout( function() {
        //             document.getElementById("news_content").innerText = "";
        //             var img = document.createElement("IMG");
        //             img.href = "/public/img/" + responseText[1] + ".png";
        //             img.style = "width: 50px; height: 50px; margin-left: 10px";
        //             openNews(responseText[0] + responseText[1]);
        //             openNews(img);
        //         }, 3000);
        //     }
        // });
    },
    setArtefact() {
        gameLogger.addMessage("Currently disabled", true);
        // let data = "model=Artefact" + "&method=setArtefact";
        // ajaxP(data, function(response) {
        //     if(response[0] !== false) {

        //     }
        // });
    },

    upgradeEffiency(skill: string) {
        const data: UpgradeEfficiencyRequest = {
            skill
        };

        AdvApi.post<UpgradeEfficiencyResponse>('/citycentre/incrementEfficiency', data).then((response) => {
            Inventory.update();

            // tr.children[1].innerHTML = response.data.efficiencyLevel;
            // tr.children[2].childNodes[0] = response.data.newEfficiencyPrice;
        });

    }
};
export default cityCentreModule;