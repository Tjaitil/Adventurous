    function checkboxed(checkbox) {
        return function(){
            check();
        };
    }
    function addWarriorEvents(element) {
        element.addEventListener('click', function() {
            checkWarriorBox();
        });
        element.querySelectorAll("button")[0].addEventListener('click', flipWarriorCard);
    }
    function checkWarriorBox() {
        // function to check wether checkbox is checked or not. Apply styles based on bolean value
        if(event.target.tagName == "BUTTON") {
            return false;
        }
        var div = event.target.closest(".warriors");
        var checkbox = div.querySelectorAll("input[type=checkbox]")[0];
        let increment = 0;
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
        var warriors_div = document.getElementsByClassName("warriors");
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
        let div = event.target.closest(".warriors");
        div.style.transition = "transform 0.2s";
        if(div.style.transform.indexOf("180") != -1) {
            div.style.transform = "rotateY(0deg)";
        }
        else {
            div.style.transform = "rotateY(180deg)";
        }
        setTimeout(function() {
            if(div.style.transform.indexOf("180") == -1) {
                div.querySelectorAll(".warrior_front")[0].style.visibility = "visible";
                div.querySelectorAll(".warrior_back")[0].style.visibility = "hidden";
            }
            else {
                div.querySelectorAll(".warrior_front")[0].style.visibility = "hidden";
                div.querySelectorAll(".warrior_back")[0].style.visibility = "visible";
            }
            div.querySelectorAll("button")[0].style.visibility = "visible"; 
        }, 100);
    }
    function updateSelectedWarriors(increment) {
        let element = document.getElementById("selected_warrior_amount");
        let warriorAmount = parseInt(element.innerHTML);
        warriorAmount += increment;
        element.innerHTML = warriorAmount;
    }