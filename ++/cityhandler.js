    var overview = {
        "towhar": "towhar.php",
        "golbak": "golbak.php",
        "snerpiir": "snerpiir.php",
        "krasnur" : "krasnur.php",
        "tasnobil": "tasnobil.php",
        "cruendo": "cruendo.php",
        'fagna': 'fagna.php',
    };
    console.log("Hello");
    xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "/adventurous/handlers/handler.getcity.php");
    xmlhttp.send();
    xmlhttp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
                 if (overview.hasOwnProperty(this.responseText) == true) {
                    console.log(overview[this.responseText]);
                    document.getElementById("town_map_a").href = overview[this.responseText];
                    document.getElementsByClassName("bar_city").href = overview[this.responseText]; //Fikse at funksjonen ikke endrer URLen på bar_city classene
                    document.getElementById("town").innerHTML = this.responseText; //Få stor bokstav på første bokstav
                 }
                else {
                    return;
                }
        }
    };