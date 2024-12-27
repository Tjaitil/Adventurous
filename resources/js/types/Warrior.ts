export interface Warrior {
    id: number;
    username: string;
    warrior_id: number;
    type: string;
    training_countdown: string | null;
    is_training: boolean;
    training_type: string | null;
    army_mission: number;
    health: number;
    location: string;
    is_resting: boolean;
    rest_start: string | null;
}

export interface Armory {
    id: number;
    username: string;
    warrior_id: number;
    helm: string | null;
    ammunition: string | null;
    ammunition_amount: number;
    body: string | null;
    right_hand: string | null;
    left_hand: string | null;
    legs: string | null;
    boots: string | null;
    attack: number;
    defence: number;
}

export interface ArmoryWarrior
    extends Pick<Warrior, 'type' | 'warrior_id' | 'id'> {
    armory: Armory;
}

export interface ArmoryUser {
    armory: Armory;
}

export type ItemParts = keyof Omit<
    ArmoryWarrior['armory'],
    'warrior_id' | 'username' | 'id' | 'ammunition_amount'
>;
