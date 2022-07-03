const warriorSelect = {
    addWarriorEvents() {
        [...document.getElementsByClassName("warrior-select-card")].forEach(element => {
            element.addEventListener('click', event => this.selectWarrior(event));
            element.querySelectorAll("button")[0].addEventListener('click', event => this.flipWarriorCard(event));
        })
    },
    selectWarrior(event) {
        let div = event.currentTarget;
        let checkbox = div.querySelectorAll("input[type=checkbox]")[0];

        // If event target is not the checkbox, toggle the checked property
        if(!checkbox.checked) {
            document.getElementById(div.id).querySelectorAll("input[type=checkbox]")[0].checked = true;
            document.getElementById(div.id).style.border = "3px ridge #5f4121";
            this.selectedWarriorAmount += 1;
        }
        else {
            document.getElementById(div.id).style.border = "1px ridge #5f4121";
            document.getElementById(div.id).querySelectorAll("input[type=checkbox]")[0].checked = false;
            this.selectedWarriorAmount -= 1;
        }
        this.updateSelectedWarriors();
    },
    warriorsCheck() {
        let warriors_div = document.getElementsByClassName("warrior-select-card");
        let warrior_check = [];
        document.getElementsByClassName("warrior-select-card");
        for(let i = 0; i < warriors_div.length; i++) {
            let checkbox = warriors_div[i].querySelectorAll("input[type=checkbox]")[0];
            if(checkbox.checked) {
                let warrior = warriors_div[i].id;
                let warror_id = warrior.replace("warrior_", "");
                warrior_check.push(warror_id);
            }
        }
        return warrior_check;
    },
    flipWarriorCard() {
        let div = event.target.closest(".warrior-select-card");      
        if(div.style.transform.indexOf("180") !== -1) {
            div.style.transform = "rotateY(0deg)";
        }
        else {
            div.style.transform = "rotateY(180deg)";
        }
    },
    selectedWarriorAmount: 0,
    updateSelectedWarriors() {
        if(!document.getElementById("selected_warrior_amount")) return false;
        document.getElementById("selected_warrior_amount").innerHTML = this.selectedWarriorAmount;
    },
    getAvailableWarriors() {
        if(!document.getElementById("warriors-select-wrapper")) return false;
        ajaxJS(data, response => {
            let responsetext = response[1];
            document.getElementById("warriors-select-wrapper").replaceWith(responsetext.warriors);
        })
    }
};
export default warriorSelect;