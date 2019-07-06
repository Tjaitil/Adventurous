    /*ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if(this.readyState == 4 && this.status == 200) {
            x = document.getElementById("announcement");
            x.innerHTML = this.responseText;
            x.style.visibility = "visible";
            document.querySelector("body").style = "background-color:black";
            document.querySelector("HEADER").style = "opacity:0.1; background-color:black;";
            document.querySelector("SECTION").style = "background-color:black; opacity:0.1;";
            document.querySelector("ASIDE").style = "background-color:black; opacity:0.1;";
        }
    };
    ajaxRequest.open('GET', "handlers/handler_ses.php?variable=level_up_data");
    ajaxRequest.send();*/
        
    function exit() {
        x = document.getElementById("announcement");
        x.style.visibility = "hidden";
        document.querySelector("HEADER").style = "opacity:1";
        document.querySelector("SECTION").style = "opacity:1;";
        document.querySelector("ASIDE").style = "opacity:1;";
    }