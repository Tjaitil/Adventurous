import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface';
import { AdvApi } from './../AdvApi';
import storeContainer from "../utilities/storeContainer";
import { Inventory } from '../clientScripts/inventory';
import { StoreItemResponse } from '../types/Responses/StoreItemResponse';
import { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';

const smithyModule = {
    init() {
        this.getData();
        storeContainer.init();
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.smith, "Smith");
    },
    data: null,
    getData() {
        AdvApi.get<StoreItemResponse>('/smithy').then((response) =>
            storeContainer.setStoreItems(response.data.store_items)
        ).catch(() => false);
    },
    smith() {
        let result = storeContainer.getSelectedTrade();
        if (!result) return;

        let { item, amount } = result;

        let data: BaseBuyStoreItemRequest = {
            item,
            amount,
        }

        AdvApi.post('/smithy/smith', data).then((response) =>
            Inventory.update()
        ).catch(() => false);
    },
    showMineral(mineral) {
        if (!mineral) return;
        let arr = <HTMLElement[]>[...document.getElementsByClassName("minerals")];

        arr.forEach((element) => {
            if (element.getAttribute("title") == mineral) {
                document.getElementById(
                    element.getAttribute("title")
                ).style.display = "inline-block";
                element.classList.add("container_selected_item");
            } else {
                if (!document.getElementById(element.getAttribute("title")))
                    return false;
                document.getElementById(element.getAttribute("title")).style.display = "none";
                element.classList.remove("container_selected_item");
            }
        });

        ClientOverlayInterface.adjustWrapperHeight();
    },
    onClose() {
        storeContainer.checkItemTooltip();
    },
};
export default smithyModule;
