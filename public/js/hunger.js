function calculateHunger() {
    var data = "model=Hunger" + "&method=checkHunger";
    ajaxG(data, function(response) {
        if(response[0] != false) {
            let responseText = response[1];
            document.getElementById("hunger_bar").querySelectorAll(".progress_value1")[0].innerText = responseText;
            let count = 0;
            /*var x = setInterval(function() {
                console.log(count++); 
            }, 1000);*/
            let width = (responseText / 100) * 100;
            document.getElementById("hunger_bar2").style.width = width + "%";
            console.log('hunger');
        }
    });
}
function updateHunger() {
    let time = Math.floor((new Date().getTime() - game.properties.startTime) / 1000);
    var data = "model=Hunger" + "&method=updateHunger" + "&time=" + time;
    ajaxP(data, function(response) {
        if(response[0] !== false) {
            let responseText = response[1];
            document.getElementById("hunger_bar").querySelectorAll(".progress_value1")[0].innerText = responseText;
            let width = (responseText / 100) * 100;
            document.getElementById("hunger_bar2").style.width = width + "%";
        }
    });
}