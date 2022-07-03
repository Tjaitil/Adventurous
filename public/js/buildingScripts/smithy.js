import storeContainer from '../utilities/storeContainer.js';

const smithyModule = {
    init() {
        this.getData();
        storeContainer.addSelectTrade();
        storeContainer.addRequirementEvent(event => this.getRequiredItem(event));
        storeContainer.addSelectedItemButtonEvent(this.smith, 'Smith');
    },
    data: null,
    getData() {
        ajaxJS("model=Smithy" + "&method=getData", response => {
            if(response[0] != false) {
                this.data = response[1].data;
            }
        });
    },
    getRequiredItem(event) {
        let elementDiv = event.currentTarget.closest(".store-container-item");
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.toLowerCase().trim();
        let itemData = this.data.find(element => element.item === item);
        if(itemData && itemData.required.length > 0) {
            storeContainer.clearRequirementContainer();
            itemData.required.forEach(element => 
                storeContainer.addRequirement(element.required_item, element.required_amount, element.required_item)
            );
            storeContainer.checkSetAmount(itemData);
        }
        document.getElementById("store-container-item-information").innerHTML = "Required mineral level " + itemData.level;

    },
    smith(event) {
        let { item, amount } = storeContainer.getSelectedTrade() || {};
        if(!item) return;

        let data = "model=Smithy" + "&method=smith" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, response => {
            if(response[0] != false) {
                updateInventory();
            }
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
    onClose() {
        storeContainer.checkItemTooltip();
    },
}
export default smithyModule;