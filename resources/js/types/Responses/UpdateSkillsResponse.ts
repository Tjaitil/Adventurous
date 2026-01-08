import type { LevelUpSkill } from '../Skill';
import type { UserLevels } from '../UserLevels';

export interface UpdateSkillsResponse {
  new_levels: LevelUpSkill[];
  user_levels: UserLevels;
}
