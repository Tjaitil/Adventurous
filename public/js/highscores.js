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
    