import { UserLevelsResource } from './types/UserLevelsResource';
import { AdvApi } from './AdvApi';
import { LevelUpSkill } from './types/LevelUpSkill';
import { gameLogger } from './utilities/gameLogger';
import { jsUcfirst } from './utilities/uppercase';

export const LevelManager = {
    skillElement: null,
    skillData: <UserLevelsResource>{},
    highLightIndex: 0,
    elementIndexes: {
        adventurer: 0,
        farmer: 1,
        miner: 2,
        trader: 3,
        warrior: 4,
    },
    getMinerLevel() {
        return this.skillData.miner_level
    },
    getFarmerlevel() {
        return this.skillData.farmer_level
    },
    getAdventurerLevel() {
        return this.skillData.adventurer_respect
    },
    getTraderLevel() {
        return this.skillData.trader_level
    },
    getWarriorLevel() {
        return this.skillData.warrior_level
    },
    get() {
        AdvApi.get<UserLevelsResource>('/userlevels').then((response) => {
            this.skillData = response;
        });
    },
    update(levelData: LevelUpSkill[]) {
        if (!Object.keys(levelData)) return false;

        let index = this.elementIndexes[levelData["skill"]];
        let element = document.getElementById("skills").querySelectorAll(".skill_level")[index];
        element.innerHTML = levelData["new_level"];
        gameLogger.addMessage(
            "Congratulations! You have leveled up " + jsUcfirst(levelData["skill"]) + " to " + levelData["new_level"]
        );
        setInterval(() => this.skillHighlight(document.getElementById("skills").children[index]), 1000);
    },
    skillHighlight(element) {
        if (element.style.backgroundColor === "" || element.style.backgroundColor == "rgb(201, 155, 105)") {
            element.style.backgroundColor = "#f8f2ec";
        } else {
            element.style.backgroundColor = "#c99b69";
        }
    },
    showHasLevelRequired(skill: string, levelRequired: number, uiElement: HTMLElement) {
        let currentLevel = this.skillData[skill + "_level"];

        if (currentLevel > levelRequired) {
            uiElement.classList.add("not-able-color");
        } else {
            uiElement.classList.remove("not-able-color");
        }
    }
};
