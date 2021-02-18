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
        var div_inputs = div.querySelectorAll("input");
        
        div_inputs[0].value = jsUcfirst(item);
        div_inputs[1].value = typeData[item].time;
        
        if(document.getElementsByClassName("page_title")[0].innerText == "Crops") {
            console.log('crops');
            div_inputs[2].value = typeData[item].seeds;    
        }
        else {
            div_inputs[2].value = typeData[item].permits;
        }
        console.log(div.children[0].children);
        if(div.children[0].children.length == 0) {
            div.children[0].appendChild(clone);
        }
        else {
            div.children[0].children[0].src = typeData[item].src;
            div.children[0].children[0].alt = item;
        }
    }
    function getData(site) {
        
        var data = "model=" + "&method=" + "&site=" + site;
        ajaxG(data, function(response) {
            i    
        });
    }