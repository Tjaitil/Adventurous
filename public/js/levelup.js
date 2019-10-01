
    window.onload = levelUP();
    function levelUP() {
        var data = "model=LevelUp" + "&method=updateData";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() { 
            if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    var data = this.responseText.split("|");
                    var pos = data.indexOf("unlocked");
                    var unlocked = data.slice(0, pos);
                    console.log(data);
                    var count = 0;
                    var div = document.createElement("DIV");
                    div.setAttribute("id", "level_up");
                    div.innerHTML += this.responseText;

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
        };
        ajaxRequest.open("POST", "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }