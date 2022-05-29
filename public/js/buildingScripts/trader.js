import countdown from '../utilities/countdown.js';

const traderModule = {
    selected: null,
    init() {
        document.getElementById("start_trader_assignment").addEventListener("click", () => this.newAssignment());
        [...document.getElementsByClassName("trader_assignment")].forEach(element =>
            element.addEventListener("click", event => traderModule.selectTrade(event))
        );
        this.getMerchantCountdown();
        // Check if active trader assignment
        if(document.getElementById("traderAssignment_progressBar")) {
            // Calculate progress
            progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);    
            document.getElementById("traderAssignment-pick-up").addEventListener("click", () => this.pickUp());
            document.getElementById("traderAssignment-deliver").addEventListener("click", () => this.deliver());
        }
    },
    getMerchantCountdown() {
        let data = "&model=Merchant" + "&method=getTraderAssigmentCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                console.log(response);
                let responseText = response[1];
                let endTime = (parseInt(responseText.traderAssigmentCountdown) + 14400) * 1000;
                let x = setInterval (function() {
                    let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                    if(document.getElementById("trader_assignments_countdown_time") == null) {
                        clearInterval(x);
                    } 
                    else if(remainder < 1) {
                        document.getElementById("trader_assignments_countdown_time").innerHTML = "Re enter building to get new trader assignments";
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("trader_assignments_countdown_time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";   
                    }
                }, 1000);
            }
        });
    },
    selectTrade(event) {
        let target = event.currentTarget;
        [...document.getElementsByClassName("trader_assignment")].forEach(element => {
            if(element === target && !element.classList.contains("trader_assignment_locked")) {
                element.classList.add("selected_trader_assignment");
                if(element.querySelectorAll(".trader_assignment_id")[0]) {
                    this.selected = element.querySelectorAll(".trader_assignment_id")[0].innerHTML.trim();
                }
            } else {
                element.classList.remove("selected_trader_assignment");
            }
        });
    }, 
    newAssignment() {
        if(document.getElementById("traderAssignment_progressBar")) {
            gameLogger.addMessage("You already have an assigment", true);
        }
        else if(this.selected === null || !this.selected) {
            gameLogger.addMessage("This assignment is locked", true);
            return false;
        }
        let data = "model=Trader" + "&method=newAssignment" + "&assignment_id=" + this.selected;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1];
                updateHunger(responseText.newHunger);
                updateCountdownTab();
                document.getElementById("traderAssignment_current").innerHTML = responseText['html'];
                progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);
            }
        });
    },
    pickUp() {
        let data = "model=Trader" + "&method=pickUp";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                let responseText = response[1];
                updateCountdownTab();
                document.getElementById("traderAssignment_cart_amount").innerHTML = responseText.cartAmount;
            }
        });
    },
    deliver() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let data = "model=Trader" + "&method=deliver";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                updateCountdownTab();
                if(responseText.assignment_finished == true) {
                    // Update trader assigment div with new assignment
                    document.getElementById("traderAssignment_current").innerHTML = responseText.html;
                    updateDiplomacyTab();
                    updateInventory();
                }
                else {
                    document.getElementById("traderAssignment_cart_amount").innerHTML = 0;
                    // Calculate progressBar
                    progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), 
                                                    responseText.delivered, false, true);
                }
            }
        });
    }
};
export default traderModule;