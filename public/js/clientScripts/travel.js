const gameTravel = {
    seconds: 14, // Countdown for the countdown function
    intervalID: null, // intervalID to clear interval when countdown is finished;
    newDestination(characterName) {
        if(event.target == null) {
            return false;
        }
        else {
            var destination = event.target.innerText.replace(" ", "-");
            let lis = conversation.conversationDiv.querySelectorAll("li");
                lis.forEach(function(element) {
                    element.removeEventListener('click', gameTravel.newDestination);
                });
        }
        let worldParameters = {'newxBase': false, 
                                'newyBase': false, 
                                'method': 'changeMap', 
                                'newMap': destination.toLowerCase()};
        canvasTextHeader.setDraw("Travelling in 15", 15);
        if(tutorial.onGoing) tutorial.exitTutorial();
        if(characterName.indexOf("sailor")) {
            worldParameters.StartPointType = true;
        } else {
            worldParameters.StartPointType = false;
        }
        setTimeout(() => game.loadWorld(worldParameters), 16000);
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
function updateLocation(destination) {
    let data = "model=Travel" + "&method=updateLocation" + "&destination=" + destination;
    ajaxP(data, function(response) {
        if(response[0] != false) {
            if(location.href.indexOf("city") != -1) {
                document.getElementById("city").innerHTML = response[1];     
            }           
        }
    });
}
