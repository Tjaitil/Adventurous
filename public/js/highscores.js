    
    function gethighscores(type) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("||");
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
        ajaxRequest.open('GET', "handlers/handler_g.php?model=" + "&method=");
        ajaxRequest.send();
    };
    