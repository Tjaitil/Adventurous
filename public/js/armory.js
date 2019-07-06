    
    function wearArmor() {
        var warrior_id = document.getElementById("select_warrior").selectedIndex;
        var element = document.getElementById("selected");
        var item = element.children[0].children[1].innerHTML;
        item = item.trim();
        var data = "model=Armory" + "&method=wearArmor" + "&warrior_id=" + warrior_id + "&item=" + item;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                document.getElementById("selected").innerHTML = "";
                updatePage();
                updateInventory('armory');
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
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
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                updatePage();
                updateInventory('armory');
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
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