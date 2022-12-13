import { HUD } from './HUD.js';
import { ajaxG } from '../ajax.js';

export function getHunger() {
    var data = "model=Hunger" + "&method=getHunger";
    ajaxG(data, function(response) {
        if(response[0] != false) {
            let responseText = response[1];
            console.log(responseText);
            HUD.elements.hungerProgressBar.setCurrentValue(responseText.newHunger);
        }
    });
}
export function updateHunger(newHunger) {
    HUD.elements.hungerProgressBar.setCurrentValue(newHunger);

    // let time = Math.floor((new Date().getTime() - game.properties.startTime) / 1000);
    // var data = "model=Hunger" + "&method=updateHunger" + "&time=" + time;
    // ajaxP(data, function(response) {
    //     if(response[0] !== false) {
    //         let responseText = response[1];
    //         progressBar.calculateProgress(document.getElementById("hunger_progressBar"), responseText.newHunger, 100, false);
    //     }
    // });
}