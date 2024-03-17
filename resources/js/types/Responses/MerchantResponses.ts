import { advAPIResponse } from './AdvResponse';

export interface MerchantStoreResponse extends advAPIResponse {
    html: {
        store: string;
    };
}

export interface SingleItemResponse extends advAPIResponse {
    html: {
        storeItem: string;
    };
}

export interface ItemPriceResponse {
    data: {
        price: number;
    };
}
