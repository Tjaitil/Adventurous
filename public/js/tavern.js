    window.onload = function () {
        document.getElementById("eat").querySelectorAll("button")[0].addEventListener("click", eat);
    };
    
    function recruitWorker(type, level = false) {
        var element = event.target.parentNode;
        var data ="model=RecruitWorker" + "&method=recruitWorker" + "&type=" + type;
        if(level != false) {
            data += "&level=" + level;
        }
        ajaxP(data, function(response) {
           if(response[0] != false) {
                gameLog(response[1]);
                element.parentNode.removeChild(element);
           }
        });
    }
    
    function talk(person, part) {
        document.getElementById("curtain").style.display = "block";
        document.getElementById("conversation").style.display = "block";
        var data = "model=talk" + "&method=talk" + "&person=" + person + "&part=" + part;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var responseText = response[1].split("|");
                if(responseText[1].search("<") != -1) {
                    document.getElementById("conv_button").style.visibility = "hidden";
                }
                
                document.getElementById(responseText[0]).style.visibility = "hidden";
                document.getElementById("conv").innerHTML = responseText[1];  
            }
        });        
    }   
    function close() {
        console.log("close");
        document.getElementById("curtain").style.display = "none";
        document.getElementById("conversation").style.display = "none";
    }
    function eat() {
        var item = document.getElementById("selected").querySelectorAll("figure")[0].children[1].innerHTML.toLowerCase();
        if(item.length == 0) {
            gameLog("ERROR: Select a item to eat!");
            return false;
        }
        var amount = document.getElementById("eat").querySelectorAll("input")[0].value;
        if(amount == 0 || amount == null) {
            gameLog("ERROR: Select a amount");
            return false;
        }
        var data = "model=Hunger" + "&method=eat" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                calculateHunger();
                updateInventory();
                document.getElementById("selected").innerHTML = "";
            }
        });
    }