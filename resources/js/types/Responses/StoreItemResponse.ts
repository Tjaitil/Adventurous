import type { StoreItemResource } from '../StoreItemResource';
import type { advAPIResponse } from './AdvResponse';
export interface StoreItemResponse extends advAPIResponse {
  data: {
    store_items: StoreItemResource[];
  };
}
