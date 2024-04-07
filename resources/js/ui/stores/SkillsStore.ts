import { UserLevels } from '@/types/UserLevelsResource';
import { defineStore } from 'pinia';

interface State {
    handleXpGainedEvent: boolean;
    UserLevelsResource?: UserLevels;
}

export const useSkillsStore = defineStore('skillsStore', {
    state: (): State => ({
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
            return this.UserLevelsResource?.miner_level > levelRequired;
        },
        hasRequiredFarmerLevel(levelRequired: number) {
            return this.UserLevelsResource?.farmer_level > levelRequired;
        },
        hasRequiredAdventurerRespect(levelRequired: number) {
            return this.UserLevelsResource?.adventurer_respect > levelRequired;
        },
        hasRequiredTraderLevel(levelRequired: number) {
            return this.UserLevelsResource?.trader_level > levelRequired;
        },
        hasRequiredWarriorLevel(levelRequired: number) {
            return this.UserLevelsResource?.warrior_level > levelRequired;
        },
    },
});
