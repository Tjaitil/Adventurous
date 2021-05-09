const viewport = {
    counter: 0,
    scale: game.properties.canvasWidth / 700,
    draw: function() {
        ctx = game.properties.context;
        // ctx.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        game.properties.context4.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        ctx.save();
        //Draw world and translate the image according to players movement
        /*console.log('drawGameY:' + (game.properties.ycamMove + gamePieces.player.yMovement));
        console.log('drawGameX:' + (game.properties.xcamMove + gamePieces.player.xMovement + 200));
        console.log(gamePieces.player.yMovement);*/
        if(/Safari|Chrome/i.test(navigator.userAgent)) {
            let xPos = 0;
            let sxPos = game.properties.xcamMove + gamePieces.player.xMovement;
            let yPos = 0;
            let syPos = game.properties.ycamMove + gamePieces.player.yMovement;
            if(game.properties.xcamMove + gamePieces.player.xMovement < 0) {
                xPos = (game.properties.xcamMove + gamePieces.player.xMovement) * -1;
                sxPos = 0;
            }
            if(game.properties.ycamMove + gamePieces.player.yMovement < 0) {
                yPos = (game.properties.ycamMove + gamePieces.player.yMovement) * -1;
                syPos = 0;
            }
            ctx.drawImage(world, sxPos,
                syPos, 1024 * this.scale, 1024 * this.scale,
                xPos, yPos,
                1024 * this.scale, 1024 * this.scale);       
        }
        else {
            ctx.drawImage(world, game.properties.xcamMove + gamePieces.player.xMovement,
                  game.properties.ycamMove + gamePieces.player.yMovement, 1024 * this.scale, 1024 * this.scale, 0, 0,
                  1024 * this.scale, 1024 * this.scale); 
        }
        /*if(xbase + xMovement < 0) {
            let width = xbase + xMovement;
            let widthP = (xbase + xMovement) * -1;
            console.log(widthP);
            game.properties.context4.clearRect(0, 0, 700, 700);
            game.properties.context4.drawImage(eastImg, 3200 + width, 0, widthP, 700, 0, 0, widthP, 700);
        }*/
        ctx.restore();
    },
    drawEdge: function() {
        ctx = game.properties.context;
        if(game.properties.xbase + gamePieces.player.xMovement < game.properties.charX) {
                ctx.fillStyle = "black";
                ctx.fillRect(0, 0, (game.properties.xbase + gamePieces.player.xMovement - game.properties.charX) * -1, 600);
        }
        
        if(game.properties.ybase + gamePieces.player.yMovement < 200) {
            ctx.fillStyle = "black";
            ctx.fillRect(0, 0, game.properties.canvasWidth, (game.properties.ybase + gamePieces.player.yMovement -
                                                                                            game.properties.charY) * -1);
        }
        if(gamePieces.player.xMovement + game.properties.xbase > 3200 - game.properties.charX - 100) {
            ctx.fillStyle = "black";
            // Safari and chrome need 2 extra pixels for the curtain to be drawn correctly
            let widthFix;
            if(/Safari|Chrome/i.test(navigator.userAgent)) {
                widthFix = 2;
            }
            else {
                widthFix = 0;
            }
            ctx.fillRect(game.properties.canvasWidth, 0, - (game.properties.xbase + gamePieces.player.xMovement -
                                                          (3200 - game.properties.charX - 64 - widthFix)), 600);
        }
        if(gamePieces.player.yMovement + game.properties.ybase > 3200 - game.properties.charY - 100) {
            ctx.fillStyle = "black";
            // Compensate for height difference, 0.511 per height difference;
            let heightDiff = (400 - game.properties.canvasHeight) * 0.511;
            ctx.fillRect(0, game.properties.canvasHeight - (game.properties.ybase + gamePieces.player.yMovement -  (2968 + heightDiff)),
                         game.properties.canvasWidth, (game.properties.ybase + gamePieces.player.yMovement - (2968 + heightDiff)));
        }
        if(gamePieces.player.ypos > 3160 || 
           (gamePieces.player.ypos < 3100 && gamePieces.player.ypos < 32) ||
           gamePieces.player.xpos > 3160 ||
           (gamePieces.player.xpos < 3100 && gamePieces.player.xpos < 32)){
            game.canvasText.showText(game.properties.actionText + ' to go to next map', false);
        }
        else if(game.canvasText.textDrawn !== false) {
            game.properties.nagivateNext = false;
            game.canvasText.hideText();
        }
        
    }
};