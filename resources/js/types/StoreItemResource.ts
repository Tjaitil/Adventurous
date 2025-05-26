import { SkillRequirementResource } from './SkillRequirementResource';

export interface StoreItemResource {
  name: string;
  amount: number;
  store_value: number;
  store_buy_price: number;
  required_items: StoreItemResource[];
  item_multiplier: number;
  adjusted_store_value: number;
  adjusted_difference: number;
  skill_requirements: SkillRequirementResource[];
  information: string;
}
