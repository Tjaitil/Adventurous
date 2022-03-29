    function checkboxed(checkbox) {
        return function(){
            check();
        };
    }
    function addWarriorEvents() {
        let div = [...document.getElementsByClassName("warrior-select-card")];
        div.forEach(element => {
            element.addEventListener('click', () => checkWarriorBox());
            element.querySelectorAll("button")[0].addEventListener('click', flipWarriorCard);
        })
    }
    function checkWarriorBox() {
        // function to check wether checkbox is checked or not. Apply styles based on bolean value
        if(event.target.tagName == "BUTTON") {
            return false;
        }
        var div = event.target.closest(".warrior-select-card");
        var checkbox = div.querySelectorAll("input[type=checkbox]")[0];
        let increment = 0;
        // if(div.querySelectorAll(".warrior_back")[0].innerHTML.indexOf("arrows") != -1) {
        //     // add warning
        // }
        // If event target is not the checkbox, toggle the checked property
        if(event.target.tagName != "INPUT") {
            if(checkbox.checked) {
                document.getElementById(div.id).querySelectorAll("input[type=checkbox]")[0].checked = false;
            }
            else {
                document.getElementById(div.id).querySelectorAll("input[type=checkbox]")[0].checked = true;
            }
        }
        if(checkbox.checked) {
            document.getElementById(div.id).style.border = "3px ridge #5f4121";
            increment = 1;
        }
        else {
            document.getElementById(div.id).style.border = "1px ridge #5f4121";
            increment = -1;
        }
        updateSelectedWarriors(increment);
    }
    function warriorsCheck() {
        var warriors_div = document.getElementsByClassName("warrior-select-card");
        var warrior_check = [];
        for(var i = 0; i < warriors_div.length; i++) {
            var checkbox = warriors_div[i].querySelectorAll("input[type=checkbox]")[0];
            if(checkbox.checked === true) {
                var warrior = warriors_div[i].id;
                var warror_id = warrior.replace("warrior_", "");
                warrior_check.push(warror_id);
            }
        }
        return warrior_check;
    }
    function flipWarriorCard() {
        let div = event.target.closest(".warrior-select-card");      
        if(div.style.transform.indexOf("180") !== -1) {
            div.style.transform = "rotateY(0deg)";
        }
        else {
            div.style.transform = "rotateY(180deg)";
        }
    }
    function updateSelectedWarriors(increment) {
        let element = document.getElementById("selected_warrior_amount");
        if(!element) return false;
        let warriorAmount = parseInt(element.innerHTML);
        warriorAmount += increment;
        element.innerHTML = warriorAmount;
    }