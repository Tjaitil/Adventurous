import { gameLogger } from './utilities/gameLogger.js';
import { jsUcfirst } from './utilities/uppercase.js';
export const newLevel = {
    skillElement: null,
    skillData: null,
    highLightIndex: 0,
    elementIndexes: {
        adventurer: 0,
        farmer: 1,
        miner: 2,
        trader: 3,
        warrior: 4,
    },
    update(levelData) {
        if (!Object.keys(levelData))
            return false;
        let index = this.elementIndexes[levelData["skill"]];
        let element = document.getElementById("skills").querySelectorAll(".skill_level")[index];
        element.innerHTML = levelData["new_level"];
        gameLogger.addMessage("Congratulations! You have leveled up " + jsUcfirst(levelData["skill"]) + " to " + levelData["new_level"]);
        setInterval(() => this.skillHighlight(document.getElementById("skills").children[index]), 1000);
    },
    skillHighlight(element) {
        if (element.style.backgroundColor === "" || element.style.backgroundColor == "rgb(201, 155, 105)") {
            element.style.backgroundColor = "#f8f2ec";
        }
        else {
            element.style.backgroundColor = "#c99b69";
        }
    },
};
