import { viewport } from './viewport.js';
import { GamePieces } from "./gamePieces.js";
import { HUD } from "./HUD.js";
import { conversation } from "./conversation.js";
export const tutorial = {
    step: 1,
    lastStep: 8,
    onGoing: false,
    steps: [
        {
            id: 0,
            index: "hssn0",
        },
    ],
    progressBar: null,
    clickEvent: function () { },
    handler: function () { },
    checkStep() {
        let index;
        switch (this.step) {
            case 1:
                index = "hssn0";
                break;
            case 2:
                console.log(conversation.index);
                if (conversation.index === "hssn1rrrrrr") {
                    index = "hssnns";
                }
                else {
                    index = "hssn1";
                }
            default:
                break;
        }
        conversation.loadConversation("hassen", index);
    },
    relocateHassen([xNewPos, yNewPos]) {
        GamePieces.objects.forEach((element) => {
            if (element.type === "figure")
                return;
            else if (element.src.indexOf("hassen") !== -1) {
                let xAdd = xNewPos - element.x;
                let yAdd = yNewPos - element.y;
                element.x += xAdd;
                element.drawX += xAdd;
                element.diameterUp += yAdd;
                element.diameterDown += yAdd;
                element.y += yAdd;
                element.drawY += yAdd;
                element.diameterLeft += xAdd;
                element.diameterRight += xAdd;
            }
        });
        viewport.resetObjectLayer();
        GamePieces.drawStaticPieces();
        viewport.checkViewportGamePieces(true);
    },
    locateHassen() {
        GamePieces.objects.forEach((element) => {
            if (element.type === "figure")
                return;
            else if (element.src.indexOf("hassen") !== -1) {
                console.log(element);
            }
        });
    },
    showBuilding(building) {
        GamePieces.objects.forEach((element) => {
            if (element.type === "figure")
                return;
            else if (element.src.indexOf(building) !== -1) {
                element.visible = true;
            }
            else if (element.type === "building") {
                element.visible = false;
            }
        });
        GamePieces.drawStaticPieces();
    },
    setTutorialTopic(topic) {
        document.getElementById("tutorial_progressContainer").querySelectorAll("p")[1].innerText =
            this.step + ". " + topic;
        HUD.elements.tutorialProgressBar.setCurrentValue(this.step);
    },
    conversationCount: 0,
    startTutorial() {
        if (this.onGoing === true)
            return;
        this.onGoing = !this.onGoing;
        HUD.makeTutorialHUD();
        conversation.loadConversation("hassen", "");
        //   this.tutorialSteps();
    },
    setNextStep(step) {
        console.log("nextStep");
        step ? (this.step = step) : this.step++;
        this.tutorialSteps();
        // document.getElementById("conversation").querySelectorAll("button")[0].removeEventListener("click", tutorial.handler);
    },
    tutorialSteps() {
        // Step 0 doesn't exist
        switch (this.step) {
            case 1:
                this.setTutorialTopic("Introduction");
                break;
            case 2:
                this.setTutorialTopic("Controls");
                conversation.loadConversation("hassen", "hssn1");
                break;
            case 3:
                // HUD
                this.setTutorialTopic("HUD");
                conversation.loadConversation("hassen", "hssn2");
                break;
            case 4:
                // Profiencies
                this.setTutorialTopic("Profiencies");
                conversation.loadConversation("hassen", "hssn3");
                break;
            case 5:
                // Buildings
                this.setTutorialTopic("Buildings");
                conversation.loadConversation("hassen", "hssn4");
                break;
            case 6:
                // Buildings
                this.setTutorialTopic("Characters");
                conversation.loadConversation("hassen", "hssn5");
                break;
            case 7:
                // Daqloons
                this.setTutorialTopic("Daqloons");
                break;
            case 8:
                // Adventures
                this.setTutorialTopic("Adventures");
                break;
            case 9:
                this.setTutorialTopic("8. End");
                conversation.loadConversation("hassen", "hssnEx");
                break;
        }
    },
    skipTutorial() {
        tutorial.setNextStep(7);
        tutorial.tutorialSteps();
    },
    exitTutorial() {
        this.onGoing = false;
        this.step = 1;
        HUD.hideTutorialHUD();
    },
};
