    if(document.getElementById("news_content").children[3] != null) {
        getCountdown();
        var img = document.getElementById("select").querySelectorAll("img");
        img.forEach(function(element) {
              // ... code code code for this one element
                element.addEventListener('click', showSelect);
            });
        document.getElementById("data_form").querySelectorAll("button")[0].addEventListener("click", grow);
        document.getElementById("seed_g").children[3].addEventListener("click", seedGenerator);
    }
    var intervals = [];
    function getCountdown() {
        document.getElementById("growing").innerHTML = "No crops growing";
        var data = "model=Crops" + "&method=checkCountdown";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = data[0] * 1000;
                var harvest = data[1];
                if(data[2].indexOf("none") != -1) {
                    document.getElementById("growing").innerHTML = "Currently growing " + jsUcfirst(data[2]);
                }
                var x = setInterval (function() {
                    intervals.push(x);
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0 && harvest === "true"){
                        clearInterval(x);
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Harvest");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateCrop);
                        document.getElementById('time').appendChild(btn);
                        document.getElementById("growing").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("growing").innerHTML = "No crops growing";
                    }
                }, 1000);
            }
        });
    }
    window.onload = getCountdown();
    function grow() {
        var form = document.getElementById("plant");
        
        if(!form.reportValidity()) {
            console.log("error");
        }
        else {
            var JSON_data = JSONForm(form);
            console.log(JSON_data);
            var data = "model=SetCrops" + "&method=setCrops" + "&JSON_data=" + JSON_data;
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    getCountdown();
                    var text = response[1].split("|");
                    console.log(text);
                }
            });
        }
    }
    function updateCrop() {
        var data = "model=UpdateCrops" + "&method=updateCrops";
        ajaxP(data, function (response) {
            if(response[0] !== false) {
                getCountdown(); 
            }
        });
    }
    function destroyCrops() {
        var conf = confirm("You will lose seeds used to plant crops, proceed?");
        if(conf != true) {
            return false;
        }
        var data = "model=Crops" + "&method=destroyCrops";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
                window.clearInterval(intervals.pop());
                getCountdown();
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
        if(itemData === false) {
            return false;
        }
        var items = ["potato", "tomato", "corn", "carrots", "cabbages", "wheat", "sugar", "spices", "apples", "oranges", "watermelon"];
        if(items.indexOf(itemData[0]) == -1) {
            gameLog("ERROR: Pick a valid item");
            return false;
        }
        var data = "model=Crops" + "&method=getSeeds" + "&item=" + itemData[0] + "&amount=" + itemData[1];
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory("crops");
            }
        });
    }
    function newCrop(name, seeds, time) {
        this.src = "public/img/" + name + ".png";
        this.seeds = seeds;
        this.time = time;
    }
    var potato = new newCrop('potato', 1, 1000);
    var tomato = new newCrop('tomato', 1, 1000);
    var corn = new newCrop('corn', 1, 1000);
    var carrots = new newCrop('carrots', 1, 1000);
    var cabbages = new newCrop('cabbages', 1, 1000);
    var wheat = new newCrop('wheat', 1, 1000);
    var sugar = new newCrop('sugar', 1, 1000);
    var spices = new newCrop('spices', 1, 1000);
    var apples = new newCrop('apples', 1, 1000);
    var oranges = new newCrop('oranges', 1, 1000);
    var watermelon = new newCrop('watermelon', 1, 1000);