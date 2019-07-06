ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                var time = data[0] * 1000;
                var fetch = data[1];
                console.log(data);
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0){
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "Finished";
                    }
                }, 1000);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=miningarea" + "&method=checkCountdown");
        ajaxRequest.send();