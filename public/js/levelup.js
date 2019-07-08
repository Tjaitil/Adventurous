    
    ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function() {
        if(this.readyState == 4 && this.status == 200) {
            if(this.responseText.length > 3) {
                gameLog(this.responseText);
                console.log(this.responseText);
                var hello = this.responseText.split("|");
                console.log(hello);
                var pos = hello.indexOf("unlocked");
                var data = hello.slice(0, pos);
                console.log(data);
                var img = document.createElement("IMG");
                img.setAttribute("src", "/public/images/" + hello[0] + ".jpg");
                var count = 0;
                var news = document.getElementById("news");
                news.style = "visibility: visible;";
                for(var i = 0; i < data.length; i++) {
                    var img = document.createElement("IMG");
                    img.setAttribute("src", "/public/images/" + hello[i] + ".jpg");
                    news.appendChild(img);
                    count++;
                }
                document.getElementById("news").innerHTML = this.responseText;
            }
        }
    };
    ajaxRequest.open("GET", "handlers/handler_js.php?model=LevelUp" + "&method=updateData");
    ajaxRequest.send();
    