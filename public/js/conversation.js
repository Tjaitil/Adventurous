    addEventListener('load', function() {      
        conversation.conversationDiv = document.getElementById("conversation").querySelectorAll("ul")[0];
        /*conversation.toggleButton();*/
    });
    
    var conversation = {
        index: null,
        conversationDiv: null,
        button: null,
        buttonToggle: false,
        selectItem: false,
        activeDialogues: [],
        convAJAX(data, callback) {
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    callback([false, this.responseText]);
                }
            };
            ajaxRequest.open('POST', "handlers/handler_con.php");
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);
        },
        loadConversation(person) {
            if(person.length <= 2) {
                return false;
            }
            /*if(event == null) {
                return false;
            }*/
            data = "person=" + person.toLowerCase() + "&index=" + false;
            conversation.convAJAX(data, function(response) {
                console.log(response);
                conversation.activeDialogues = JSON.parse(response[1]);
                console.log(conversation.activeDialogues);
                if(conversation.buttonToggle == false) {
                    conversation.toggleButton();
                }
                conversation.makeLinks();
                document.getElementById("conversation_container").style.visibility = "visible";
                document.getElementById("conversation_container").style.top =
                    document.getElementById("game_canvas").offsetTop + 250 + "px";
            });
        },
        endConversation() {
            this.index = "none";
            conversation.conversationDiv.innerHTML = "";
            document.getElementById("conversation_container").style.visibility = "hidden";
        },
        toggleButton() {
            if(this.button === null) {
                this.button = document.createElement("button");
                this.button.appendChild(document.createTextNode("Click here to continue"));
                this.button.addEventListener("click", conversation.getNextLine);
                document.getElementById("conversation").appendChild(this.button);
            }
            else if(this.buttonToggle === false) {
                document.getElementById("conversation").querySelectorAll("button")[0].style.visibility = "visible";
                document.getElementById("conversation_container").querySelectorAll("h3")[0].innerText = "";
                this.buttonToggle = true;
            }
            else {
                document.getElementById("conversation").querySelectorAll("button")[0].style.visibility = "hidden";
                document.getElementById("conversation_container").querySelectorAll("h3")[0].innerText = "Select an answer";
                this.buttonToggle = false;
            }
        },
        getNextLine(info = false) {
            var text;
            
            if(event === null && info == false) {
                return;
            }
            if(event.target.tagName === "BUTTON") {
                text = document.getElementById("conversation").querySelectorAll("li")[0].innerText;
            }
            else if(event.target.tagName !== "LI" && info !== false) {
                text = info + '|';
            }
            else {
                text = event.target.innerText;
            }
            console.log(text);
            data = "person=" + null +  "&index=" + text;
            conversation.convAJAX(data, function(response) {
                console.log(response[1]);
                if(response[1] === "end") {
                    conversation.endConversation();
                    return false;
                }
                conversation.activeDialogues = [];
                conversation.activeDialogues = JSON.parse(response[1]);
                if(conversation.activeDialogues.length == 1) {
                    let split = conversation.activeDialogues[0].split("|");
                    if(typeof(split[3]) !== "undefined") {
                        if(split[3].indexOf(".") != -1) {
                            let objArray = split[3].split(".");
                            let objName = objArray[0];
                            let funcName = objArray[1];
                            window[objName][funcName]();
                        }
                        else {
                            window[split[3]]();    
                        }
                    }
                }
                if(conversation.activeDialogues.length > 1) {
                    conversation.buttonToggle = true;
                    conversation.toggleButton();
                    conversation.makeLinks();
                }
                else {
                    /*if(conversation.activeDialogues[0][1] === "selectItem") {
                        console.log('Choose inventory');
                        conversation.buttonToggle = true;
                        conversation.toggleButton();
                    }*/
                    if(conversation.buttonToggle !== true) {
                        conversation.toggleButton();
                    }
                    switch(conversation.activeDialogues[0].split("|")[2]) {
                        case "Q":
                            conversation.makeLinks();
                            break;
                        case "q":
                            conversation.makeLinks();
                            break;
                        case "r":
                            conversation.makeLinks();
                            break;
                        case "end":
                            conversation.makeLinks();
                            document.getElementById("conversation").querySelectorAll("button")[0].removeEventListener("click", getNextLine);
                            document.getElementById("conversation").querySelectorAll("button")[0].addEventListener("click",
                                                                                                  conversation.endConversation);
                            break;
            
                    }
                }
            });
            /*let nextIndex;
            for(var i = 0; i < conversation.activeDialogues.length; i++) {
                if(conversation.activeDialogues[i].indexOf(text) != -1) {
                    if(conversation.activeDialogues[i][2] === "end") {
                        conversation.endConversation();
                    }
                    if(typeof conversation.activeDialogues[i][3] !== "undefined") {
                        if(conversation.activeDialogues[i][3].indexOf(".") != -1) {
                            let objArray = conversation.activeDialogues[i][3].split(".");
                            let objName = objArray[0];
                            let funcName = objArray[1];
                            window[objName][funcName]();
                        }
                        else {
                            window[conversation.activeDialogues[i][3]]();    
                        }
                    }
                    console.log(conversation.activeDialogues);
                    nextIndex = conversation.activeDialogues[i][2];
                    break;
                }
            }
            console.log(nextIndex);
            if(nextIndex === null) {
                conversation.index =  conversation.index.slice(0, conversation.index.lastIndexOf("Q"));
            }
            console.log(conversation.index + "+" + nextIndex);
            conversation.index = conversation.index + nextIndex;
            console.log(conversation.index);*/
    
        },
        togglePerson(person) {
            console.log(person);
            console.log(person.indexOf("#"));
            if(person ==="a") {
                document.getElementById("conversation_a").visibility = "hidden";
            }
            else if(person.indexOf("#") != -1) {
                console.log(person.split("#")[1].trim());
                document.getElementById("conversation_container").querySelectorAll("h3")[0].innerText = person.split("#")[1].trim();
            }
            else {
                document.getElementById("conversation_a").visibility = "visible";
            }
            
        },
        makeLinks() {
            let str = conversation.activeDialogues;
            console.log(str);
            conversation.conversationDiv.innerHTML = "";
            if(str.length > 1 === true) {
                for(var i = 0; i < str.length; i++) {
                    let strProperties = str[i].split("|");
                    this.togglePerson(strProperties[0]);
                    let li = document.createElement("li"); 
                    li.innerHTML = strProperties[1];
                    li.className = "conv_link";
                    li.style.cursor = "pointer";
                    li.addEventListener("click", conversation.getNextLine);
                    conversation.conversationDiv.appendChild(li);
                }
            }
            else {
                let strProperties = str[0].split("|");
                this.togglePerson(strProperties[0]);
                let li = document.createElement("li"); 
                li.innerHTML = strProperties[1];
                li.className = "conv_link";
                li.style.cursor = "auto";
                conversation.conversationDiv.appendChild(li);
            }
        }
    };
    function selectItem() {
        document.getElementById("conversation_container").querySelectorAll("h3")[0].innerText = "Select an answer";
    }
    highlightInventory = {
        intervalID: null,    
        set() {
            this.intervalID = setInterval(function() {
                let inventory = document.getElementById("inventory");
                if(inventory.style.backgroundColor === "" || inventory.style.backgroundColor == "rgb(76, 52, 26)") {
                    inventory.style.backgroundColor = "#986834";
                }
                else {
                    inventory.style.backgroundColor = "#4c341a";
                } 
            }, 1500);
        },
        clear() {
            document.getElementById("inventory").style.backgroundColor = "#4c341a";
            clearInterval(this.intervalID);
        }
    };