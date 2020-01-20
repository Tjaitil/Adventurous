    if(document.getElementById("inventory") != null) {
        addSelectEvent(false);
    }
    function select(element) {
        var img = element.cloneNode(true);
        /*img.removeAttribute("onclick");*/
        img.children[0].style.height = "50px";
        img.children[0].style.width = "50px";
        img.children[1].style.visibility = "hidden";
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(img);
        if(window.location.href.search("armory") != -1) {
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
        var figures = document.getElementById("inventory").querySelectorAll('figure');
        figures.forEach(function(element) {
            element.addEventListener('click', function() {
                var location = window.location.toString();
                if(location.indexOf("market") == -1) {
                    console.log("add");
                    select(element);
                }
                else {
                    select_i();
                }
            });
        });
    }