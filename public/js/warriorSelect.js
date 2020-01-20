    function checkboxed(checkbox) {
        return function(){
            check();
        };
    }
    function check() {
        var div = event.target.closest(".warriors");
        var checkbox = div.querySelectorAll("input[type=checkbox]")[0];
        if(checkbox.checked) {
            document.getElementById(div.id).style = "border: 1px solid red";
        }
        else {
            document.getElementById(div.id).style = "border: 1px solid black";
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
    }