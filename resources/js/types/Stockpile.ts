export interface StockpileItemResource {
  item: string;
  amount: number;
}

export interface StockpileDataResponse {
  data: {
    items: StockpileItemResource[];
  };
  meta: {
    max_slots: number;
  };
}
