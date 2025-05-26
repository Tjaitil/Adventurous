import { StoreItemResource } from '../StoreItemResource';
import { advAPIResponse } from './AdvResponse';
export interface StoreItemResponse extends advAPIResponse {
  data: {
    store_items: StoreItemResource[];
  };
}
