
    function validJSON(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
    function checkError(responseText) {
        let errorWords = ["ERROR", "error", "notice", "Exception", "exception", "Trace", "trace", "Warning"];
        let match = false;
        for(const i of errorWords) {
            if(responseText.includes(i)) {
                match = true;
                break;
            }
        }
        if(match === true) {
            return true;
        } else {
            return false;
        }
    }
    function ajaxG(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                let responseText = JSON.parse(this.responseText);
                checkResponse(responseText);
                if(checkError(this.responseText)) {
                    callback([false, responseText]);
                } else {
                    callback([true, responseText]);
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
                let responseText = JSON.parse(this.responseText);
                checkResponse(responseText);
                if(checkError(this.responseText)) {
                    callback([false, responseText]);
                    gameLogger.addMessage("ERROR!:");
                    console.log(this.responseText);
                    gameLogger.logMessages();
                } else {
                    callback([true, responseText]);
                }
            }
            else {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/" + file + ".php?" + data);
        ajaxRequest.send();
    }
    function ajaxP(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                let responseText = JSON.parse(this.responseText);
                checkResponse(responseText);
                if(checkError(this.responseText)) {
                    callback([false, responseText]);
                    // gameLogger.addMessage("ERROR Something unexpected happened!");
                    // gameLogger.logMessages();
                } else {
                    callback([true, responseText]);
                }
            }
            else {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    function checkResponse(responseText) {
        if(typeof(responseText.levelUP) !== "undefined" && Object.keys(responseText.levelUP).length > 0) {
            newLevel.update(responseText.levelUP);
        } 
        if(typeof(responseText.gameMessages) !== "undefined") {
            gameLogger.addMessage(responseText.gameMessages);
            gameLogger.logMessages();
        }
    }
    function diplomacy() {
        let data = "model=test" + "&method=diplomacy";
        ajaxG(data, function(response) {
            console.log(response);
        });
    }