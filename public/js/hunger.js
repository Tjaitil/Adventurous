function calculateHunger() {
    var data = "model=Hunger" + "&method=checkHunger";
    ajaxG(data, function(response) {
        if(response[0] != false) {
            let responseText = response[1];
            console.log(document.getElementById("hunger_progressBar"));
            progressBar.calculateProgress(document.getElementById("hunger_progressBar"), responseText, 100, false);
        }
    });
}
function updateHunger() {
    let time = Math.floor((new Date().getTime() - game.properties.startTime) / 1000);
    var data = "model=Hunger" + "&method=updateHunger" + "&time=" + time;
    ajaxP(data, function(response) {
        if(response[0] !== false) {
            let responseText = response[1];
            progressBar.calculateProgress(document.getElementById("hunger_progressBar"), responseText, 100, false);
        }
    });
}