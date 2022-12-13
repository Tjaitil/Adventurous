import { tutorial } from './tutorial.js';
import { pauseManager } from './pause.js';
import { inputHandler } from './inputHandler.js';
import { Game } from "../advclient.js";
import { gameLogger } from "../utilities/gameLogger.js";
import { GamePieces } from "./gamePieces.js";
import { conversation } from "./conversation.js";
export const controls = {
    playerLeft: false,
    playerUp: false,
    playerRight: false,
    playerDown: false,
    actionText: "Press x",
    enterText: "E - Enter building",
    enterButton: "E -",
    personText: "W - talk to ",
    personButton: "W -",
    device: "pc",
    w: null,
    p: null,
    space: null,
    continueConversation: null,
    checkPlayerMovement() {
        GamePieces.player.speedX = 0;
        GamePieces.player.speedY = 0;
        if (this.playerLeft === true) {
            GamePieces.player.speedX = -GamePieces.player.speed;
        }
        if (this.playerRight === true) {
            GamePieces.player.speedX = GamePieces.player.speed;
        }
        if (this.playerUp === true) {
            GamePieces.player.speedY = -GamePieces.player.speed;
        }
        if (this.playerDown === true) {
            GamePieces.player.speedY = GamePieces.player.speed;
        }
    },
    e(eMouseX = false, eMouseY = false) {
        function enterBuilding() {
            for (let i = 0; i < GamePieces.buildings.length; i++) {
                let object = GamePieces.buildings[i];
                if (GamePieces.player.ypos > object.diameterUp &&
                    GamePieces.player.ypos < object.diameterDown &&
                    GamePieces.player.xpos > object.diameterLeft &&
                    GamePieces.player.xpos < object.diameterRight &&
                    Math.abs(GamePieces.player.ypos - object.diameterDown) < 32) {
                    if (Game.properties.inBuilding == false) {
                        inputHandler.fetchBuilding(object.src.split(".png")[0]);
                    }
                    break;
                }
            }
        }
        if (tutorial.onGoing) {
            gameLogger.addMessage("This building can not be accessed on tutorial island");
            gameLogger.logMessages();
        }
        if (Game.properties.inBuilding != true && Game.properties.device == "pc") {
            enterBuilding();
        }
        else if (Game.properties.inBuilding != true && Game.properties.device == "mobile") {
            // console.log("check building");
            // let element = document.getElementById("text_canvas");
            // let ElementPos = element.getBoundingClientRect();
            // // Remove elementPos of the canvas so that 0.0 is in up-left corner
            // let mouseY = eMouseY - ElementPos.top;
            // let mouseX = eMouseX - ElementPos.left;
            // let x = mouseX + (GamePieces.player.xpos - viewport.width / 2 + 32);
            // let y = mouseY + (GamePieces.player.ypos - viewport.height / 2);
            // let result = false;
            // for (let i = 0; i < GamePieces.buildings.length; i++) {
            //     let object = GamePieces.buildings[i];
            //     if (
            //         y > object.diameterUp &&
            //         y < object.diameterDown &&
            //         x > object.diameterLeft &&
            //         x < object.diameterRight &&
            //         Math.abs(GamePieces.player.ypos - object.diameterDown) < 32
            //     ) {
            //         result = true;
            //         inputHandler.fetchBuilding(object.src.split(".png")[0]);
            //         break;
            //     }
            // }
            // return result;
        }
    },
    /* one tap -> if tap on building checkBuilding()->fetchBuilding else talk to character;
     * double tap -> enter next map
     * move and endmove for mobile
     */
    mobileControlButtonMove(event) {
        if (Game.properties.gameState === "pause") {
            pauseManager.resumeGame();
        }
        let button = document.getElementById("control_button");
        let element = event.target.closest("#control");
        let elementPos = element.getBoundingClientRect();
        let buttonPos = button.getBoundingClientRect();
        let eventY = event.targetTouches[0].clientY - elementPos.y;
        let eventX = event.targetTouches[0].clientX - elementPos.x;
        // eventYTrigger/eventXTrigger is the minimum width the button gets moved before movement happens;
        let eventYTrigger = 50;
        let eventXTrigger = 50;
        // a is the distance from eventY to diameter
        // b is the distance from eventX to diaemter
        let a = Math.abs(eventY - elementPos.height / 2);
        let b = Math.abs(eventX - elementPos.width / 2);
        button.style.top = event.targetTouches[0].clientY - elementPos.y - 25 + "px";
        button.style.left = event.targetTouches[0].clientX - 50 + "px";
        if (a < 5 && b < 5) {
            this.playerLeft = false;
            this.playerUp = false;
            this.playerRight = false;
            this.playerDown = false;
            return false;
        }
        if (a > 110 || b > 110) {
            this.playerLeft = false;
            this.playerUp = false;
            this.playerRight = false;
            this.playerDown = false;
            return false;
        }
        let angle;
        if (eventX > eventXTrigger && eventY < eventYTrigger) {
            angle = (Math.atan(a / b) / (2 * 3.14)) * 360;
        }
        if (eventX < eventXTrigger && eventY < eventYTrigger) {
            angle = (Math.atan(b / a) / (2 * 3.14)) * 360 + 90;
        }
        if (eventX < eventXTrigger && eventY > eventYTrigger) {
            angle = (Math.atan(a / b) / (2 * 3.14)) * 360 + 180;
        }
        if (eventX > eventXTrigger && eventY > eventYTrigger) {
            angle = (Math.atan(b / a) / (2 * 3.14)) * 360 + 270;
        }
        if (337.5 < angle || angle < 22.5) {
            this.playerLeft = false;
            this.playerUp = false;
            this.playerRight = true;
            this.playerDown = false;
        }
        if (22.5 < angle && angle < 67.5) {
            this.playerLeft = false;
            this.playerUp = true;
            this.playerRight = true;
            this.playerDown = false;
        }
        if (67.5 < angle && angle < 112.5) {
            this.playerLeft = false;
            this.playerUp = true;
            this.playerRight = false;
            this.playerDown = false;
        }
        if (112.5 < angle && angle < 157.5) {
            this.playerLeft = true;
            this.playerUp = true;
            this.playerRight = false;
            this.playerDown = false;
        }
        if (157.5 < angle && angle < 202.5) {
            this.playerLeft = true;
            this.playerUp = false;
            this.playerRight = false;
            this.playerDown = false;
        }
        if (202.5 < angle && angle < 247.5) {
            this.playerLeft = true;
            this.playerUp = false;
            this.playerRight = false;
            this.playerDown = true;
        }
        if (247.5 < angle && angle < 292.5) {
            this.playerLeft = false;
            this.playerUp = false;
            this.playerRight = false;
            this.playerDown = true;
        }
        if (292.5 < angle && angle < 337.5) {
            this.playerLeft = false;
            this.playerUp = false;
            this.playerRight = true;
            this.playerDown = true;
        }
    },
    endMobileMove() {
        let button = document.getElementById("control_button");
        button.style.top = "25%";
        button.style.left = "25%";
        this.playerLeft = false;
        this.playerUp = false;
        this.playerRight = false;
        this.playerDown = false;
    },
    checkDeviceType() {
        // Check for device type and bind events according to device
        if (window.screen.width > 830 ||
            /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == false) {
            document.getElementById("control").style.display = "none";
            this.actionText = "Press x";
            this.enterText = "E - Enter building";
            this.enterButton = "E -";
            this.personText = "W - talk to ";
            this.personButton = "W -";
            this.device = "pc";
        }
        else {
            this.actionText = "Double tap";
            this.enterText = "Tap on building to enter";
            this.enterButton = "Tap on";
            this.personText = "Tap on screen to talk";
            this.personButton = "Tap on";
            this.device = "mobile";
        }
        if (Game.properties.device === "mobile") {
            // document.getElementById("text_canvas").addEventListener("click",
            //     function() {
            //         // If the conversation_container visibility is visible, conversation is happening. Prevent other actions
            //         if(conversation.checkConversation()) {
            //             return false;
            //         }
            //         // If game is loading, return false
            //         if(game.properties.gameState === 'loading') {
            //             return false;
            //         }
            //         // doubleClickDetect();
            //         let clickTimer;
            //         if(click == 1) {
            //             let clientX = event.clientX;
            //             let clientY = event.clientY;
            //             /*let clientX = event.touches[0].clientX;
            //             let clientY = event.touches[0].clientY;*/
            //             console.log(clientX);
            //             console.log(clientY);
            //             clickTimer = setTimeout(function() {
            //                 // Single tap
            //                 let check = inputHandler.checkBuilding(clientX, clientY);
            //                 if(check == false) {
            //                     inputHandler.checkCharacter();
            //                 }
            //             }, 300);
            //         }
            //         else {
            //             // Double tap
            //             clearTimeout(clickTimer);
            //             // If game is loading, return false
            //             if(game.properties.gameState === 'playing') {
            //                 game.getNextMap();
            //             }
            //         }
            //     });
            // document.getElementById("control").addEventListener("touchmove", controls.mobileControlButtonMove);
            // document.getElementById("control").addEventListener("touchend", controls.endMobileMove);
        }
        // Set controls
        controls.w = () => inputHandler.interactCharacter();
        controls.p = () => pauseManager.togglePause();
        controls.space = () => {
            let event = new Event("keydown");
            if (!conversation.multipleResponses) {
                conversation.getNextLine(false, true);
            }
        };
        // Prevent user from scrolling with arrow keys on site
        window.addEventListener("keydown", (e) => {
            // space and arrow keys
            if ([32, 37, 38, 39, 40, 67].indexOf(e.keyCode) > -1 ||
                (e.keyCode == 32 && e.target == document.body)) {
                e.preventDefault();
            }
        }, false);
        window.addEventListener("keydown", (e) => {
            switch (e.keyCode) {
                case 32:
                    controls.space();
                    break;
                case 37:
                    controls.playerLeft = true;
                    break;
                case 38:
                    controls.playerUp = true;
                    break;
                case 39:
                    controls.playerRight = true;
                    break;
                case 40:
                    controls.playerDown = true;
                    break;
                case 65:
                    // A
                    if (GamePieces.player.cooldown <= 0 && GamePieces.player.combat === true)
                        GamePieces.player.attack = true;
                    break;
                case 67:
                    // C
                    GamePieces.player.combat = !GamePieces.player.combat;
                    break;
                case 87:
                    // W
                    if (Game.properties.gameState === "playing" && conversation.checkConversation() === false) {
                        controls.w();
                    }
                    break;
                case 69:
                    // E
                    if (Game.properties.gameState === "playing" && conversation.checkConversation() === false) {
                        controls.e();
                    }
                    break;
                case 80:
                    // P
                    if (conversation.checkConversation() === false) {
                        controls.p();
                    }
                    break;
            }
        }, false);
        window.addEventListener("keyup", function (e) {
            switch (e.keyCode) {
                case 37:
                    controls.playerLeft = false;
                    break;
                case 38:
                    controls.playerUp = false;
                    break;
                case 39:
                    controls.playerRight = false;
                    break;
                case 40:
                    controls.playerDown = false;
                    break;
            }
        }, false);
    },
};
