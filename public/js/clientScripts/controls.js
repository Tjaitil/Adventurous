const controls = {
        playerLeft: false,
        playerUp: false,
        playerRight: false,
        playerDown: false,
        e: null,
        w: null,
        x: null,
        p: null,
        continueConversation: null,
        /* one tap -> if tap on building checkBuilding()->fetchBuilding else talk to character;
         * double tap -> enter next map
         * move and endmove for mobile
         */
        mobileControlButtonMove() {
            if (game.properties.gameState === 'pause') {
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
            let a = Math.abs(eventY - (elementPos.height / 2));
            let b = Math.abs(eventX - (elementPos.width / 2));
            button.style.top = (event.targetTouches[0].clientY - elementPos.y - 25) + "px";
            button.style.left = (event.targetTouches[0].clientX - 50) + "px";
    
            if (a < 5 && b < 5) {
                controls.playerLeft = false;
                controls.playerUp = false;
                controls.playerRight = false;
                controls.playerDown = false;
                return false;
            }
            if (a > 110 || b > 110) {
                controls.playerLeft = false;
                controls.playerUp = false;
                controls.playerRight = false;
                controls.playerDown = false;
                game.controls.endMove();
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
                controls.playerLeft = false;
                controls.playerUp = false;
                controls.playerRight = true;
                controls.playerDown = false;
            }
            if (22.5 < angle && angle < 67.5) {
                controls.playerLeft = false;
                controls.playerUp = true;
                controls.playerRight = true;
                controls.playerDown = false;
            }
            if (67.5 < angle && angle < 112.5) {
                controls.playerLeft = false;
                controls.playerUp = true;
                controls.playerRight = false;
                controls.playerDown = false;
            }
            if (112.5 < angle && angle < 157.5) {
                controls.playerLeft = true;
                controls.playerUp = true;
                controls.playerRight = false;
                controls.playerDown = false;
            }
            if (157.5 < angle && angle < 202.5) {
                controls.playerLeft = true;
                controls.playerUp = false;
                controls.playerRight = false;
                controls.playerDown = false;
            }
            if (202.5 < angle && angle < 247.5) {
                controls.playerLeft = true;
                controls.playerUp = false;
                controls.playerRight = false;
                controls.playerDown = true;
            }
            if (247.5 < angle && angle < 292.5) {
                controls.playerLeft = false;
                controls.playerUp = false;
                controls.playerRight = false;
                controls.playerDown = true;
            }
            if (292.5 < angle && angle < 337.5) {
                controls.playerLeft = false;
                controls.playerUp = false;
                controls.playerRight = true;
                controls.playerDown = true;
            }
        },
        endMobileMove() {
            let button = document.getElementById("control_button");
            button.style.top = "25%";
            button.style.left = "25%";
            controls.playerLeft = false;
            controls.playerUp = false;
            controls.playerRight = false;
            controls.playerDown = false;
        },
        checkDeviceType() {
            // Check for device type and bind events according to device
            if(window.screen.width > 830 ||
            (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == false)) {
                document.getElementById("control").style.display = "none";
                game.properties.actionText = "Press x";
                game.properties.enterText = "E - Enter building";
                game.properties.enterButton = "E -";
                game.properties.personText = "W - talk to ";
                game.properties.personButton = "W -";
                game.properties.device = "pc";
            }
            else {
                game.properties.actionText = "Double tap";
                game.properties.enterText = "Tap on building to enter";
                game.properties.enterButton = "Tap on";
                game.properties.personText = "Tap on screen to talk";
                game.properties.personButton = "Tap on";
                game.properties.device = "mobile";
            }
            if(game.properties.device === "mobile") {
                document.getElementById("text_canvas").addEventListener("click", 
                    function() {
                        // If the conversation_container visibility is visible, conversation is happening. Prevent other actions
                        if(conversation.checkConversation()) {
                            return false;
                        }
                        // If game is loading, return false
                        if(game.properties.gameState === 'loading') {
                            return false;
                        }
                        doubleClickDetect();
                        let clickTimer;
                        if(click == 1) {
                            let clientX = event.clientX;
                            let clientY = event.clientY;
                            /*let clientX = event.touches[0].clientX;
                            let clientY = event.touches[0].clientY;*/
                            console.log(clientX);
                            console.log(clientY);
                            clickTimer = setTimeout(function() {
                                // Single tap
                                console.log('single tap');
                                let check = inputHandler.checkBuilding(clientX, clientY);
                                if(check == false) {
                                    inputHandler.checkCharacter();
                                }
                            }, 300);
                        }
                        else {
                            // Double tap
                            clearTimeout(clickTimer);
                            // If game is loading, return false
                            if(game.properties.gameState === 'playing') {
                                game.getNextMap();    
                            }
                        }
            
                    });
                document.getElementById("control").addEventListener("touchmove", controls.mobileControlButtonMove);
                document.getElementById("control").addEventListener("touchend", controls.endMobileMove);   
            }
            // Set controls
            controls.e = inputHandler.checkBuilding;
            controls.w = inputHandler.checkCharacter;
            controls.x = game.getNextMap;
            controls.p = function() {
                pauseManager.togglePause();
            };
            controls.space = function() {
                if( !conversation.multipleResponses ) {
                    conversation.getNextLine(false, true);
                }
            };
            // Prevent user from scrolling with arrow keys on site
            window.addEventListener("keydown", function(e) {
                // space and arrow keys
                if([32, 37, 38, 39, 40, 67].indexOf(e.keyCode) > -1 || (e.keyCode == 32 && e.target == document.body)) {
                    e.preventDefault();
                }
            }, false);
            window.addEventListener('keydown', function (e) {
                switch(e.keyCode) {
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
                        if(gamePieces.player.cooldown <= 0 && gamePieces.player.combat === true) gamePieces.player.attack = true;
                        break;
                    case 67:
                        // C
                        gamePieces.player.combat = !gamePieces.player.combat;
                        break;
                    case 87:
                        // W
                        if(game.properties.gameState === 'playing' && conversation.checkConversation() === false) {
                            controls.w();    
                        }
                        break;
                    case 88:
                        // X
                        console.log('x');
                        if(game.properties.gameState === 'playing' && conversation.checkConversation() === false) {
                            controls.x();
                        }
                        break;
                    case 69:
                        // E
                        if(game.properties.gameState === 'playing' && conversation.checkConversation() === false) {
                            controls.e();
                        }
                        break;
                    case 80:
                        // P
                        if(conversation.checkConversation() === false) {
                            controls.p();
                        }
                        break
                }
            }, false);
            window.addEventListener('keyup', function (e) {
                switch(e.keyCode) {
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