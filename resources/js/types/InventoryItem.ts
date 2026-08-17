export interface InventoryItem {
  id: number;
  item: string;
  amount: number;
}

export interface InventoryDataResponse {
  data: {
    items: InventoryItem[];
  };
  meta: {
    max_slots: number;
  };
}

/**
 * Shape of the `Inventory` broadcast payload — unwrapped (no `data` key)
 * since it comes from InventoryResourceCollection::resolve(), not toResponse().
 */
export interface InventoryBroadcastPayload {
  items: InventoryItem[];
  meta: {
    max_slots: number;
  };
}
