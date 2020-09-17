    /*window.addEventListener("load", countdown());
    function countdown() {
        document.getElementById("travel").innerHTML = "Travelling done";
        var data = "model=Travel" + "&method=checkCountdown";
        ajaxJS(data, function(response) {
                var responseText = response[1].split("|");
                var time = responseText[0] * 1000;
                var info = "Currently travelling to:" + " ";
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("travel_time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    document.getElementById("travel").innerHTML = info + responseText[1];
                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("travel").innerHTML = "";
                        document.getElementById("travel_time").innerHTML = "Travelling done";
                        updateLocation(responseText[1]);
                    }
                }, 1000);
        });
    }*/
    var gameTravel = {
        seconds: 3, // Countdown for the countdown function
        intervalID: null, // intervalID to clear interval when countdown is finished;
        newDestination: function(destination) {
            
            /*if(conversation.index === null) {
                return;
            }*/
            gameTravel.intervalID = setInterval(
                function() {
                    if(gameTravel.seconds <= 0) {
                        clearInterval(gameTravel.intervalID);
                        gameTravel.seconds = 15;
                        game.canvasText.hideText();
                        game.loadWorld(false, false, "changeMap", [destination.toLowerCase()]);
                    }
                    else {
                        game.canvasText.showText('Travelling in ' + gameTravel.seconds, false, false);
                        gameTravel.seconds--;    
                    }
                }, 1000);
            
        }
    };
    /*function travel(destination) {
        /*var data = "model=Travel" + "&method=getData" + "&destination=" + destination;
        ajaxP(data, function(response) {
           if(response[0] != false) {
                countdown();
           }
        });
        if(conversation.index === null) {
            return;
        }
    }*/
    function updateLocation(destination) {
        var data = "model=Travel" + "&method=updateLocation" + "&destination=" + destination;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                if(location.href.indexOf("city") != -1) {
                    document.getElementById("city").innerHTML = response[1];     
                }           
            }
        });
    }
    