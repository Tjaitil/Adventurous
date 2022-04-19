    scriptLoader.loadScript(['jsonForm'], 'utility');
    if(document.getElementById("news_content").children[2] != null) {
        getCountdown();
        var img = document.getElementById("select").querySelectorAll("img");
        img.forEach(function(element) {
              // ... code code code for this one element
                element.addEventListener('click', showSelect);
            });
        console.log(document.getElementById("data_container").querySelectorAll("button"));
        document.getElementById("data_container").querySelectorAll("button")[0].addEventListener("click", grow);
        document.getElementById("seed_generator").children[3].addEventListener("click", seedGenerator);
        document.getElementById("cancel_action").addEventListener("click", destroyCrops);
        // selectitem.js
        selectItemEvent.addSelectEvent();
        fetchData();
    }
    var intervals = [];
    function getCountdown() {
        document.getElementById("growing").innerHTML = "No crops growing";
        var data = "model=Crops" + "&method=checkCountdown";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                console.log(response[1]);
                var data = response[1].split("|");
                console.log(data);
                var time = data[0] * 1000;
                var harvest = parseInt(data[1]);
                console.log(harvest);
                var x = setInterval(function() {
                    intervals.push(x);
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("time").innerText = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                        document.getElementById("cancel_action").style.visibility = "";
                    }
                    // Check if countdown is finished and harvest is true
                    if(distance < 0 && harvest === 1) {
                        clearInterval(x);
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Harvest");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateCrop);
                        document.getElementById("cancel_action").style.visibility = "hidden";
                        document.getElementById("time").innerText = "";
                        document.getElementById('time').appendChild(btn);
                        document.getElementById("growing").innerText = "Finished";
                    }
                    else if(distance < 0) {
                        clearInterval(x);
                        document.getElementById("growing").innerText = "No crops growing";
                        document.getElementById("time").innerText = "";
                    }
                }, 1000);
            }
        });
    }
    function grow() {
        var form = document.getElementById("data_form");
        if(!form.reportValidity()) {
            console.log("error");
        }
        else {
            let JSON_data = JSONForm(form);
            let data = "model=SetCrops" + "&method=setCrops" + "&JSON_data=" + JSON_data;
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    let responseText = response[1];
                    updateHunger(responseText.newHunger);
                    getCountdown();
                    updateCountdownTab();
                    document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
                }
            });
        }
    }
    function updateCrop() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let data = "model=UpdateCrops" + "&method=updateCrops";
        ajaxP(data, function (response) {
            if(response[0] !== false) {
                let responseText = response[1];
                getCountdown();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';

            }
        });
    }
    function destroyCrops() {
        if(confirm("You will lose seeds used to plant crops, proceed?")) {
            return false;
        }
        var data = "model=Crops" + "&method=destroyCrops";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1];
                window.clearInterval(intervals.pop());
                getCountdown();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
            }
        });
    }
    function img() {
        var img = document.getElementById("type_img");
        var select = document.getElementById("form_select");
        var name = select.children[select.selectedIndex].value;
        if(name.length < 1) {
            return;
        }
        img.style = "display:block";
        img.src = "public/images/" + name;
    }
    function seedGenerator() {
        var itemData = selectedCheck();
        console.log(itemData);
        if(itemData === false) {
            return false;
        }
        var items = ["potato", "tomato", "corn", "carrots", "cabbages", "wheat", "sugar", "spices", "apples", "oranges", "watermelon"];
        if(items.indexOf(itemData[0]) == -1) {
            gameLogger.addMessage("ERROR: Pick a valid item");
            gameLogger.logMessages();
            return false;
        }
        var data = "model=Crops" + "&method=getSeeds" + "&type=" + itemData[0] + "&amount=" + itemData[1];
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory("crops");
                document.getElementById("selected_amount").value = "";
            }
        });
    }