const smithyModule = {
    init() {
        document.getElementById("smith").querySelectorAll("button").forEach(element => 
            element.addEventListener('click', this.smith)
        );

        [...document.getElementsByClassName("minerals")].forEach(element => {
            element.addEventListener('click', () => {
                if(!element.getAttribute("title")) return;
                this.showMineral(element.getAttribute("title"));
            });
        });
    },
    showMineral(mineral) {
        if(!mineral) return;
        [...document.getElementsByClassName("minerals")].forEach(element => {
            if(element.getAttribute("title") == mineral) {
                document.getElementById(element.getAttribute("title")).style.display = "inline-block";
                element.style = "border: 2px solid brown";
            } else {
                if(!document.getElementById(element.getAttribute("title"))) return false;
                document.getElementById(element.getAttribute("title")).style = "display: none";
                element.style = "border: none"; 
            }
        })
        newsContentSidebar.activeButton = "smith";
        newsContentSidebar.adjustMainContentHeight();
    },
    smith(event) {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let amount = event.target.parentElement.children[0].value;
        let item = event.target.closest("tr").querySelectorAll("figcaption")[0].innerHTML.toLowerCase();
        let minerals = document.getElementsByClassName("minerals");
        for(let i = 0; i < minerals.length; i++) {
            if(minerals[i].style.borderStyle == "solid") {
                this.mineral = minerals[i].getAttribute("title");
                break;
            }
        } 
        if(this.mineral.length < 1){
            gameLogger.addMessage("Please select a mineral", true);
            return false;
        }
        event.target.parentElement.children[0].value = "";
        let data = "model=Smithy" + "&method=smith" + "&item=" + item  + "&amount=" + amount + "&mineral=" + this.mineral;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory();
            }       
        });
    },
    onClose() {
        return;
    },
}
export default smithyModule;