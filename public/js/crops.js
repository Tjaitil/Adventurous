    document.getElementById("seed_g").children[3].addEventListener("click", seedGenerator);
    document.getElementById("plant_button").addEventListener("click", grow);
    var intervals = [];
    function getCountdown() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                var time = data[0] * 1000;
                var harvest = data[1];
                console.log(data);
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
                        document.getElementById('growing').appendChild(btn);
                        document.getElementById("time").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "None growing";
                    }
                }, 1000);
            }
        };
        ajaxRequest.open("GET", "/handlers/handler_g.php?&model=Crops" + "&method=checkCountdown");
        ajaxRequest.send();
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
                gameLog(this.responseText);
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
        var quantity = document.getElementById("quantity").value;
        if(!selectedCheck()) {
            return;
        }
        var items = ["potato", "tomato", "corn", "carrots", "cabbages", "wheat", "sugar", "spices", "apples", "oranges", "watermelon"];
        var item = document.getElementById("selected").children[0].children[1].innerHTML.toLowerCase();
        if(items.indexOf(item) == -1) {
            gameLog("ERROR: Pick a valid item");
            return false;
        }
        ajaxRequest = new XMLHttpRequest();
        var data = "model=Crops" + "&method=getSeeds" + "&item=" + item + "&quantity=" + quantity;
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    gameLog(this.responseText);   
                }
                else {
                    gameLog(this.responseText);
                    updateInventory("crops");
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);  
    }