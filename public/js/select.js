    // Module file
    function showSelect() {
        var element = event.target;
        var clone = element.cloneNode(true);
        clone.removeAttribute("onclick");
        var item = element.getAttribute("alt");
        let className;
        if(document.getElementsByClassName("page_title")[0].innerText === "crops") {
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

        let baseReduction = Number(parseInt(typeData[item].time) * (workforceData['efficiency_level'] * 0.01)).toFixed(2);
        let perWorkforce = Number(parseInt(typeData[item].time) * (1 * 0.02)).toFixed(2);
        document.getElementById("reduction_time").innerText = "- " + baseReduction + 's ' + '& -' + perWorkforce + 's each worker';
        document.getElementsByName("experience")[0].value = typeData[item].experience;
        document.getElementsByName("location")[0].value = typeData[item].location;
        
        let imgSrc;
        // Check wether or not the player are in crops or mine
        if(game.properties.building === "crops") {
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
            if(game.properties.building === 'mine') {
                item = item + ' ore';
            }
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
            let responseText = response[1];
            workforceData = responseText.workforce_data;
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
            document.getElementsByName("workforce")[0].setAttribute("max", responseText.workforce_data.avail_workforce);
        });
    }
    var typeData = {
        
    };
    var workforceData;
    