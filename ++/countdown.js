
    function update () {
        xmlhttp3 = new XMLHttpRequest();
        xmlhttp3.open("GET", "/adventurous/handlers/handler.updatecrops.php");
        xmlhttp3.send();
        xmlhttp3.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("demo").innerHTML = "Finished";
            }       
        };
        }
 