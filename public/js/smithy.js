    window.onload = function () {
        var buttons = document.getElementById("smith").querySelectorAll("button");
        buttons.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                smith();
            });
        });
    };
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
    
    function smith() {
        var amount = event.target.parentElement.children[0].value;
        var item = event.target.closest("tr").children[0].innerHTML.toLowerCase();
        console.log(item);
        var minerals = document.getElementsByClassName("minerals");
        console.log(minerals);
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
        var data = "model=smithy" + "&method=smith" + "&item=" + item  + "&amount=" + amount + "&mineral=" + this.mineral;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('smithy');
            }       
        });
    }
