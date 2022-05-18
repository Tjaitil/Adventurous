const traderModule = {
    selected: null,
    init() {
        document.getElementById("start_trader_assignment").addEventListener("click", () => this.newAssignment());
        [...document.getElementsByClassName("trader_assignment")].forEach(element =>
            element.addEventListener("click", event => traderModule.selectTrade(event))
        );
        // Check if active trader assignment
        if(document.getElementById("traderAssignment_progressBar")) {
            // Calculate progress
            progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);    
            document.getElementById("traderAssignment-pick-up").addEventListener("click", () => this.pickUp());
            document.getElementById("traderAssignment-deliver").addEventListener("click", () => this.deliver());
        }
    },
    selectTrade(event) {
        let target = event.currentTarget;
        [...document.getElementsByClassName("trader_assignment")].forEach(element => {
            if(element === target) {
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
        if(this.selected === null || !this.selected) {
            gameLogger.addMessage("Unvalid assignment", true);
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