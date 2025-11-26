interface Farmer {
  id: number;
  username: string;
  workforce?: Workforce[];
}

interface Miner {
  id: number;
  username: string;
  workforce?: Workforce[];
}

interface Trader {
  id: number;
  username: string;
  traderAssignment?: TraderAssignment;
  cart?: Cart;
}

interface Workforce {
  id: number;
}

interface TraderAssignment {
  id: number;
}

interface Cart {
  id: number;
}

interface WarriorStatuses {
  statuses: {
    finished_training: number;
    training: number;
    on_mission: number;
    idle: number;
    resting: number;
  };
}

interface ProficiencyStatuses {
  Farmers: Farmer[] | null;
  Trader: Trader | null;
  Miners: Miner[] | null;
  warrior_statuses: WarriorStatuses;
}

export type { ProficiencyStatuses, Farmer, Miner, Trader, WarriorStatuses };
