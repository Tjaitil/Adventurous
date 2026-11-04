import type { advAPIResponse } from '@/types/Responses/AdvResponse';
import type { SkillTypes } from '@/types/Skill';

export type WorkforceSkill = Extract<SkillTypes, 'farmer' | 'miner'>;

export interface WorkforceLocation {
  name: string;
  amount: number;
}

export interface WorkforceData {
  skill: WorkforceSkill;
  efficiency_level: number;
  efficiency_upgrade_price: number;
  max_efficiency_level: number;
  workforce_total: number;
  avail_workforce: number;
  max_workers: number;
  can_recruit: number;
  locations: WorkforceLocation[];
}

export interface WorkforceLodgeDataResponse {
  data: Record<WorkforceSkill, WorkforceData>;
}

export interface UpgradeEfficiencyRequest {
  skill: WorkforceSkill;
}

export type UpgradeEfficiencyResponse = advAPIResponse<{
  efficiency_level: number;
  new_efficiency_price: number;
}>;
