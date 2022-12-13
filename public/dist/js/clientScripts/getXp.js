window.addEventListener("load", () => {
    let wrappers = [...document.getElementsByClassName("profiency-level-wrapper")];
    console.log(wrappers);
    wrappers.forEach((element) => element.addEventListener("click", () => getSkillXp(element, element.dataset.wrapperSkill)));
});
export async function getSkillXp(element, skill) {
    let divs = document.getElementById("skills").querySelectorAll("div");
    for (const div of divs) {
        if (div === element && element.style.visibility === "visible") {
            element.style.visibility = "hidden";
        }
        else {
            element.style.visibility = "hidden";
        }
    }
    // if (skill == false ) {
    //     for (let x = 0; x < divs.length; x++) {
    //         let element = <HTMLElement>divs[x].children[1];
    //         element.style.visibility = "hidden";
    //     }
    //     return false;
    // }
    const skillDivs = {
        adventurer: 0,
        farmer: 1,
        miner: 2,
        trader: 3,
        warrior: 4,
    };
    // let element = event.target.closest("div");
    // for (let i = 0; i < divs.length; i++) {
    //     let div = <HTMLElement>divs[i].children[1];
    //     if (skillDivs[skill] != i) {
    //         div.style.visibility = "hidden";
    //     }
    // }
    let tooltip = element.querySelectorAll(".skill_tooltip")[0];
    tooltip.style.right = "-30%";
    if (tooltip.style.visibility == "visible") {
        tooltip.style.visibility = "hidden";
        return false;
    }
    else if (skill == "adventurer") {
        tooltip.innerHTML = skill.charAt(0).toUpperCase() + skill.slice(1);
        tooltip.style.visibility = "visible";
    }
    else {
        await fetch("handlers/handler_ses.php?" + new URLSearchParams({ variable: skill }))
            .then((res) => res.json())
            .then((res) => {
            console.log(res);
        });
        // let ajaxRequest = new XMLHttpRequest();
        // ajaxRequest.onload = function () {
        //     if (this.readyState == 4 && this.status == 200) {
        //         var data = this.responseText.split("|");
        //         data.shift();
        //         let skillName = jsUcfirst(skill);
        //         /*element.title = skillName + '\n' + "Current xp: " + data[0] + '\n' + "Next level: " + data[1];*/
        //         tooltip.innerHTML = skillName + "</br>" + "Current xp: " + data[0] + "</br>" + "Next level: " + data[1];
        //         tooltip.style.visibility = "visible";
        //     }
        // };
        // ajaxRequest.open("GET", "handlers/handler_ses.php?variable=" + skill);
        // ajaxRequest.send();
    }
}
export function show_xp(skill, xp) {
    var elements = {
        adventurer: 0,
        farmer: 1,
        miner: 2,
        trader: 3,
        warriors: 4,
    };
    var element = document.getElementById("skills").children[elements[skill]].children[2];
    element.innerHTML = "+" + xp;
    setTimeout(hide_xp, 2000, element);
}
export function hide_xp(element) {
    element.innerHTML = "";
}
