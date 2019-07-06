    function buyWorker(type, level = false) {
        var data = "model=buyworker" + "&method=buyWorker" + "&type=" + type + "&type_level=" + level;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR") != -1) {
                    gameLog(this.responseText);
                }
            }
        };
        ajaxRequest.open("POST", "/handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function talk(person, part) {
        document.getElementById("curtain").style.display = "block";
        document.getElementById("conversation").style.display = "block";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.search("ERROR:") != -1) {
                    gameLog(this.responseText);
                 }
                else {
                    console.log(this.responseText);
                    var data = this.responseText.split("|");
                    console.log(data);
                    if(data[1].search("<") != -1) {
                        document.getElementById("conv_button").style.visibility = "hidden";
                    }
                    
                    document.getElementById(data[0]).style.visibility = "hidden";
                    document.getElementById("conv").innerHTML = data[1];  
                }   
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=talk" + "&method=talk" + "&person=" + person + "&part=" + part);
        ajaxRequest.send();
        
        
    }   
    
    function close() {
        document.getElementById("curtain").style.display = "none";
        document.getElementById("conversation").style.display = "none";
    }