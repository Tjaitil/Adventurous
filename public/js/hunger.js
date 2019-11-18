function calculateHunger() {
    var data = "model=Hunger" + "&method=checkHunger";
    ajaxG(data, function(response) {
        if(response[0] != false) {
            var responseText = response[1];
            console.log(Number(responseText));
            console.log("hunger");
            var sum = 12.5;
            var spans = document.getElementById("hunger").querySelectorAll("span");
            for(var i = 0; i < 8; i++) {
                if(sum < responseText) {
                    spans[i].style.backgroundColor = "blue";
                }
                else {
                    spans[i].style.backgroundColor = "transparent";
                }
                sum+= 12.5;
            }
        }
    });
}

setTimeout(calculateHunger, 3000);