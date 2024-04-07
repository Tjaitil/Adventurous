import { LevelUpSkill } from '../Skill';
import { UserLevels } from '../UserLevels';

export interface UpdateSkillsResponse {
    new_levels: LevelUpSkill[];
    user_levels: UserLevels;
}
