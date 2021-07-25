function checkDeviceType() {
    // Check if there is phone
    if(window.screen.width > 830 ||
       (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == false)) {
        document.getElementById("control").style.display = "none";
        game.properties.actionText = "Press x";
        game.properties.enterText = "E - enter building";
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
                if(game.properties.loading == false) {
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
                        let check = game.checkBuilding(clientX, clientY);
                        if(check == false) {
                            game.checkCharacter();
                        }
                    }, 300);
                }
                else {
                    // Double tap
                    clearTimeout(clickTimer);
                    // If game is loading, return false
                    if(game.properties.loading == false) {
                        game.getNextMap();    
                    }
                }
    
            });
        document.getElementById("control").addEventListener("touchmove", game.controls.move);
        document.getElementById("control").addEventListener("touchend", game.controls.endMove);   
    }
        // Set controls
        game.controls.e = game.checkBuilding;
        game.controls.w = game.checkCharacter;
        game.controls.x = game.getNextMap;
        // Prevent user from scrolling with arrow keys on site
        window.addEventListener("keydown", function(e) {
            // space and arrow keys
            if([37, 38, 39, 40, 67].indexOf(e.keyCode) > -1 || (e.keyCode == 32 &&e.target == document.body)) {
                e.preventDefault();
            }
        }, false);
        window.addEventListener('keydown', function (e) {
            if(game.properties.gamePause == true) {
                game.resumeGame();
            }
            switch(e.keyCode) {
                case 37:
                    game.controls.left = true;
                    break;
                case 38:
                    game.controls.up = true;
                    break;
                case 39:
                    game.controls.right = true;
                    break;
                case 40:
                    game.controls.down = true;
                    break;
                case 65:
                    // A
                    if(gamePieces.player.cooldown <= 0) gamePieces.player.attack = true;
                    break;
                case 67:
                    // C
                    if(gamePieces.player.combat == false){ gamePieces.player.combat = true;}
                    else{ gamePieces.player.combat = false;}
                    break;
                case 80:
                    // P
                    game.inactivityTime(true);
                    break;
                case 87:
                    // W
                    if(game.properties.loading == false && conversation.checkConversation() == false) {
                        game.controls.w();    
                    }
                    break;
                case 88:
                    // X
                    console.log('x');
                    if(game.properties.loading == false && conversation.checkConversation() == false) {
                        game.controls.x();
                    }
                    break;
                case 69:
                    // E
                    if(game.properties.loading == false && conversation.checkConversation() == false) {
                        game.controls.e();
                    }
                    break;
            }
        }, false);
        window.addEventListener('keyup', function (e) {
            switch(e.keyCode) {
                case 37:
                    game.controls.left = false;
                    break;
                case 38:
                    game.controls.up = false;
                    break;
                case 39:
                    game.controls.right = false;
                    break;
                case 40:
                    game.controls.down = false;
                    break;
            }
        }, false);
};