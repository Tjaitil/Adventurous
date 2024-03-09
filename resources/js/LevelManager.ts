import { SkillTypes } from './types/Skill';
import { useSkillsStore } from './ui/stores/SkillsStore';

export const LevelManager = {
    showHasLevelRequired(
        skill: SkillTypes,
        levelRequired: number,
        uiElement: HTMLElement,
    ) {
        const store = useSkillsStore();

        let hasRequiredLevel = false;
        switch (skill) {
            case 'farmer':
                hasRequiredLevel = store.hasRequiredFarmerLevel(levelRequired);
                break;

            case 'adventurer':
                hasRequiredLevel =
                    store.hasRequiredAdventurerRespect(levelRequired);
                break;
            case 'miner':
                hasRequiredLevel = store.hasRequiredMinerLevel(levelRequired);
                break;
            case 'trader':
                hasRequiredLevel = store.hasRequiredTraderLevel(levelRequired);
                break;
            case 'warrior':
                hasRequiredLevel = store.hasRequiredWarriorLevel(levelRequired);
                break;
            default:
                break;
        }
        if (hasRequiredLevel === false) {
            uiElement.classList.add('not-able-color');
        } else {
            uiElement.classList.remove('not-able-color');
        }
    },
};
