    if(document.getElementById("news_content").children[2] != null) {
        // selectitem.js
        selectItemEvent.addSelectEvent();
        document.getElementById("news_content_main_content").querySelectorAll("button")[0].addEventListener("click", function() {
           inputHandler.fetchBuilding('armycamp'); 
        });
        if(/Safari|Chrome/i.test(navigator.userAgent)) {
            let span = document.getElementsByClassName("armory_view_span");
            for(var i = 0; i < span.length; i++) {
                span[i].style.left = "-10%";
                span[i].style.display = "block";
            }
        }
    }
    function toggleOption() {
        var element = document.getElementById("selected").children[0].children[1].innerHTML;
        if(element.search("Sword") != -1 || element.search("Dagger") != -1) {
            document.getElementById("type").style.visibility = "visible";
        }
        else if(element.search("Arrow") != -1 || element.search("Knives") != -1) {
            document.getElementById("ranged_alt").style.visibility = "visible";
        }
        else {
            document.getElementById("type").style.visibility = "hidden";
        }
    }
    function wearArmor() {
        let warrior_id = document.getElementById("select_warrior").selectedIndex;
        let element = document.getElementById("selected");
        let item = element.children[0].children[1].innerHTML;
        item = item.trim();
        let result = false;
        let minerals = ["Iron", "Steel", "Gargonite", "Adron", "Yeqdon", "Frajrite", "Oak", "Beech", "Yew"];
        let items = ["Sword", "Spear", "Dagger", "Shield", "Platebody", "Platelegs", "Helm", "Arrows", "Bow" , "Throwing", "Boots"];
        // Check out if the $item matches $mineral and $item
        let item_array = item.split(" ");
        if(minerals.indexOf(item_array[0]) == -1) {
            result = true;
        }
        if(items.indexOf(item_array[1]) == -1) {
            result = true;
        }
        if(result === true) {
            gameLogger.addMessage("ERROR: Select a valid item to wear!");
            gameLogger.logMessages();
            return false;
        }
        let select = document.getElementById("type");
        let hand;
        if(select.style.visibility == "visible") {
            hand = select.options[select.selectedIndex].value;
        }
        else {
            hand = false;
        }
        let rangedAmount = document.getElementById("ranged_alt");
        let amount;
        if(rangedAmount.style.visibility == "visible") {
            amount = rangedAmount.querySelectorAll("input")[0].value;
        }
        else {
            amount = false;
        }
        let data = "model=Armory" + "&method=wearArmor" + "&warrior_id=" + warrior_id + "&item=" + item  + "&hand=" +
                    hand + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                document.getElementById("selected").innerHTML = "";
                document.getElementById("ranged_alt").children[1].value = 1;
                document.querySelectorAll(".armory_view")[warrior_id -1].innerHTML = response[1];
                updateInventory('armory', true);
            }
        }, false);
    }
    
    function removeArmor(element) {
        var parent = element.parentNode;
        var warrior_id = parent.querySelectorAll("p")[0].innerHTML.split("#")[1].trim();
        var item = element.title;
        var part = element.className;
        if(item === 'none') {
            return false;
        }
        var data = "model=Armory" + "&method=removeArmor" + "&warrior_id=" + warrior_id + "&part=" + part;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                document.getElementsByClassName("armory_view")[warrior_id - 1].innerHTML = response[1];
                updateInventory('armory', true);
            }
        });
    }
    function updatePage() {
        var data = "model=Armory" + "&method=getData";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                document.getElementById("warriors").innerHTML = response[1];
            }
        });
    }
    function testCombatSkills(warriors) {
        var data = "model=test" + "&method=loadCombat" + "&route=calculator" + "&warriors=" + JSON.stringify(warriors);
        ajaxP(data, function(response) {
            console.log(response);
        });
    }