import type { UserLevels } from '@/types/UserLevels';
import { defineStore } from 'pinia';

interface State {
  handleXpGainedEvent: boolean;
  UserLevelsResource: UserLevels;
}

export const useSkillsStore = defineStore('skillsStore', {
  state: (): State => ({
    UserLevelsResource: {
      miner_level: 0,
      farmer_level: 0,
      adventurer_respect: 0,
      trader_level: 0,
      warrior_level: 0,
      username: '',
      warrior_xp: 0,
      farmer_xp: 0,
      miner_xp: 0,
      trader_xp: 0,
      farmer_next_level_xp: 0,
      miner_next_level_xp: 0,
      trader_next_level_xp: 0,
      warrior_next_level_xp: 0,
    },
    handleXpGainedEvent: false,
  }),
  actions: {
    setHandleXpGainedEvent(status: boolean) {
      this.handleXpGainedEvent = status;
    },
    setUserLevelsResource(resource: UserLevels) {
      this.UserLevelsResource = resource;
    },
    hasRequiredMinerLevel(levelRequired: number) {
      return this.UserLevelsResource.miner_level > levelRequired;
    },
    hasRequiredFarmerLevel(levelRequired: number) {
      return this.UserLevelsResource.farmer_level > levelRequired;
    },
    hasRequiredAdventurerRespect(levelRequired: number) {
      return this.UserLevelsResource.adventurer_respect > levelRequired;
    },
    hasRequiredTraderLevel(levelRequired: number) {
      return this.UserLevelsResource.trader_level > levelRequired;
    },
    hasRequiredWarriorLevel(levelRequired: number) {
      return this.UserLevelsResource.warrior_level > levelRequired;
    },
  },
});
