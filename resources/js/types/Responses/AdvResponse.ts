import type { GameLog } from '../GameLog';
import type { LevelUpSkill } from '../Skill';

export interface advAPIResponse<T = object> {
  logs: GameLog[];
  data: T;
  levelUp?: LevelUpSkill[];
  html?: {
    [key: string]: string;
  };
}
