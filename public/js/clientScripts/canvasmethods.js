const CANVAS_METHODS = {
    draw(context, image, width, height, x, y) {
        
    }
};

game.properties.context3.drawImage(this.sprite,
    7 * 32,
    this.spriteYIndex * 32,
    32 * viewport.scale,
    32 * viewport.scale,
    this.drawX - (gamePieces.player.xMovement * viewport.scale),
    this.drawY - (gamePieces.player.yMovement * viewport.scale), this.width, this.height);