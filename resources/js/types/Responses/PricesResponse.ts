export interface ItemPriceResponse {
    name: string;
    store_value: number;
}

export interface ItemPricesResponse {
    prices: ItemPriceResponse[];
}
