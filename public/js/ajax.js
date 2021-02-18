function ajaxG(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    if(log == true) {
                        gameLog(this.responseText);
                    }
                    callback([false, this.responseText]);
                }
                else {
                    callback([true, this.responseText]);
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?" + data);
        ajaxRequest.send();
    }
    function ajaxJS(data, callback, log = true, file = false) {
        if(file == false) {
            file = "handler_js";
        }
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    if(log == true) {
                        gameLog(this.responseText);
                    }
                    callback([false, this.responseText]);
                }
                else {
                    callback([true, this.responseText]);
                }
            }
        };
        ajaxRequest.open('GET', "handlers/" + file + ".php?" + data);
        ajaxRequest.send();
    }
    function ajaxP(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    if(log == true) {
                        gameLog(this.responseText);
                    }
                    callback([false, this.responseText]);
                }
                else {
                    callback([true, this.responseText]);
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }