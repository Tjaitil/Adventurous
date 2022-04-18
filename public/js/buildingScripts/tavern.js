    // Check if news_content_main_content -> children[2] has gotten content from game.js -> game.fetchBuilding()
    if(document.getElementById("news_content").children[2] != null) {
        document.getElementById("eat").querySelectorAll("button")[0].addEventListener("click", eat);
        selectItemEvent.addSelectEvent();
    }
    
    function recruitWorker(type, level = false) {
        var element = event.target.parentNode;
        let data = "model=RecruitWorker" + "&method=recruitWorker" + "&type=" + type + "&level=" + level;
        ajaxP(data, function(response) {
           if(response[0] != false) {
                element.parentNode.removeChild(element);
           }
        });
    }    
    // function talk(person, part) {
    //     document.getElementById("curtain").style.display = "block";
    //     document.getElementById("conversation").style.display = "block";
    //     var data = "model=Talk" + "&method=talk" + "&person=" + person + "&part=" + part;
    //     ajaxG(data, function(response) {
    //         if(response[0] != false) {
    //             var responseText = response[1].split("|");
    //             if(responseText[1].search("<") != -1) {
    //                 document.getElementById("conv_button").style.visibility = "hidden";
    //             }
                
    //             document.getElementById(responseText[0]).style.visibility = "hidden";
    //             document.getElementById("conv").innerHTML = responseText[1];  
    //         }
    //     });        
    // }   
    function close() {
        console.log("close");
        document.getElementById("curtain").style.display = "none";
        document.getElementById("conversation").style.display = "none";
    }
    function getHealingAmount(item) {
        console.log(event.target);
        if(item.length == 0) {
            return false;
        }
        let data = "model=Tavern" + "&method=getHealingAmount" + "&item=" + item; 
        ajaxG(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                let responseText = response[1];
                if(parseInt(responseText) === 0) {
                    document.getElementById("item_healing_amount").innerText = "No healing from this item";
                }
                else {
                    document.getElementById("item_healing_amount").innerText = "Healing per item " + responseText.heal;
                }
                
            }
        });
    }
    function eat() {
        let item = document.getElementById("selected").querySelectorAll("figure")[0].children[1].innerHTML.toLowerCase();
        if(item.length == 0) {
            gameLogger.addMessage("ERROR: Select a item to eat!");
            gameLogger.logMessages();
            return false;
        }
        let amount = document.getElementById("eat").querySelectorAll("input")[0].value;
        if(amount == 0 || amount == null) {
            gameLogger.addMessage("ERROR: Select a amount");
            gameLogger.logMessages();
            return false;
        }
        let data = "model=Hunger" + "&method=eat" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                getHunger();
                updateInventory();
                document.getElementById("selected").innerHTML = "";
                document.getElementById("eat").querySelectorAll("input")[0].value = "";
            }
        });
    }