    addEventListener('load', function() {      
        conversation.conversationDiv = document.getElementById("conversation").querySelectorAll("ul")[0];
        document.getElementById("conversation_container").querySelectorAll("img")[0].addEventListener("click", () =>
                                                                                                      conversation.endConversation());
        /*conversation.toggleButton();*/
    });
    const conversation = {
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
        checkConversation() {
            if(document.getElementById("conversation_container").style.visibility == "visible") {
                return true;
            }
            else {
                return false;
            }
        },
        loadConversation(person, index, callback = false) {
            if(person.length <= 2) {
                return false;
            }
            /*if(event == null) {
                return false;
            }*/
            // If index is undefined, set it to false
            if(index == undefined) {
                index = false;
            }
            let h = document.createElement("h2");
            h.innerText = "Loading...";
            h.id = "loading_message";
            let conversation_container = document.getElementById("conversation_container");
            conversation_container.style.scale = "1";
            conversation_container.style.visibility = "visible";
            this.conversationDiv.appendChild(h);
            if(game.properties.device == "mobile") {
                let conversationContainerHeight = game.properties.canvasHeight * 0.40;
                if(conversationContainerHeight > 170) {
                    conversation_container.style.height = "170px";
                }
                else {
                    conversation_container.style.height = conversationContainerHeight + "px";
                }
                document.getElementById("conversation").style.height = conversation_container.offsetHeight - 32 + "px";
                // Set offsetTop for non pc device so that the bottom of conversation container is the same as game_canvas;
                conversation_container.style.top =
                    (document.getElementById("game_canvas").offsetTop + document.getElementById("game_canvas").height) -
                    conversation_container.offsetHeight + "px";
            }
            else {
                document.getElementById("conversation_container").style.top =
                document.getElementById("game_canvas").offsetTop + document.getElementById("game_canvas").height - 
                conversation_container.offsetHeight + "px";
            }
            
            data = "person=" + person.toLowerCase() + "&index=" + index;
            conversation.convAJAX(data, (response) => {

                if(response[1] === "ERROR") {
                    gameLog("That person is not interested in talking to you");
                    this.endConversation();
                }
                else {
                    console.log(this);
                    this.index = true;
                    console.log(response[1]);
                    this.activeDialogues = JSON.parse(response[1]);
                    console.log(this.buttonToggle);
                    if(this.buttonToggle == false) {
                        this.toggleButton();
                    }
                    this.makeLinks();
                    
                    // Set width on conversation so it fits between images by subtracting container width by both pictures
                    document.getElementById("conversation").style.width =
                    conversation_container.offsetWidth - (document.getElementById("conversation_a").width * 2)  - 17 + "px";
                    console.log(document.getElementById("conversation").style.width);
                    if(callback !== false) {
                        callback();
                    }
                }
            });
        },
        endConversation() {
            this.index = null;
            this.conversationDiv.innerHTML = "";
            document.getElementById("conversation_container").style.visibility = "hidden";
            document.getElementById("conversation_container").style.scale = "0.5";
            this.buttonToggle = false;
            this.button = null;
            document.getElementById("conversation_a").style.visibility = "hidden";
            document.getElementById("conversation_b").style.visibility = "hidden";
            document.getElementById("conversation").querySelectorAll("button")[0].style.visibility = "hidden";

            /*if(resume == true) {
                game.resumeGame();    
            }*/
        },
        toggleButton() {
            if(this.button === null) {
                if(document.getElementById("conversation").querySelectorAll("button").length > 0) {
                    this.button = document.getElementById("conversation").querySelectorAll("button")[0];
                    this.button.addEventListener("click", () => this.getNextLine());
                    this.button.style.visibility = "visible";
                    this.buttonToggle = true;
                }
                else {
                    this.button = document.createElement("button");
                    this.button.appendChild(document.createTextNode("Click here to continue"));
                    this.button.addEventListener("click", () => this.getNextLine());
                    document.getElementById("conversation").appendChild(this.button);
                    this.buttonToggle = true;
                }
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
            let text;
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
            let data = "person=set" +  "&index=" + text;
            this.convAJAX(data, (response) => {
                if(response[1] === "end") {
                    this.endConversation();
                    return false;
                }
                console.log(response[1]);
                this.activeDialogues = [];
                this.activeDialogues = JSON.parse(response[1]);
                if(this.activeDialogues.length == 1) {
                    let split = this.activeDialogues[0].split("|");
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
                    this.buttonToggle = true;
                    this.toggleButton();
                    this.makeLinks();
                    if(response[1].indexOf("newDestination") != -1) {
                        let lis = this.conversationDiv.querySelectorAll("li");
                        lis.forEach(function(element) {
                            // ... code code code for this one element
                            element.addEventListener('click', gameTravel.newDestination);
                        });
                    }
                }
                else {
                    console.log('inside hello');
                    /*if(conversation.activeDialogues[0][1] === "selectItem") {
                        console.log('Choose inventory');
                        conversation.buttonToggle = true;
                        conversation.toggleButton();
                    }*/
                    if(this.buttonToggle !== true) {
                        this.toggleButton();
                    }
                    switch(this.activeDialogues[0].split("|")[2]) {
                        case "Q":
                        case "q":
                        case "r":
                            this.makeLinks();
                            break;
                        case "end":
                            this.makeLinks();
                            document.getElementById("conversation").querySelectorAll("button")[0].
                                    removeEventListener("click",conversation.getNextLine);
                            document.getElementById("conversation").querySelectorAll("button")[0].addEventListener("click",
                                                                                                  this.endConversation);
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
        togglePerson(part) {
            console.log(part);
            if(part.indexOf("a") != -1) {
                let a = part.split(",")[1];
                document.getElementById("conversation_a").src = "public/images/" + a + ".png";
                document.getElementById("conversation_a").style.visibility = "visible";
                document.getElementById("conversation_b").style.visibility = "hidden";
            }
            else {
                document.getElementById("conversation_b").style.visibility = "visible";
                document.getElementById("conversation_a").style.visibility = "hidden";
            }
            if(part.indexOf("#") != -1) {
                console.log(person.split("#")[1].trim());
                document.getElementById("conversation_container").querySelectorAll("h3")[0].innerText = person.split("#")[1].trim();
            }
        },
        makeLinks() {
            let str = conversation.activeDialogues;
            this.conversationDiv.innerHTML = "";
            if(str.length > 1 === true) {
                for(var i = 0; i < str.length; i++) {
                    let strProperties = str[i].split("|");
                    this.togglePerson(strProperties[0]);
                    let li = document.createElement("li"); 
                    li.innerHTML = strProperties[1];
                    li.className = "conv_link";
                    li.style.cursor = "pointer";
                    li.addEventListener("click", () => this.getNextLine());
                    this.conversationDiv.appendChild(li);
                }
            }
            else {
                let strProperties = str[0].split("|");
                this.togglePerson(strProperties[0]);
                let li = document.createElement("li"); 
                li.innerHTML = strProperties[1];
                li.className = "conv_link";
                li.style.cursor = "auto";
                this.conversationDiv.appendChild(li);
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