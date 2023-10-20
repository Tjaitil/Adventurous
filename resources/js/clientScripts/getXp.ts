
window.addEventListener("load", () => {
    let wrappers = <HTMLElement[]>[...document.getElementsByClassName("skill-level-wrapper")];
    wrappers.forEach((element) =>
        element.addEventListener("click", () => getSkillXp(element.dataset.wrapperSkill))
    );
});

export function getSkillXp(skill: string) {
    const skillDivs = {
        adventurer: 0,
        farmer: 1,
        miner: 2,
        trader: 3,
        warrior: 4,
    };

    let selectedSkillIndex = skillDivs[skill];
    if (!selectedSkillIndex) return false;

    let selectedSkillDiv = document.getElementsByClassName("skill-level-wrapper")[selectedSkillIndex];
    if (!selectedSkillDiv) return false;

    let tooltips = <HTMLElement[]>[...document.getElementById("skill-levels-container").querySelectorAll(".skill_tooltip")];

    let currentTooltip = tooltips[selectedSkillIndex];

    if (currentTooltip.style.visibility == "visible") {
        currentTooltip.style.visibility = "hidden";
    } else {
        tooltips.forEach(element => element.style.visibility = "hidden");
        currentTooltip.style.visibility = "visible";
    }
}

export function hideAllSkillTooltips() {
    let tooltips = <HTMLElement[]>[...document.getElementById("skill-levels-container").querySelectorAll(".skill_tooltip")];
    tooltips.forEach(element => element.style.visibility = "hidden");
}