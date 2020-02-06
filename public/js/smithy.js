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
                minerals[i].style = "border: 2px solid brown";
            }
            else {
                document.getElementById(divs[i]).style = "display: none";
                minerals[i].style = "border: none"; 
            }   
        }
    }
