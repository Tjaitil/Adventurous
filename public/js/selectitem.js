    function select(element) {
        var img = element.cloneNode(true);
        /*img.removeAttribute("onclick");*/
        img.children[0].style.height = "50px";
        img.children[0].style.width = "50px";
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(img);
    }
    
    function select_i(element) {
        document.getElementById("form_select").selectedIndex = 2;
        toggleType();
        console.log(toggleType);
        var text = element.children[1].innerHTML;
        var text_comb = text.split("x");
        if(text_comb[1].trim() === 'Gold') {
            gameLog("ERROR: You cannot sell gold!");
            return false;
        }
        document.getElementById("item_name").value = text_comb[1].trim();
        var img = element.cloneNode(true);
        img.removeChild(img.children[1]);
        img.removeAttribute("onclick");
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(img);
    }