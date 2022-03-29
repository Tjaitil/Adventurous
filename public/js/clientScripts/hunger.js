function getHunger() {
    var data = "model=Hunger" + "&method=getHunger";
    ajaxG(data, function(response) {
        if(response[0] != false) {
            let responseText = response[1];
            console.log(responseText);
            progressBar.calculateProgress(document.getElementById("hunger_progressBar"), responseText.newHunger, 100, true);
        }
    });
}
function updateHunger(newHunger) {
    newHunger = parseInt(newHunger);
    progressBar.calculateProgress(document.getElementById("hunger_progressBar"), newHunger, 100, false);


    // let time = Math.floor((new Date().getTime() - game.properties.startTime) / 1000);
    // var data = "model=Hunger" + "&method=updateHunger" + "&time=" + time;
    // ajaxP(data, function(response) {
    //     if(response[0] !== false) {
    //         let responseText = response[1];
    //         progressBar.calculateProgress(document.getElementById("hunger_progressBar"), responseText.newHunger, 100, false);
    //     }
    // });
}