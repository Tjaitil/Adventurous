import selectContainer from '../selectContainer.js';
import countdown from '../utilities/countdown.js';

const mineModule = {
    intervalID: null,
    init() {
        this.getCountdown();
        [...document.getElementsByClassName("mineral_type")].forEach(element => 
                element.addEventListener('click', (event) => selectContainer.showSelect(event))
        );
        document.getElementById("cancel_action").addEventListener("click", () => this.cancelMining());
        document.getElementById("fetch_minerals_action").addEventListener("click", () => this.updateMine());
        document.getElementById("data_container").querySelectorAll("button")[0].addEventListener("click", 
            () => this.setMine());
        selectContainer.fetchData();
    },
    getCountdown() {
        document.getElementById("mining").innerHTML = "No miners at work";
        let data = "&model=Mine" + "&method=checkCountdown";
        ajaxG(data, response => {
            if(response[0] != false) {
                let responseText = response[1];
                let endTime = responseText.data * 1000;
                let fetch = responseText.fetch_minerals;
                if(responseText.mining_type !== 'none') {
                    document.getElementById("mining").innerHTML = "Currently mining " + responseText.mining_type;    
                }
                this.intervalID = setInterval(() => {
                    let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                    if(document.getElementById("time") == null) {
                        clearInterval(this.intervalID);
                    }
                    else {
                        document.getElementById("cancel_action").style.display = "inline-block";
                        document.getElementById("fetch_minerals_action").style.display = "none";
                        document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
                    }
                    // Check if countdown is finished and fetch is true
                    if (remainder < 0 && fetch === "1"){
                        clearInterval(this.intervalID);
                        document.getElementById("cancel_action").style.display = "none";
                        document.getElementById("fetch_minerals_action").style.display = "inline-block";
                        document.getElementById("time").innerHTML = "";
                    }
                    else if (remainder < 0) {
                        clearInterval(this.intervalID);
                        document.getElementById("cancel_action").style.display = "none";
                        document.getElementById("fetch_minerals_action").style.display = "none";
                        document.getElementById("mining").innerHTML = "No miners at work";
                        document.getElementById("time").innerHTML = "";
                    }
                }, 1000);
                newsContentSidebar.adjustMainContentHeight();
            }
        });
    },
    setMine() {
        let mineral = document.getElementById("selected_mineral_type").value;
        let workforce = document.getElementById("workforce_amount").value;
        if(workforce === 0 || workforce.length === 0) {
            gameLogger.addMessage("You need to select the amount of workers", true);
            return false;
        } else if(mineral.length === 0) {
            gameLogger.addMessage("You need to select at least one mineral", true);
            return false;
        }
        let data = "model=SetMine" + "&method=setMine" + "&mineral=" + mineral + "&workforce=" + workforce;
        ajaxP(data, response => {
            if(response[0] !== false) {
                let responseText = response[1];
                updateHunger(responseText.newHunger);
                this.getCountdown();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = 
                '(' + responseText.availWorkforce + ')';
                document.getElementById("data_container").querySelectorAll("p")[0].innerHTML = 
                    "Total permits:" + responseText.permits;
            }
        });
    },
    updateMine() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let data = "model=UpdateMine" + "&method=updateMine";
        ajaxP(data, response => {
            if(response[0] !== false) {
                let responseText = response[1];
                this.getCountdown();
                updateInventory();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
            }       
        });
    },
    cancelMining() {
        let data = "model=Mine" + "&method=cancelMining";
        ajaxP(data, response => {
            if(response[0] !== false) {
                // Clear interval started by getCountdown
                let responseText = response[1];
                clearInterval(this.intervalID);
                updateCountdownTab();
                this.getCountdown();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
            }       
        });
    },
    onClose() {

    }
}
export default mineModule;