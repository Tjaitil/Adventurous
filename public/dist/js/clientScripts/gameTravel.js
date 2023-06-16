import { Game } from '../advclient.js';
import { tutorial } from "./tutorial.js";
import { canvasTextHeader } from "./canvasText.js";
import { ajaxP } from "../ajax.js";
export const gameTravel = {
    seconds: 14,
    intervalID: null,
    newDestination(event, characterName) {
        if (event.target == null) {
            return false;
        }
        let targetElement = event.currentTarget;
        let destination = targetElement.innerText.replace(" ", "-");
        canvasTextHeader.setDraw("Travelling in 15", 15);
        if (tutorial.onGoing)
            tutorial.exitTutorial();
        let startPointType = false;
        // If character is sailor then draw player at dock
        if (characterName.indexOf("sailor")) {
            startPointType = true;
        }
        else {
            startPointType = false;
        }
        setTimeout(() => Game.setWorld({
            method: "changeMap",
            newDestination: destination.toLowerCase(),
            startPointType
        }), 16000);
        this.intervalID = setInterval(() => {
            if (gameTravel.seconds <= 0) {
                clearInterval(gameTravel.intervalID);
            }
            else {
                canvasTextHeader.text = "Travelling in " + gameTravel.seconds;
                canvasTextHeader.draw();
                gameTravel.seconds--;
            }
        }, 1000);
        return true;
    },
};
function updateLocation(destination) {
    let data = "model=Travel" + "&method=updateLocation" + "&destination=" + destination;
    ajaxP(data, function (response) {
        if (response[0] != false) {
            if (location.href.indexOf("city") != -1) {
                document.getElementById("city").innerHTML = response[1];
            }
        }
    });
}
