    function select(element) {
        var parent = element.parentNode;
        var img = parent.getElementsByTagName("IMG");
        console.log(img);
        for(var i = 0; i < img.length; i++) {
            if(img[i].style.border != "1px solid red;") {
                img[i].style.border = "none";
            }
        }
        element.style.border = "1px solid red";
    }
    
    function showMineral(mineral, element) {
        var divs = ["iron", "steel", "gargonite", "adron", "yeqdon", "frajrite"];
        var minerals = document.getElementsByClassName("minerals");
        for(var i = 0; i < divs.length; i++) {
            if(divs[i] == mineral) {
                document.getElementById(divs[i]).style = "display: inline";
                minerals[i].style = "border: 1px solid red";
            }
            else {
                document.getElementById(divs[i]).style = "display: none";
                minerals[i].style = "border: none"; 
            }   
        }
    }
    
    function smith(item, parent, mineral = false) {
        var minerals = document.getElementsByClassName("minerals");
        var amount = parent.children[0].value;
        if(mineral != false) {
            for(var i = 0; i < minerals.length; i++) {
                if(minerals[i].style.border == "1px solid red") {
                    this.mineral = minerals[i].getAttribute("title");
                    break;
                }
            } 
            if(this.mineral.length < 1){
                gameLog("Please select a mineral");
                return false;
            }  
        }
        ajaxRequest = new XMLHttpRequest();
        var data = "model=smithy" + "&method=smith" + "&item=" + item  + "&amount=" + amount + "&mineral=" + this.mineral;
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    updateInventory('smithy');
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data); 
    }
