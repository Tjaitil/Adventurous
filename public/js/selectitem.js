    /*if(document.getElementById("inventory") != null) {
        addSelectEvent(false);
    }*/
    function select() {
        let element = event.target.closest("figure");
        var img = element.cloneNode(true);
        console.log(img);
        /*img.removeAttribute("onclick");*/
        img.children[0].style.height = "50px";
        img.children[0].style.width = "50px";
        img.children[1].style.visibility = "hidden";
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(img);
        if(document.getElementsByClassName("page_title")[0].innerText == "armory") {
            toggleOption();
        }
    }
    function select_i() {
        var element = event.target.closest("figure");
        toggleType();
        var item = element.children[1].innerHTML.toLowerCase().trim();
        if(item === 'gold') {
            gameLog("ERROR: You cannot sell gold!");
            return false;
        }
        document.getElementById("item_name").value = jsUcWords(item);
        var img = element.cloneNode(true);
        img.removeChild(img.children[1]);
        img.removeAttribute("onclick");
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(img);
    }
    function addSelectEvent() {
        console.log('addSelectEvent');
        let figures = document.getElementById("inventory").querySelectorAll('figure');
        figures.forEach(function(element) {
            if(document.getElementsByClassName("page_title")[0].innerText !== "market") {
                element.addEventListener('click', select);
            }
            else {
                element.addEventListener('click', select_i);
            }
        });
    }