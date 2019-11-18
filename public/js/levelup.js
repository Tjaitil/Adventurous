
    window.onload = levelUP();
    function levelUP() {
        var data = "model=LevelUp" + "&method=updateData";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                console.log(response[1]);
                data = response[1].split("|");
                var pos = data.indexOf("unlocked");
                var unlocked = data.slice(0, pos);
                console.log(data);
                var count = 0;
                var div = document.createElement("DIV");
                div.setAttribute("id", "level_up");
                div.innerHTML += response[1];

                if(unlocked.length > 0 ) {
                    for(var i = 0; i < unlocked.length; i++) {
                        var img = document.createElement("IMG");
                        img.setAttribute("src", "/public/images/" + hello[i] + ".jpg");
                        div.appendChild(img);
                        count++;
                    }
                }
                else {
                    var element = document.createElement("p");
                    element.innerHTML = "Nothing new at this level";
                    div.appendChild(element);
                }
                openNews(div);
            }       
        });
    }