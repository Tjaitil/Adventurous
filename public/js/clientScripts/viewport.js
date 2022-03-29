const viewport = {
    counter: 0,
    // scale: game.properties.canvasWidth / 700,
    scale: 1,
    zoom: 1.2,
    worldImage: new Image(3200, 3200),
    draw() {
        ctx = game.properties.context;
        game.properties.context4.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        ctx.fillStyle = "black";
        ctx.fillRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        // if (/Safari|Chrome/i.test(navigator.userAgent)) {
        //     let xPos = 0;
        //     let sxPos = game.properties.xcamMove + gamePieces.player.xMovement;
        //     let yPos = 0;
        //     let syPos = game.properties.ycamMove + gamePieces.player.yMovement;
        //     if (game.properties.xcamMove + gamePieces.player.xMovement < 0) {
        //         xPos = (game.properties.xcamMove + gamePieces.player.xMovement) * -1;
        //         sxPos = 0;
        //     }
        //     if (game.properties.ycamMove + gamePieces.player.yMovement < 0) {
        //         yPos = (game.properties.ycamMove + gamePieces.player.yMovement) * -1;
        //         syPos = 0;
        //     }
        //     ctx.drawImage(game.properties.worldImage, sxPos,
        //         syPos, 1024 * this.scale, 1024 * this.scale,
        //         xPos, yPos,
        //         1024 * this.scale, 1024 * this.scale);
        // }
        // else {
        ctx.drawImage(this.worldImage, game.properties.xcamMove + gamePieces.player.xMovement,
            game.properties.ycamMove + gamePieces.player.yMovement, 
            game.properties.canvasWidth, game.properties.canvasHeight, 0, 0,
            game.properties.canvasWidth, game.properties.canvasHeight);
        // }
    },
    drawEdge() {
        if (gamePieces.player.ypos > 3160 ||
            (gamePieces.player.ypos < 3100 && gamePieces.player.ypos < 10) ||
            gamePieces.player.xpos > 3160 ||
            (gamePieces.player.xpos < 3100 && gamePieces.player.xpos < 10)) {
            game.getNextMap();
        }
    },
    checkViewportGamePieces(first = true) {
        // If player has moved a certain amount of pixels update object that will be drawn
        if (Math.abs(gamePieces.player.xTracker) > 100 ||
            Math.abs(gamePieces.player.yTracker) > 100 ||
            first == true) {
            gamePieces.nearObjects = gamePieces.objects.filter((object) => {
                return ((Math.abs(object.diameterRight - gamePieces.player.xpos) <= game.properties.canvasWidth + 50 ||
                    Math.abs(object.diameterLeft - gamePieces.player.xpos) <= game.properties.canvasWidth + 50) &&
                    (Math.abs(object.diameterUp - gamePieces.player.ypos) <= game.properties.canvasHeight + 50 ||
                        Math.abs(object.diameterDown - gamePieces.player.ypos) <= game.properties.canvasHeight + 50));
            });
            // Visible object is only the objects that are visible
            gamePieces.visibleObjects = gamePieces.nearObjects.filter(object => {
                return (object.type !== 'figure');
            })
            gamePieces.player.xTracker = 0;
            gamePieces.player.yTracker = 0;
        }
    }
};
