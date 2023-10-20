import { advAPIResponse } from "./AdvResponse.js"

export interface MerchantStoreResponse extends advAPIResponse {
    html: {
        store: string,
    }
}

export interface SingleItemResponse extends advAPIResponse {
    html: {
        storeItem: string,
    }
}

export interface ItemPriceResponse {
    data: {
        price: number,
    }
}
