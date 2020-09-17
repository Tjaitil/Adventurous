    addEventListener('load', function() {
        let test = "QerlaQakskkfQ"
        var hello = "qert";
        console.log(test.split("Q"));
      
        conversation.conversationDiv = document.getElementById("conversation").querySelectorAll("ul")[0];
        /*conversation.toggleButton();*/
    });
    
    /*
     Last letter in the index determines what type the last interaction was. For example "hfrq2" is answer to question 2
     
     Q - main questions
     q - question
     r - response
     rr - random response
     1-9 - number of response/question
     
     person|conversation|nextIndex|Funcname/other/end
    
    
    */
    
    
    
    
    var dialogues = [];
    dialogues['hfQ'] = "a|Hello, I am Harfen. Who are you?|r";
    dialogues['hfQr'] = ["b|My name is ...|Q1|wertyd()","b|None of your business|r1"];
    dialogues['hfQrQ1'] = "a|What can i do for you?|r";
    dialogues['hfQrQ1r'] = [
        "b|Tell me about yourself|r",
        "b|I'd like to look at your merchant|FuncName|End"
    ];
    dialogues['hfQrr1'] = "a|Well stranger, then we don't have more to talk about.|end"
    dialogues['ps'] = "a|Hello, what can i do you for sir?|r";
    dialogues['psr'] = ["b|I would like to travel with your cart|r", "b|Bye|end"];
    dialogues['psrr'] = ["b|Fansal Plains|end|travel",
                         "b|Golbak|end|gameTravel.newDestination",
                         "b|Hirtam|end|gameTravel.newDestination",
                         "b|Khanz|end|gameTravel.newDestination",
                         "b|Krasnur|end|gameTravel.newDestination",
                         "b|Tasnobil|end|gameTravel.newDestination",
                         "b|Towhar|end|gameTravel.newDestination"];
    
    var conversation = {
        index: null,
        conversationDiv: null,
        button: null,
        buttonToggle: false,
        activeDialogues: [],
        startConversation() {
            this.toggleButton();
            this.index = "ps";
            this.makeLinks();
            document.getElementById("conversation_container").style.visibility = "visible";
            document.getElementById("conversation_container").style.top = window.pageYOffset + 100 + "px";
        },
        endConversation() {
            this.index = "none";
            conversation.conversationDiv.innerHTML = "";
            document.getElementById("conversation_container").style.visibility = "hidden";
        },
        toggleButton() {
            if(this.button == null) {
                this.button = document.createElement("button");
                this.button.appendChild(document.createTextNode("Click here to continue"));
                this.button.addEventListener("click", conversation.getNextLine);    
            }
            if(this.buttonToggle === false) {
                document.getElementById("conversation").appendChild(this.button);
                this.buttonToggle = true;
            }
            else {
                document.getElementById("conversation").removeChild(this.button);
                this.buttonToggle = false;
            }
        },
        checkLine() {
            let text = event.target.innerText;
            let textIndex = dialogues.indexOf(test);
        },
        getNextLine() {
            let text;
            console.log(event.target);
            if(event.target.tagName === "BUTTON") {
                text = document.getElementById("conversation").querySelectorAll("li")[0].innerText;
            }
            else {
                text = event.target.innerText;    
            }
            console.log(text);
            let nextIndex;
            for(var i = 0; i < conversation.activeDialogues.length; i++) {
                if(conversation.activeDialogues[i].indexOf(text) != -1) {
                    if(conversation.activeDialogues[i][2] === "end") {
                        conversation.endConversation();
                    }
                    if(typeof conversation.activeDialogues[i][3] !== "undefined") {
                        window[conversation.activeDialogues[i][3]]();
                    }
                    console.log(conversation.activeDialogues);
                    nextIndex = conversation.activeDialogues[i][2];
                    break;
                }
            }
            console.log(nextIndex);
            if(nextIndex === undefined) {
                conversation.index =  conversation.index.slice(0, conversation.index.lastIndexOf("Q"));
            }
            console.log(conversation.index + "+" + nextIndex);
            conversation.index = conversation.index + nextIndex;
            console.log(conversation.index);
            if(Array.isArray(dialogues[conversation.index]) === true) {
               conversation.makeLinks();
                if(this.buttonToggle === false) {
                    this.toggleButton();
                }
            }
            else {
                switch(nextIndex.slice(0,1)) {
                    case "Q":
                        conversation.makeLinks();
                        break;
                    case "q":
                        break;
                    case "r":
                        conversation.makeLinks();
                    break;
            
               }
            }
        },
        togglePerson(person) {
            if(person ==="a") {
                document.getElementById("conversation_a").visibility = "none";
            }
            else {
                document.getElementById("conversation_a").visibility = "visible";
            }
            
        },
        makeLinks() {
            let str = dialogues[conversation.index];
            conversation.conversationDiv.innerHTML = "";
            if(Array.isArray(str) === true) {
                for(var i = 0; i < str.length; i++) {
                    let strProperties = str[i].split("|");
                    this.togglePerson(strProperties[0]);
                    conversation.activeDialogues[i] = str[i].split("|");
                    console.log(str[i].split("|"));
                    let li = document.createElement("li"); 
                    li.innerHTML = strProperties[1];
                    li.className = "conv_link";
                    li.addEventListener("click", conversation.getNextLine);
                    conversation.conversationDiv.appendChild(li);
                }
            }
            else {
                let strProperties = str.split("|");
                this.togglePerson(strProperties[0]);
                conversation.activeDialogues.push(str.split("|"));
                let li = document.createElement("li"); 
                li.innerHTML = strProperties[1];
                li.className = "conv_link";
                li.addEventListener("click", conversation.getNextLine);
                conversation.conversationDiv.appendChild(li);
                console.log(conversation.activeDialogues);
            }
        }
    };
    function end() {
        console.log("end");
    }
    function travel() {
        console.log("travel");
    }