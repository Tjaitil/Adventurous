interface Farmer {
  id: number;
  username: string;
  location: string;
  crop_type: string | null;
  crop_finishes_at: string | null;
  workforce?: Workforce[];
}

interface Miner {
  id: number;
  username: string;
  location: string;
  mineral_ore: string | null;
  mining_finishes_at: string | null;
  workforce?: Workforce[];
}

interface Trader {
  id: number;
  username: string;
  assignment_id: number;
  delivered: number;
  cart_amount: number;
  trader_assignment: TraderAssignment | null;
  cart: Cart;
}

interface Workforce {
  id: number;
}

interface TraderAssignment {
  id: number;
  cargo: string;
  base: string;
  destination: string;
  assignment_amount: number;
  assignment_type: string;
}

interface Cart {
  id: number;
  capasity: number;
}

interface WarriorStatuses {
  statuses: {
    finished_training: number;
    training: number;
    on_mission: number;
    idle: number;
    resting: number;
  };
  army_mission?: {
    current_army_missions: Array<{
      id: number;
      countdown: string;
    }>;
  };
}

interface ProficiencyStatuses {
  farmers: Farmer[] | null;
  trader: Trader;
  miners: Miner[] | null;
  warrior_statuses: WarriorStatuses;
}

export type { ProficiencyStatuses, Farmer, Miner, Trader, WarriorStatuses };
