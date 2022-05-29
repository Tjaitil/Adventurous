const skillContainer = {
    typeData: {
        
    },
    workforceData: {

    },
    showSelect(event) {
        let targetElement = event.currentTarget;
        let clone = targetElement.cloneNode(true);
        clone.removeAttribute("onclick");
        let item = targetElement.getAttribute("alt");
        let className;
        if(document.getElementsByClassName("page_title")[0].innerText === "crops") {
            className = "crop_type";
        }
        else {
            className = "mineral";
        }
        [...document.getElementsByClassName(className)].forEach(element => {
            if(targetElement === element) {
                element.classList.add("item_selected");
            } else {
                element.classList.remove("item_selected");
            }
        });
        clone.style.border = "none";
        let div = document.getElementById("data_form");
        div.style.visibility = "visible";
        document.getElementById("data").style.visibility = "visible";
        let div_inputs = div.querySelectorAll("input");
        div_inputs[0].value = jsUcfirst(item);
        console.log(item);
        document.getElementsByName("time")[0].value = this.typeData[item].time;

        let baseReduction = Number(parseInt(this.typeData[item].time) * (this.workforceData['efficiency_level'] * 0.01)).toFixed(2);
        let perWorkforce = Number(parseInt(this.typeData[item].time) * 0.005).toFixed(2);
        document.getElementById("reduction_time").innerText = "- " + baseReduction + 's ' + '& -' + perWorkforce + 's each worker';
        document.getElementsByName("experience")[0].value = this.typeData[item].experience;
        document.getElementsByName("location")[0].value = this.typeData[item].location;
        
        let imgSrc;
        // Check wether or not the player are in crops or mine
        if(game.properties.building === "crops") {
            document.getElementsByName("seeds")[0].value = this.typeData[item].seed_required;    
            document.getElementsByName("level")[0].value = this.typeData[item].farmer_level;
            imgSrc = this.typeData[item].crop_type + ".png";
        }
        else {
            document.getElementsByName("permits")[0].value = this.typeData[item].permit_cost;
            document.getElementsByName("level")[0].value = this.typeData[item].miner_level;
            imgSrc = this.typeData[item].mineral_type + ".png";
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
    },
    fetchData(site) {
        let data;
        if(game.properties.building === 'crops') {
            data = "model=Crops" + "&method=getData";
        }
        else {
            data = "model=Mine" + "&method=getData";
        }
        ajaxJS(data, (response) => {
            let responseText = response[1].data;
            this.workforceData = responseText.workforce_data;
            if(game.properties.building === 'crops') {
                for(let i = 0; i < responseText['crop_types'].length; i++) {
                    this.typeData[responseText['crop_types'][i]['crop_type']] = responseText['crop_types'][i];
                }    
            }
            else {
                responseText['mineral_types'];
                for(let i = 0; i < responseText['mineral_types'].length; i++) {
                    this.typeData[responseText['mineral_types'][i]['mineral_type']] = responseText['mineral_types'][i];
                }    
            }
            document.getElementById("workforce_amount").setAttribute("max", responseText.workforce_data.avail_workforce);
        });
    },
};
export default skillContainer;
