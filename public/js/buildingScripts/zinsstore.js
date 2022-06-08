import storeContainer from "../utilities/storeContainer.js";

const zinsStoreModule = {
    init() {
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.trade, 'Sell');
    },
    trade() {
        let { item, amount } = storeContainer.getSelectedTrade() || {};
        if(!item) return;

        ajaxP("model=ZinsStore" + "&method=tradeItem" + "&item=" + item + "&amount=" + amount, 
            response => {
            if(response[0] !== false) {
                updateInventory();
            }
        });
    },
}
export default zinsStoreModule;