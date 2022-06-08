const tavernModule = {
    init() {
        document.getElementById("tavern-eat-button").addEventListener("click", () => this.eat());
        selectItemEvent.addSelectEvent();
        [... document.getElementsByClassName("tavern-worker-recrute")].forEach(element =>
            element.addEventListener("click", event => this.recruitWorker(event))    
        );
        let hungerBar = document.getElementById("hunger_progressBar");
        let tavernHungerBar = hungerBar.cloneNode(true);
        let eatDiv = document.getElementById("eat");
        eatDiv.insertBefore(tavernHungerBar, eatDiv.children[1]);
    },
    recruitWorker(event) {
        let element = event.target.closest(".tavern-worker");
        let type = element.querySelectorAll(".tavern-worker-type")[0].innerText.trim();
        if(!type) return false;
        let level = (element.querySelectorAll(".tavern-worker-level")[0]) ? 
            element.querySelectorAll("tavern-worker-level")[0].innerText.trim() : false;
        let data = "model=RecruitWorker" + "&method=recruitWorker" + "&type=" + type + "&level=" + level;
        ajaxP(data, response => {
           if(response[0] != false) {
                let container = event.target.closest(".tavern-worker");
                document.getElementById("tavern-workers-grid-container").removeChild(container);
           }
        });
    },
    getHealingAmount(item) {
        if(item.length == 0) return false;
        let data = "model=Tavern" + "&method=getHealingAmount" + "&item=" + item; 
        ajaxG(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                if(parseInt(responseText) === 0) {
                    document.getElementById("item_healing_amount").innerText = "No healing from this item";
                }
                else {
                    document.getElementById("item_healing_amount").innerText = "Healing per item " + responseText.heal;
                }
                newsContentSidebar.adjustMainContentHeight();
            }
        });
    },
    eat() {
        let item = document.getElementById("selected").querySelectorAll("figure")[0].children[1].innerHTML.toLowerCase();
        if(item.length == 0) {
            gameLogger.addMessage("ERROR: Select a item to eat!");
            gameLogger.logMessages();
            return false;
        }
        let amount = document.getElementById("healing-item-amount").value;
        if(amount == 0 || amount == null) {
            gameLogger.addMessage("ERROR: Select a amount");
            gameLogger.logMessages();
            return false;
        }
        let data = "model=Hunger" + "&method=eat" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                let hungerBarContainers = document.querySelectorAll("#hunger_progressBar");
                hungerBarContainers.forEach(element => 
                    progressBar.calculateProgress(element, responseText.newHunger, false, true)
                );
                updateInventory();
                document.getElementById("selected").innerHTML = "";
                document.getElementById("healing-item-amount").value = "";
            }
        });
    }
}
export default tavernModule;