export interface LevelUpSkill {
  skill: LevelUpAbleSkills;
  new_level: number;
}

export type LevelUpAbleSkills = 'farmer' | 'miner' | 'trader' | 'warrior';
export type SkillTypes =
  | 'farmer'
  | 'miner'
  | 'trader'
  | 'warrior'
  | 'adventurer';
