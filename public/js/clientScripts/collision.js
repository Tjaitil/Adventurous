const collisionCheck = function(debug = false) {
    // Collision detection, if user is less than 1px from object prevent movement
    for(i = 0; i < gamePieces.nearObjects.length; i++) {
        if(gamePieces.nearObjects[i].noCollision == true) {
            continue;
        }
        if(Math.abs(gamePieces.player.diameterDown - gamePieces.nearObjects[i].diameterUp) <= 2 &&
            gamePieces.player.diameterRight >= gamePieces.nearObjects[i].diameterLeft &&
            gamePieces.player.diameterLeft <= gamePieces.nearObjects[i].diameterRight) {
            gamePieces.player.down = "blocked";
            if (debug == true) {
                console.log(gamePieces.nearObjects[i]);
                console.log('player_down');
            }
        }
        if(Math.abs(gamePieces.player.diameterRight - gamePieces.nearObjects[i].diameterLeft) <= 2 &&
            gamePieces.player.diameterUp <= gamePieces.nearObjects[i].diameterDown &&
            gamePieces.player.diameterDown >= gamePieces.nearObjects[i].diameterUp) {
            gamePieces.player.right = "blocked";
            if (debug == true) {
                console.log(gamePieces.nearObjects[i]);
                console.log('player right');
            }
        }
        if(Math.abs(gamePieces.player.diameterUp - gamePieces.nearObjects[i].diameterDown) <= 2 &&
            gamePieces.player.diameterRight >= gamePieces.nearObjects[i].diameterLeft &&
            gamePieces.player.diameterLeft <= gamePieces.nearObjects[i].diameterRight) {
            gamePieces.player.up = "blocked";
            if (debug == true) {
                console.log(gamePieces.nearObjects[i]);
                console.log('player up');
            }
        }
        if(Math.abs(gamePieces.player.diameterLeft - gamePieces.nearObjects[i].diameterRight) <= 2 &&
            gamePieces.player.diameterUp <= gamePieces.nearObjects[i].diameterDown &&
            gamePieces.player.diameterDown >= gamePieces.nearObjects[i].diameterUp) {
            gamePieces.player.left = "blocked";
            if (debug == true) {
                console.log(gamePieces.nearObjects[i]);
                console.log('player left');
            }
        }
    }
    // Triangle collision detection
    /*if(xtmax > player.diameterLeft && ytmin < player.diameterDown && (hYPos + 1 >= player.diameterLeft || )) {
        
    }*/

    if(controls.playerLeft && gamePieces.player.left == "blocked") {
        gamePieces.player.speedX = 0;
    }
    if(controls.playerRight && gamePieces.player.right == "blocked") {
        gamePieces.player.speedX = 0;
    }
    if(controls.playerDown && gamePieces.player.down == "blocked") {
        gamePieces.player.speedY = 0;
    }
    if(controls.playerUp && gamePieces.player.up == "blocked") {
        gamePieces.player.speedY = 0;
    }
    gamePieces.player.xTracker = gamePieces.player.xMovement +=
        Math.round(gamePieces.player.movementSpeed * game.properties.delta) * gamePieces.player.speedX;
    gamePieces.player.yTracker = gamePieces.player.yMovement +=
        Math.round(gamePieces.player.movementSpeed * game.properties.delta) * gamePieces.player.speedY;
};