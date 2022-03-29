    if(document.getElementById("news_content").children[2] != null) {
        console.log('smith');
        var buttons = document.getElementById("smith").querySelectorAll("button");
        buttons.forEach(function(element) {
            // Add event for each element
            element.addEventListener('click', smith);
        });
    }
    function select(element) {
        let parent = element.parentNode;
        let img = parent.getElementsByTagName("IMG")[0];
        console.log(img);
        for(var i = 0; i < img.length; i++) {
            if(img[i].style.border != "1px solid red;") {
                img[i].style.border = "none";
            }
        }
        element.style.border = "1px solid red";
    }
    
    function showMineral(mineral) {
        var divs = ["iron", "steel", "gargonite", "adron", "yeqdon", "frajrite"];
        var minerals = document.getElementsByClassName("minerals");
        for(var i = 0; i < divs.length; i++) {
            if(divs[i] == mineral) {
                document.getElementById(divs[i]).style.display = "inline-block";
                minerals[i].style = "border: 2px solid brown";
            }
            else {
                document.getElementById(divs[i]).style = "display: none";
                minerals[i].style = "border: none"; 
            }   
        }
        newsContentSidebar.activeButton = "smith";
        newsContentSidebar.adjustMainContentHeight();
    }
    function smith() {
        var amount = event.target.parentElement.children[0].value;
        var item = event.target.closest("tr").querySelectorAll("figcaption")[0].innerHTML.toLowerCase();
        var minerals = document.getElementsByClassName("minerals");
        for(var i = 0; i < minerals.length; i++) {
            if(minerals[i].style.borderStyle == "solid") {
                this.mineral = minerals[i].getAttribute("title");
                break;
            }
        } 
        if(this.mineral.length < 1){
            gameLogger.addMessage("Please select a mineral");
            gameLogger.logMessages();
            return false;
        }
        event.target.parentElement.children[0].value = "";
        var data = "model=Smithy" + "&method=smith" + "&item=" + item  + "&amount=" + amount + "&mineral=" + this.mineral;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('smithy');
            }       
        });
    }