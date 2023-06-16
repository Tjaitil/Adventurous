// Object containing common game messages
export var commonMessages;
(function (commonMessages) {
    commonMessages["inventoryFull"] = "Remove some items from inventory before doing this action";
})(commonMessages || (commonMessages = {}));
// Log gamemessages
export const gameLogger = {
    messages: [],
    currentlyLogging: false,
    currentIndex: 0,
    addMessage(message, instantLog = false) {
        if (Array.isArray(message)) {
            for (let i = 0; i < message.length; i++) {
                this.messages.push(message[i]);
            }
        }
        else {
            this.messages.push(message);
        }
        // Use to start this.logMessages instead of having to call it directly in another file 
        if (instantLog)
            this.logMessages();
    },
    logMessages() {
        if (this.messages.length === 0)
            return false;
        // Start new loop only if none is set
        console.log(this.currentlyLogging);
        if (!this.currentlyLogging) {
            this.clientLog();
        }
        this.currentlyLogging = true;
    },
    mainLog() {
        function addZero(num) {
            let str = num + "";
            if (num < 10) {
                str = "0" + num;
            }
            return str;
        }
        let message = this.messages[this.currentIndex];
        let tr = document.createElement("TR");
        let td = document.createElement("TD");
        if (message.indexOf("ERROR") != -1) {
            message = message.split("ERROR")[1].trim();
            td.className = "error_log";
        }
        if (message.search("\\[") == -1) {
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
        console.log('clientlog');
        let message = this.messages[this.currentIndex];
        let div = document.getElementById("log_2");
        div.innerHTML = message;
        div.style.opacity = "1";
        div.style.height = "50px";
        div.style.top = window.pageYOffset + 5 + "px";
        // TODO: Fix main log
        // this.mainLog();
        setTimeout(() => {
            console.log('log_2');
            document.getElementById("log_2").style.height = "4px";
        }, 3700);
        setTimeout(() => {
            if (this.currentIndex !== this.messages.length - 1) {
                this.currentIndex++;
                this.clientLog();
            }
            else {
                this.closeClientLog();
            }
        }, 4000);
    },
    closeClientLog() {
        let div = document.getElementById("log_2");
        div.style.height = "4px";
        div.style.top = "0px";
        div.style.opacity = "0";
        let ajaxRequest = new XMLHttpRequest();
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
