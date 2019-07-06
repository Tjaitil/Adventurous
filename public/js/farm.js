    window.onload = xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var time = this.responseText * 1000;
                console.log(this.responseText);
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("crop_countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("crop_countdown").innerHTML = "Finished";
                    }
                }, 1000);
            }
        };
        xmlhttp.open("GET", "/handlers/handler_js.php?&model=farm" + "&method=checkCountdown");
        xmlhttp.send();
    
    
    
    // Toggle vision of DIVS containing information
    
    /*xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "/adventurous/handlers/handler.getworkforce.php?model=farmData" + "&method=getWorkforce");
    xmlhttp.send();
    xmlhttp.onload = function () {
        if(this.readyState == 4 && this.status == 200) {
            var a = this.responseText;
            var values = a.split("|");
            var workforce = Number(values[0]);
            var ava = Number(values[1]);
            document.getElementById("workforce_ava").innerHTML = ava;
            document.getElementById("workforce_tot").innerHTML= workforce;
            var decLength = ava / workforce;
            var fullLength = decLength * 100;
            document.getElementById("workforce_bar2").style.width = fullLength + "%";
        }
    };*/
    
    /*window.onload = function time () {
                        xmlhttp = new XMLHttpRequest();
                        xmlhttp.open("GET", "/adventurous/getTime.php");
                        xmlhttp.send();
                        xmlhttp.onload = function () {
                        if(this.readyState == 4 && this.status == 200) {
                               var time = this.responseText * 1000;
                                   var x = setInterval (function() {
                                   var now = new Date().getTime();
                                   var distance = time - now;
                                   var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                   var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                   var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                   var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                   document.getElementById("demo").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                                   if (distance < 0) {
                                    clearInterval(x);
                                    document.getElementById("demo").innerHTML = "Finished";
                                   }
                               }, 1000);
                               }
                            };
                        };*/
                    
    function showcaseCrops () {
        document.getElementById("crops_card").style.visibility = "visible";
    }
    function hideCrops () {
        document.getElementById("crops_card").style.visibility = "hidden";
    }
    function showcaseButchery () {
        document.getElementById("butchery_card").style.visibility = "visible";
    }
    function hideButchery () {
        document.getElementById("butchery_card").style.visibility = "hidden";
    }
    function showcaseWorkforce () {
        document.getElementById("workforce_card").style.visibility = "visible";
    }
    function hideWorkforce () {
        document.getElementById("workforce_card").style.visibility = "hidden";
    }