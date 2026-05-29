import type { StoreItemResource } from '../StoreItemResource';
import type { advAPIResponse } from './AdvResponse';
export interface StoreItemResponse extends advAPIResponse {
  data: {
    store_items: StoreItemResource[];
    store_value_modifier_as_percentage?: number;
    is_discount_active?: boolean;
  };
}
