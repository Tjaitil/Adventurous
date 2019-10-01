    
    document.getElementById("selected").addEventListener("change", function() {
        console.log("hello");
    });
    
    function toggleOption() {
        var element = document.getElementById("selected").children[0].children[1].innerHTML;
        if(element.search("Sword") != -1 || element.search("Dagger") != -1) {
            document.getElementById("type").style.visibility = "visible";
        }
        else {
            document.getElementById("type").style.visibility = "hidden";
        }
    }
    function wearArmor() {
        var warrior_id = document.getElementById("select_warrior").selectedIndex;
        var element = document.getElementById("selected");
        var item = element.children[0].children[1].innerHTML;
        item = item.trim();
        var result = false;
        var minerals = ["Iron", "Steel", "Gargonite", "Adron", "Yeqdon", "Frajrite"];
        var items = ["Sword", "Spear", "Dagger", "Shield", "Platebody", "Platelegs", "Helm"];
        // Check out if the $item matches $mineral and $item
        var item_array = item.split(" ");
        console.log(item_array);
        console.log(items.indexOf(item_array[0]));
        if(minerals.indexOf(item_array[0]) == -1) {
            result = true;
        }
        if(items.indexOf(item_array[1]) == -1) {
            result = true;
        }
        if(result === true) {
            gameLog("ERROR: Select a valid item to wear!");
            return false;
        }
        var select = document.getElementById("type");
        var hand;
        console.log(select);
        if(select.style.visibility == "visible") {
            hand = select.options[select.selectedIndex].value;
        }
        else {
            hand = false;
        }
        var data = "model=Armory" + "&method=wearArmor" + "&warrior_id=" + warrior_id + "&item=" + item  + "&hand=" + hand;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                console.log(response[1]);
                document.getElementById("selected").innerHTML = "";
                updateInventory('armory');
                document.querySelectorAll(".armory_view")[warrior_id -1].innerHTML = response[1];
            }
        }, false);
    }
    
    function removeArmor(element) {
        var parent = element.parentNode;
        var warrior_id = parent.children[0].innerHTML;
        var item = element.title;
        var part = element.className;
        if(item === 'none') {
            return false;
        }
        var data = "model=Armory" + "&method=removeArmor" + "&warrior_id=" + warrior_id + "&item=" + item + "&part=" + part;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                document.getElementsByClassName("armory_view")[warrior_id - 1].innerHTML = response[1];
                updateInventory('armory');
            }
        });
    }
    
    function updatePage() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                document.getElementById("warriors").innerHTML = this.responseText;
                /*var soldiers = this.responseText.split("||");
                soldiers.pop();
                var list = document.getElementsByClassName("armory_view");
                var children_count = list[0].children;
                for(var i = 0; i < list.length; i++) {
                    var soldier_armor = soldiers[i].split("|");
                    var y = i;
                    for(var x = 0; x < children_count.length - 1; x++) {
                        var element = list[y].children[x + 1];
                        var source = "public/images/" + soldier_armor[x + 1] + ".jpg";
                        element.src = source.replace(" ", "_");
                        element.title = soldier_armor[x + 1];
                    }
                }*/
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=Armory" + "&method=getData");
        ajaxRequest.send();
    }