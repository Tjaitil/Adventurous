    function checkboxed(checkbox) {
        return function(){
            check();
        };
    }
    function check() {
        if(event.target.tagName == "BUTTON") {
            return false;
        }
        var div = event.target.closest(".warriors");
        var checkbox = div.querySelectorAll("input[type=checkbox]")[0];
        console.log(div);
        console.log(div.querySelectorAll("input[type=checkbox]"));
        if(checkbox.checked) {
            document.getElementById(div.id).style.border = "1px solid black";
            document.getElementById(div.id).querySelectorAll("input[type=checkbox]")[0].checked = false;
        }
        else {
            document.getElementById(div.id).style.border = "3px ridge #5f4121";
            document.getElementById(div.id).querySelectorAll("input[type=checkbox]")[0].checked = true;
        }
    }
    function warriorsCheck() {
        var warriors_div = document.getElementsByClassName("warriors");
        console.log(warriors_div[1]);
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
        console.log(div.style.transform.indexOf("180"));
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
    