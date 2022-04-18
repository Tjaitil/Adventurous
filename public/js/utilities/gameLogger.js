// Log gamemessages
const gameLogger = {
    messages: [],
    currentlyLogging: false,
    currentIndex: 0,
    addMessage(message) {
        if(Array.isArray(message)) {
            for(let i = 0; i < message.length; i++) {
                this.messages.push(message[i]);
            }
        }
        else {
            this.messages.push(message);
        }
    },
    logMessages() {
        if(this.messages.length === 0) return false;
        // Start new loop only if none is set
        if(!this.currentlyLogging) {
           this.clientLog();
        } 
        this.currentlyLogging = true;
    },
    mainLog() {
        let message = this.messages[this.currentIndex];
        let tr = document.createElement("TR");
        let td = document.createElement("TD");
        if(message.indexOf("ERROR") != -1) {
            message = message.split("ERROR")[1].trim();
            td.className = "error_log";
        }
        if(message.search("\\[") == -1) {
            var d = new Date();
            var time = "[" + addZero(d.getHours()) + ":" + addZero(d.getMinutes()) + ":" + addZero(d.getSeconds()) + "] ";
            message = time + message;
        }
        let table = document.getElementById("game_messages");
        tr.appendChild(td);
        td.innerHTML = message;
        let logElement = document.getElementById("log");
        let isScrolledToBottom = logElement.scrollHeight - logElement.clientHeight <= logElement.scrollTop + 1;
        table.appendChild(tr);
        // scroll to bottom if isScrolledToBottom
        if (isScrolledToBottom) {
            logElement.scrollTop = logElement.scrollHeight - logElement.clientHeight;
        }
    },
    clientLog() {
        let message = this.messages[this.currentIndex];
        let div = document.getElementById("log_2");
        div.innerHTML = message;
        div.style.opacity = 1;
        div.style.height = "50px";
        div.style.top = window.pageYOffset + 5 + "px";
        this.mainLog();
        setTimeout(() => {
            document.getElementById("log_2").style.height = "4px";
        }, 3700);
        setTimeout(() => {
            if(this.currentIndex !== this.messages.length - 1) {
                this.currentIndex++;
                this.clientLog();
            } else {
                this.closeClientLog();
            }
        }, 4000);
    },
    closeClientLog() {
        let div = document.getElementById("log_2");
        div.style.height = "4px";
        div.style.top = "0px"
        div.style.opacity = 0;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_log.php?log=" + JSON.stringify(this.messages));
        ajaxRequest.send();
        this.messages = [];
        this.currentIndex = 0;
        this.currentlyLogging = false;
    }
};
