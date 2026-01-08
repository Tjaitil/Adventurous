import type { advAPIResponse } from './AdvResponse';

export interface ChangeArmorResponse extends advAPIResponse {
  html: {
    warrior_armory: string;
  };
}
