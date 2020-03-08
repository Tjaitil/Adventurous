    // Module file
    function showSelect() {
        var element = event.target;
        var clone = element.cloneNode(true);
        clone.removeAttribute("onclick");
        var item = element.getAttribute("alt");
        var div = document.getElementById("data_form");
        div.style.visibility = "visible";
        console.log(div.children[1]);
        var div_inputs = div.querySelectorAll("input");
        
        div_inputs[0].value = jsUcfirst(item);
        div_inputs[1].value = this[item].time;
        if(window.location.href.indexOf("crop") != -1) {
            div_inputs[2].value = this[item].seeds;    
        }
        else {
            div_inputs[2].value = this[item].permits;
        }
        if(div.children[0].children.length == 0) {
            div.children[0].appendChild(clone);
        }
        else {
            clone.src = this[item].src;
        }
    }
    function getData(site) {
        
        var data = "model=" + "&method=" + "&site=" + site;
        ajaxG(data, function(response) {
            i    
        });
    }