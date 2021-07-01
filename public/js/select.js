    // Module file
    function showSelect() {
        var element = event.target;
        var clone = element.cloneNode(true);
        clone.removeAttribute("onclick");
        var item = element.getAttribute("alt");
        let className;
        if(document.getElementsByClassName("page_title")[0].innerText == "Crops") {
            className = "crop";
        }
        else {
            className = "mineral";
        }
        let items = document.getElementsByClassName(className);
        for(var i = 0; i < items.length; i++) {
            if(item == items[i].getAttribute("alt")) {
                items[i].style.border = "2px solid black";
            }
            else {
                items[i].style.border = "none";
            }
        }
        clone.style.border = "none";
        var div = document.getElementById("data_form");
        div.style.visibility = "visible";
        document.getElementById("data").style.visibility = "visible";
        var div_inputs = div.querySelectorAll("input");
        div_inputs[0].value = jsUcfirst(item);
        document.getElementsByName("time")[0].value = typeData[item].time;
        document.getElementById("reduction_time").innerText = "- " + (parseInt(typeData[item].time) * 
                gameData['workforce_data']['efficiency_level'] / 100) + 's';
        document.getElementsByName("experience")[0].value = typeData[item].experience;
        document.getElementsByName("location")[0].value = typeData[item].location;
        
        let imgSrc;
        // Check wether or not the player are in crops or mine
        if(document.getElementsByClassName("page_title")[0].innerText == "Crops") {
            document.getElementsByName("seeds")[0].value = typeData[item].seed_required;    
            document.getElementsByName("level")[0].value = typeData[item].farmer_level;
            imgSrc = typeData[item].crop_type + ".png";
        }
        else {
            document.getElementsByName("permits")[0].value = typeData[item].permit_cost;
            document.getElementsByName("level")[0].value = typeData[item].miner_level;
            imgSrc = typeData[item].mineral_type + ".png";
        }
        let selectedFigure = document.getElementById("selected_item");
        if(selectedFigure.children.length == 0) {
            selectedFigure.appendChild(clone);
        }
        else {
            let img = selectedFigure.children[0]; 
            img.src = "public/images/" + item + ".png";
            img.alt = item;
            img.style.border = "";
        }
    }
    function fetchData(site) {
        let data;
        if(game.properties.building === 'crops') {
            data = "model=Crops" + "&method=getData";
        }
        else {
            data = "model=Mine" + "&method=getData";
        }
        ajaxJS(data, function(response) {
            console.log(response);
            let responseText = JSON.parse(response[1]);
            gameData = responseText;
            if(game.properties.building === 'crops') {
                responseText['crop_types'];
                for(let i = 0; i < responseText['crop_types'].length; i++) {
                    typeData[responseText['crop_types'][i]['crop_type']] = responseText['crop_types'][i];
                }    
            }
            else {
                responseText['mineral_types'];
                for(let i = 0; i < responseText['mineral_types'].length; i++) {
                    typeData[responseText['mineral_types'][i]['mineral_type']] = responseText['mineral_types'][i];
                }    
            }
            console.log(document.getElementsByName("workforce"));
            document.getElementsByName("workforce")[0].setAttribute("max", gameData.workforce_data.avail_workforce);
        });
    }
    var typeData = {
        
    };
    var gameData;
    