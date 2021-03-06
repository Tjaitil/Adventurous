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
        seconds: 14, // Countdown for the countdown function
        intervalID: null, // intervalID to clear interval when countdown is finished;
        newDestination: function() {
            if(event.target == null) {
                return false;
            }
            else {
                var destination = event.target.innerText;
                let lis = conversation.conversationDiv.querySelectorAll("li");
                    lis.forEach(function(element) {
                        // ... code code code for this one element
                        element.removeEventListener('click', gameTravel.newDestination);
                    });
            }
            canvasTextHeader.setDraw("Travelling in 15", 15);
            setTimeout(() => game.loadWorld(false, false, "changeMap", [destination.toLowerCase()]), 16000);
            this.intervalID = setInterval(() => {
                    if(gameTravel.seconds <= 0) {
                        clearInterval(gameTravel.intervalID);
                    }
                    else {
                        canvasTextHeader.text = "Travelling in " + gameTravel.seconds;
                        canvasTextHeader.draw();
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
    