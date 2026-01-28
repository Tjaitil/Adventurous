import type { Warrior } from './Warrior';

export interface WarriorArmory {
  warrior_id: number;
  attack: number;
  defence: number;
  helm: string | null;
  ammunition: string | null;
  ammunition_amount: number;
  right_hand: string | null;
  body: string | null;
  left_hand: string | null;
  legs: string | null;
  boots: string | null;
}

export interface MinimalWarriorWithArmory
  extends Pick<Warrior, 'type' | 'warrior_id' | 'id'> {
  armory: WarriorArmory;
}

export type ArmoryPartsToRender = Omit<
  WarriorArmory,
  'warrior_id' | 'attack' | 'defence' | 'ammunition_amount' | 'id' | 'username'
>;

export type ArmoryPartsKeysToRender = keyof ArmoryPartsToRender;

export type ArmoryPartsToRenderValue = WarriorArmory[ArmoryPartsKeysToRender];

export interface ArmoryUser {
  armory: WarriorArmory;
}

export type ItemParts = keyof Omit<
  MinimalWarriorWithArmory['armory'],
  'warrior_id' | 'username' | 'id' | 'ammunition_amount'
>;
