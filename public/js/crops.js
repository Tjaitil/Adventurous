    document.getElementById("seed_g").children[3].addEventListener("click", seedGenerator);
    document.getElementById("plant_button").addEventListener("click", grow);
    function getCountdown() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                var time = data[0] * 1000;
                var harvest = data[1];
                console.log(data);
                var x = setInterval (function() {
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
            console.log(form);
            var JSON_data = JSONForm(form);
            console.log(result);
            var data = "model=SetCrops" + "&method=setCrops" + "&JSON_data=" + JSON_data;
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    
                }
            });
        }
    }
    function updateCrop() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    getCountdown();
                }
            }
        };
        ajaxRequest.open("GET", "/handlers/handler_js.php?&model=UpdateCrops" + "&method=updateCrops");
        ajaxRequest.send();
    }
    
    function destroyCrops() {
        var conf = confirm("You will lose seeds used to plant crops, proceed?");
        if(conf != true) {
            return false;
        }
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    gameLog(this.responseText);
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=Crops" + "&method=destroyCrops");
        ajaxRequest.send();
    }
/*
 *
 *document.getElementById("workforce_ava").innerHTML = ava;
            document.getElementById("workforce_tot").innerHTML= workforce;
 *
 *var estimate = {
        crop: document.getElementById("plant_crop").value,
        quantity: document.getElementById("plant_quantity").value,
        workforce: document.getElementById("plant_workforce").value,
        console: function () {console.log(this.crop);},
        estimated: function () {
            var cropf = this.crop;
            var quantityf = this.quantity;
            var workforcef = this.workforce;
            var combined = cropf * quantityf / workforcef + "s";
            document.getElementById("plant_estimated").value = this.crop +  "+"  + this.quantity + "+" + this.workforce + "=" + combined;
        },
    };*/
/*document.getElementById("plant_crop").addEventListener("change", estimate.estimated);
document.getElementById("plant_quantity").addEventListener("input", estimate.estimated);
document.getElementById("plant_workforce").addEventListener("input", estimate.estimated);*/

                /*var type = {
                    make: function(name, location, level) {
                    this.name = name;
                    this.location = location;
                    this.level = level;
                  }
                };
                
                var corn  = new type.make('corn', 'USA');
                console.log(corn);
            
                var corn1 = 3;

                for (var i = 0; i < corn1; i++) {
                document.getElementById("crop_" + [i]).style.backgroundColor = "red";
                }
            
                function showTable () {
                document.getElementById("crops_table").style.visibility = "visible";
                document.getElementById("crops_table").style.display = "block";
                document.getElementById("crops").style.visibility = "hidden";
                }
                function showFig () {
                document.getElementById("crops_table").style.visibility = "hidden";
                document.getElementById("crops_table").style.display = "none";
                document.getElementById("crops").style.visibility = "visible";
                }*/
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