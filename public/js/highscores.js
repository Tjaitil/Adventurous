    window.addEventListener("load", function() {
        // Make section cover whole width of page by setting the gridRow to 1 / 3 from 2 / 3. See layout.css
        let divWrapper = document.getElementsByClassName("wrapper")[0];
        divWrapper.style.gridTemplateColumns = "auto";
        document.getElementsByTagName("section")[0].style.gridRow = "1 / 3";
    });
    function gethighscores(type) {
        var data = "model=" + "&method=";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                data = response[1].split("||");
                var table = document.getElementById("highscores");
                table.removeChild(table.childNodes[1]);
                var tr = document.createElement("TR");
                for(var i = 0; i < data.length; i++) {
                    datapieces = data[i].split("|");
                    document.getElementById("rows").appendChild();
                    for(var x = 0; x < datapieces.length; x++) {
                        var element = document.createElement("TD");
                        element.innerHTML = datapieces[i];
                        tr.appendChild(element);
                    }
                    table.appendChild(tr);
                }
            }
        });
    }
    