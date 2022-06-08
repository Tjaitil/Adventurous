import selectContainer from '../selectContainer.js';
import countdown from '../utilities/countdown.js';

scriptLoader.loadScript(['jsonForm'], 'utility');
    
const cropsModule = {
    intervalID: null,
    init() {
        this.getCountdown();
        [...document.getElementsByClassName("crop_type")].forEach(element => 
            element.addEventListener('click', (event) => selectContainer.showSelect(event))
        );
        document.getElementById("data_container").querySelectorAll("button")[0].addEventListener("click", 
            () => this.grow());
        document.getElementById("seed_generator").children[3].addEventListener("click", 
            () => this.seedGenerator());
        document.getElementById("cancel_action").addEventListener("click", 
            () => this.destroyCrops());
        document.getElementById("harvest_action").addEventListener("click", 
            () => this.updateCrop());
        // selectitem.js
        selectItemEvent.addSelectEvent();
        selectContainer.fetchData();
    },
    getCountdown() {
        document.getElementById("growing").innerHTML = "No crops growing";
        let data = "model=Crops" + "&method=checkCountdown";
        ajaxJS(data, (response) => {
            if(response[0] != false) {
                let responseText = response[1];
                let endTime = responseText.date * 1000;
                let harvest = parseInt(responseText.harvest);
                this.intervalID = setInterval(() => {
                    let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                    
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("time").innerText = hours + "h " + minutes + "m " + seconds + "s ";
                        document.getElementById("cancel_action").style.display = "inline-block";
                        document.getElementById("harvest_action").style.display = "none";
                    }
                    // Check if countdown is finished and harvest is true
                    if(remainder < 0 && harvest === 1) {
                        clearInterval(this.intervalID);
                        document.getElementById("harvest_action").style.display = "inline-block";
                        document.getElementById("cancel_action").style.display = "none";
                        document.getElementById("time").innerText = "";
                        document.getElementById("growing").innerText = "Finished";
                    }
                    else if(remainder < 0) {
                        clearInterval(this.intervalID);
                        document.getElementById("growing").innerText = "No crops growing";
                        document.getElementById("time").innerText = "";
                        document.getElementById("cancel_action").style.display = "none";
                        document.getElementById("harvest_action").style.display = "none";
                    }
                }, 1000);
                setTimeout(() => newsContentSidebar.adjustMainContentHeight(), 1100);
            }
        });
    },
    grow() {
        let workforce = document.getElementById("workforce_amount").value;
        let crop = document.getElementById("selected_crop_type").value;

        if(workforce === 0 || workforce.length === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        } else if(crop.length === 0) {
            gameLogger.addMessage("You need to select the crop you are trying to grow", true);
            return false;
        }
        let JSON_data = JSON.stringify({
            workforce,
            crop
        });
        let data = "model=SetCrops" + "&method=setCrops" + "&JSON_data=" + JSON_data;
        ajaxP(data, response => {
            if(response[0] !== false) {
                let responseText = response[1];
                updateHunger(responseText.newHunger);
                this.getCountdown();
                updateInventory();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
            }
        });
    },
    updateCrop() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let data = "model=UpdateCrops" + "&method=updateCrops";
        ajaxP(data, response => {
            if(response[0] !== false) {
                let responseText = response[1];
                this.getCountdown();
                updateCountdownTab();
                updateInventory();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';

            }
        });
    },
    destroyCrops() {
        let data = "model=Crops" + "&method=destroyCrops";
        ajaxP(data, response => {
            if(response[0] !== false) {
                let responseText = response[1];
                clearInterval(this.intervalID);
                this.getCountdown();
                updateCountdownTab();
                updateInventory();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
            }
        });
    },
    img() {
        let img = document.getElementById("type_img");
        let select = document.getElementById("form_select");
        let name = select.children[select.selectedIndex].value;
        if(name.length < 1) {
            return;
        }
        img.style = "display:block";
        img.src = "public/images/" + name;
    },
    seedGenerator() {
        let itemData = selectedCheck();
        if(itemData === false) {
            return false;
        }
        let items = ["potato", "tomato", "corn", "carrots", "cabbages", "wheat", "sugar", "spices", "apples", "oranges", "watermelon"];
        if(!items.includes(itemData[0])) {
            gameLogger.addMessage("ERROR: Pick a valid item");
            gameLogger.logMessages();
            return false;
        }
        let data = "model=Crops" + "&method=getSeeds" + "&type=" + itemData[0] + "&amount=" + itemData[1];
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory();
                document.getElementById("selected_amount").value = "";
            }
        });
    }
};
export default cropsModule;