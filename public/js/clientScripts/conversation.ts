import { gameTravel } from './gameTravel.js';
import { tutorial } from "./tutorial.js";
import { Game } from "../advclient.js";
import { inputHandler } from "./inputHandler.js";
import { gameLogger } from "../utilities/gameLogger.js";
import { GamePieces } from "./gamePieces.js";
import viewport from "./viewport.js";
import { jsUcfirst } from "../utilities/uppercase.js";
import { clientOverlayInterface } from "./clientOverlayInterface.js";

addEventListener("load", function () {
    conversation.conversationDiv = document.getElementById("conversation").querySelectorAll("ul")[0];
    document
        .getElementById("conversation_container")
        .querySelectorAll("img")[0]
        .addEventListener("click", () => conversation.endConversation());
    /*conversation.toggleButton();*/
    conversation.button = document.getElementById("conv_button");
    conversation.button.addEventListener("click", conversation.addNextEvent);
});
export const conversation = {
    index: null,
    indexSet: false,
    active: false,
    end: false,
    conversationDiv: null,
    button: null,
    buttonToggle: false,
    selectItem: false,
    activeDialogues: [],
    persons: [],
    conversationPartner: null,
    multipleResponses: false,
    endEvents: [],
    personContainerA: null,
    personContainerB: null,
    setup() {
        this.personContainerA = document.getElementById("conversation_a");
        this.personContainerB = document.getElementById("conversation_b");
    },
    addNextEvent() {
        conversation.button.addEventListener("click", (event) => conversation.getNextLine(false, false, event));
    },
    convAJAX(data, callback) {

        // fetch('handlers/handler_con.php',)
        let ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
                callback([false, this.responseText]);
            }
        };
        ajaxRequest.open("POST", "handlers/handler_con.php");
        ajaxRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajaxRequest.send(data);
    },
    checkConversation() {
        if (document.getElementById("conversation_container").style.visibility === "visible") {
            return true;
        } else {
            return false;
        }
    },
    loadConversation(character, index: string = "", callback = false) {
        let characterObject = GamePieces.characters.find((object) => {
            return object.displayName === character;
        });
        let person = (this.persons[0] = characterObject.displayName);
        person = character;
        if (person.length <= 2) {
            return false;
        }
        this.active = true;
        this.end = false;
        this.persons = [];
        // Close news to prevent players having conversation and also being in building
        clientOverlayInterface.hide();

        // If index is undefined, set it to false
        index == undefined ? (this.index = false) : (this.index = index);
        let h = document.createElement("h2");
        h.innerText = "Loading...";
        h.id = "loading_message";
        let conversation_container = document.getElementById("conversation_container");
        if (conversation_container.style.visibility !== "visible") {
            conversation_container.style.scale = "1";
            conversation_container.style.visibility = "visible";
        }
        this.conversationDiv.appendChild(h);
        if (Game.properties.device == "mobile") {
            let conversationContainerHeight = viewport.height * 0.4;
            if (conversationContainerHeight > 170) {
                conversation_container.style.height = "170px";
            } else {
                conversation_container.style.height = conversationContainerHeight + "px";
            }
            document.getElementById("conversation").style.height = conversation_container.offsetHeight - 32 + "px";
            // Set offsetTop for non pc device so that the bottom of conversation container is the same as game_canvas;
        }

        let data = "person=" + person.toLowerCase() + "&index=" + this.index;
        conversation.convAJAX(data, (response) => {
            if (response[1] === "ERROR") {
                gameLogger.addMessage("That person is not interested in talking to you");
                gameLogger.logMessages();
                this.endConversation();
                return;
            }
            let responseText = JSON.parse(response[1]);
            document.getElementById("conversation").querySelectorAll("button")[0].style.visibility = "visible";
            this.indexSet = true;
            this.activeDialogues = responseText.conversation;
            this.index = responseText.conversationIndex;
            if (this.buttonToggle == false) {
                this.toggleButton();
            }
            this.makeLinks();

            // Set width on conversation so it fits between images by subtracting container width by both pictures
            document.getElementById("conversation").style.width =
                conversation_container.offsetWidth - this.personContainerA.width * 2 - 17 + "px";
            console.log(document.getElementById("conversation").style.width);
            if (callback !== false) {
                // callback();
            }
        });
    },
    endConversation() {
        for (const i of this.endEvents) {
            i();
        }
        this.conversationDiv.innerHTML = "";
        this.active = false;
        document.getElementById("conversation_container").style.visibility = "hidden";
        document.getElementById("conversation_container").style.scale = "0.5";
        this.buttonToggle = false;
        this.personContainerA.style.visibility = "hidden";
        this.personContainerB.style.visibility = "hidden";
        document.getElementById("conversation").querySelectorAll("button")[0].style.visibility = "hidden";
        this.endEvents = [];
    },
    toggleButton() {
        if (this.buttonToggle === false) {
            document.getElementById("conversation").querySelectorAll("button")[0].style.display = "";
            document.getElementById("conversation_header").innerText = "";
            this.buttonToggle = true;
        } else {
            document.getElementById("conversation").querySelectorAll("button")[0].style.display = "none";
            document.getElementById("conversation_header").innerText = "Select an answer";
            this.buttonToggle = false;
        }
    },
    checkFunctions(split) {
        if (split[3].length === 0) return false;
        // Event will fire after getting the data!
        let objArray = split[3].split(".");
        let objName = objArray[0];
        let funcName = objArray[0].split("#")[0];
        let triggerEvent;
        switch (funcName) {
            case "fetchBuilding":
                triggerEvent = () => {
                    inputHandler.fetchBuilding(String(objArray[1]));
                };
                break;
            case "loadConversation":
                if (objArray[2] !== "undefined") {
                    conversation.loadConversation(String(objArray[1]), String(objArray[2]));
                } else {
                    conversation.loadConversation(String(objArray[1]), "");
                }
                break;
            case "relocateHassen":
                triggerEvent = () => {
                    tutorial.relocateHassen([Number(objArray[1]), Number(objArray[2])]);
                };
                break;
            case "checkStep":
                triggerEvent = () => {
                    tutorial.checkStep;
                };
                break;
            case "setNextStep":
                triggerEvent = () => {
                    tutorial.setNextStep();
                };
                break;
            case "setHuntedStatus":
                triggerEvent = () => {
                    GamePieces.player.setHuntedStatus(true);
                };
                break;
            case "showBuilding":
                triggerEvent = () => {
                    tutorial.showBuilding(objArray[1]);
                };
                break;
            default:
                break;
        }
        console.log(triggerEvent);
        if (objArray[0].indexOf("#") !== -1) {
            this.endEvents.push(triggerEvent);
        } else if (triggerEvent.length !== "undefined") {
            triggerEvent();
        }
    },
    getNextLine(info = false, space = false, event?: Event) {
        if (this.end === true) {
            this.endConversation();
            return false;
        }
        let text;
        console.log(event);
        console.log(info);
        if (event === undefined && info == false) {
            return;
        }
        let eventTargetElement = <HTMLElement>event.currentTarget;
        if (eventTargetElement.tagName === "BUTTON" || space === true) {
            text = document.getElementById("conversation").querySelectorAll("li")[0].innerText;
        } else if (eventTargetElement.tagName !== "LI" && info !== false) {
            text = info + "|";
        } else {
            text = eventTargetElement.innerText;
        }

        let data = "person=set" + "&index=" + text;
        this.convAJAX(data, (response) => {
            if (response[1] === "end") {
                this.endConversation();
                return false;
            }

            let responseText = JSON.parse(response[1]);
            this.activeDialogues = responseText.conversation;
            this.index = responseText.conversationIndex;
            if (this.activeDialogues.length == 1) {
                let split = this.activeDialogues[0].split("|");
                if (typeof split[3] !== "undefined") {
                    this.checkFunctions(split);
                }
                // If conversation is ended by one of the functions in previous if statement, return false
                if (document.getElementById("conversation_container").style.visibility === "hidden") {
                    return false;
                }
            }
            if (conversation.activeDialogues.length > 1) {
                this.buttonToggle = true;
                this.multipleResponses = true;
                this.toggleButton();
                this.makeLinks();
                if (response[1].indexOf("newDestination") != -1) {
                    let lis = this.conversationDiv.querySelectorAll("li");
                    lis.forEach((element) => {
                        element.addEventListener('click', (event) => gameTravel.newDestination(event, this.persons[0]));
                    });
                }
            } else {
                /*if(conversation.activeDialogues[0][1] === "selectItem") {
                    console.log('Choose inventory');
                    conversation.buttonToggle = true;
                    conversation.toggleButton();
                }*/
                this.multipleResponses = false;
                if (this.buttonToggle !== true) {
                    this.toggleButton();
                }
                switch (this.activeDialogues[0].split("|")[2]) {
                    case "Q":
                    case "q":
                    case "r":
                        this.makeLinks();
                        break;
                    case "end":
                        this.makeLinks();

                        // Clone node to remove event listener
                        let nextLineButton = document.getElementById("conversation").querySelectorAll("button")[0];
                        let clone;
                        if (nextLineButton) {
                            clone = nextLineButton.cloneNode(true);
                        }

                        document.getElementById("conversation").querySelectorAll("button")[0].replaceWith(clone);

                        this.end = true;
                        break;
                }
            }
        });
    },
    togglePerson(part) {
        // Toggle person talking
        if (part.indexOf("a") != -1) {
            // If part is more than 1 length, the second index will be a string with new character
            if (part.split(",").length > 1) {
                let person = part.split(",")[1];
                if (this.persons.indexOf(person)) this.persons.push(person);
                this.conversationPartner = person;
            } else {
                this.conversationPartner = this.persons[0];
            }
            console.log(this.persons[0]);
            this.personContainerA.style.visibility = "visible";
            this.personContainerA.src = "public/images/" + this.persons[0] + ".png";
            document.getElementById("conversation_header").innerText = jsUcfirst(this.persons[0]);
            this.personContainerB.style.visibility = "hidden";
        } else {
            this.personContainerB.src = "public/images/character image.png";
            this.personContainerB.style.visibility = "visible";
            document.getElementById("conversation_header").innerHTML = "You";
            this.personContainerA.style.visibility = "hidden";
        }
        if (part.indexOf("#") != -1) {
            document.getElementById("conversation_container").querySelectorAll("h3")[0].innerText = part
                .split("#")[1]
                .trim();
        }
    },
    makeLinks() {
        let str = conversation.activeDialogues;
        this.conversationDiv.innerHTML = "";
        console.log(str);
        if (str.length > 1 === true) {
            for (var i = 0; i < str.length; i++) {
                let strProperties = str[i].split("|");
                this.togglePerson(strProperties[0]);
                let li = document.createElement("li");
                li.innerHTML = strProperties[1];
                li.className = "conv_link";
                li.style.cursor = "pointer";
                li.addEventListener("click", (event) => this.getNextLine(false, false, event));
                this.conversationDiv.appendChild(li);
            }
        } else {
            let strProperties = str[0].split("|");
            this.togglePerson(strProperties[0]);
            let li = document.createElement("li");
            li.innerHTML = strProperties[1];
            li.className = "conv_link";
            li.style.cursor = "auto";
            this.conversationDiv.appendChild(li);
        }
    },
};
function selectItem() {
    document.getElementById("conversation_header").innerText = "Select an answer";
}
const highlightInventory = {
    intervalID: null,
    set() {
        this.intervalID = setInterval(function () {
            let inventory = document.getElementById("inventory");
            if (inventory.style.backgroundColor === "" || inventory.style.backgroundColor == "rgb(76, 52, 26)") {
                inventory.style.backgroundColor = "#986834";
            } else {
                inventory.style.backgroundColor = "#4c341a";
            }
        }, 1500);
    },
    clear() {
        document.getElementById("inventory").style.backgroundColor = "#4c341a";
        clearInterval(this.intervalID);
    },
};
