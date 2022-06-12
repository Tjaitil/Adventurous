import storeContainer from '../utilities/storeContainer.js';

const archeryShopModule = {
    init() {
        this.getData();
        storeContainer.addSelectTrade();
        storeContainer.addRequirementEvent(event => this.getMaterials(event));
        storeContainer.addSelectedItemButtonEvent(this.fletch, 'fletch');
    },
    data: null,
    getData() {
        let data = "model=ArcheryShop" + "&method=getData";
        ajaxJS(data, response => {
            if(response[0] !== false) {
                this.data = response[1].data;
            }
        });
    },
    getMaterials(event) {
        let elementDiv = event.currentTarget.closest(".store-container-item");
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.toLowerCase().trim();
        let itemData = this.data.find(element => element.item === item);
        if(itemData && itemData.required.length > 0) {
            storeContainer.clearRequirementContainer();
            itemData.required.forEach(element => 
                storeContainer.addRequirement(element.material, element.required_amount, element.material)
            );
            if(itemData.setAmount) {
                let span = document.createElement("span")
                span.classList.add("item_amount");
                span.innerHTML = itemData.setAmount;
                span.style.visibility = "visible";
                console.log(span);
                document.getElementById("store-container-selected-trade").appendChild(span);
            }
        }
    },
    fletch() {
        let { item, amount } = storeContainer.getSelectedTrade() || {};
        if(!item) return;

        let data = "model=ArcheryShop" + "&method=fletch" + "&item=" + item  + "&amount=" + amount;
        ajaxP(data, response => {
            if(response[0] !== false) {
                updateInventory();
            }       
        });
    },
    onClose() {
        storeContainer.checkItemTooltip();
    }
};
export default archeryShopModule;