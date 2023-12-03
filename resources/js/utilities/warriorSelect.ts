import { ajaxJS } from "../ajax";

const warriorSelect = {
    addWarriorEvents() {
        [...document.getElementsByClassName("warrior-select-card")].forEach(element => {
            element.addEventListener('click', event => this.selectWarrior(event));
            element.querySelectorAll("button")[0].addEventListener('click', event => this.flipWarriorCard(event));
        })
    },
    selectWarrior(event) {
        let div = event.currentTarget;
        let checkbox = <HTMLInputElement>div.querySelectorAll("input[type=checkbox]")[0];

        // If event target is not the checkbox, toggle the checked property
        if(!checkbox.checked) {
            checkbox.checked = true;
            document.getElementById(div.id).style.border = "3px ridge #5f4121";
            this.selectedWarriorAmount += 1;
        }
        else {
            document.getElementById(div.id).style.border = "1px ridge #5f4121";
            checkbox.checked = false;
            this.selectedWarriorAmount -= 1;
        }
        this.updateSelectedWarriors();
    },
    warriorsCheck() {
        let warriors_div = document.getElementsByClassName("warrior-select-card");
        let warrior_check = [];
        document.getElementsByClassName("warrior-select-card");
        for(let i = 0; i < warriors_div.length; i++) {
            let checkbox = <HTMLInputElement>warriors_div[i].querySelectorAll("input[type=checkbox]")[0];
            if(checkbox.checked) {
                let warrior = warriors_div[i].id;
                let warror_id = warrior.replace("warrior_", "");
                warrior_check.push(warror_id);
            }
        }
        return warrior_check;
    },
    flipWarriorCard(event) {
        let div = event.currentTarget;      
        if(div.style.transform.indexOf("180") !== -1) {
            div.style.transform = "rotateY(0deg)";
        }
        else {
            div.style.transform = "rotateY(180deg)";
        }
    },
    selectedWarriorAmount: 0,
    updateSelectedWarriors() {
        let warrior_amount = <HTMLElement>document.getElementById("selected_warrior_amount");
        if(!warrior_amount) return false;

        warrior_amount.innerHTML = "" + this.selectedWarriorAmount;
    },
    getAvailableWarriors() {
        if(!document.getElementById("warriors-select-wrapper")) return false;
        let data = "model=Warriors" + "&method=" + "getAvailableWarriors";
        ajaxJS(data, response => {
            let responsetext = response[1];
            let div = document.createElement("div");
            div.innerHTML = responsetext.html;
            console.log(div.children[0]);
            document.getElementById("warriors-select-wrapper").replaceWith(div.children[0]);
            this.addWarriorEvents();
        })
    }
};
export default warriorSelect;