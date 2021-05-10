const spritesContainer = {
    sprites = [],
    loadDefaultSprites() {
        this.spriteMaker(coin, 108, 40, coin);
    },
    spriteMaker(name, width, height, src) {
        let sprite = name;
        let image = new Image(width, height);
        image.src = ".public/images/" + src + ".png";
        image.onload = () => {
            this.sprites.push({spriteName: name, sprite: image, width, height});
        }
        image.onerror = () => {
            console.log('Sprite loading failed');
        }
    },
}
function coinAnimation(x, y) {
    this.index = 0;
    this.x = x;
    this.y = y;
    this.drawX = this.x;
    this.drawY = this.y;
    this.indexX = 0;
    this.sprite = new Image(108, 40);
    this.sprite.onload = function() {
        this.sprite.src = "./public/images/coin.png";
    };
    this.draw = function() {
        if(duration % 2 == 0) {
            this.index++;
            game.properties.context3.drawImage(this.sprite, 
                                              this.index * 18, 0, 
                                              18 * viewport.scale, 20 * viewport.scale, 
                                              this.drawX - (gamePieces.player.xMovement * viewport.scale), 
                                              this.drawY - (gamePieces.player.yMovement * viewport.scale),
                                              this.width, this.height);
            game.properties.context3.fillRect(this.drawX - (gamePieces.player.xMovement * viewport.scale), 
            this.drawY - (gamePieces.player.yMovement * viewport.scale), 30, 30);
            if(this.index == 5) {
                this.index = 0;
            }
        }
    };
}
function createDaqloon(id, x, y) {
    this.id = id;
    this.index = 1;
    this.attackDamage = 15;
    this.defence = 10;
    this.x = x;
    this.y = y;
    this.width = 45;
    this.height = 45;
    this.diameterTop = this.y;
    this.diameterLeft = this.x;
    this.diameterBottom = this.y + this.height;
    this.diameterRight = this.x + this.width;
    this.drawX = 0;
    this.drawY = 0;
    this.spriteXIndex = 1;
    this.spriteYIndex = 0;
    this.sprite = new Image(160, 64);
    this.oldX = 0;
    this.oldY = 0;
    this.health = 100;
    this.movementSpeed = 75;
    this.attack = false;
    this.hit = function() {
        game.properties.context3.drawImage(this.sprite,
                                           7 * 32,
                                           this.spriteYIndex * 32,
                                           32 * viewport.scale,
                                           32 * viewport.scale,
                                           this.drawX - (gamePieces.player.xMovement * viewport.scale),
                                           this.drawY - (gamePieces.player.yMovement * viewport.scale), this.width, this.height);
        this.health -= 10;
        this.drawHealthBar(this.drawX - (gamePieces.player.xMovement * viewport.scale),
                           this.drawY - (gamePieces.player.yMovement * viewport.scale));
        if(this.health <= 0) {
            this.die = true;
            gamePieces.items.push(new coinAnimation(this.drawX, this.drawY));    
        }
    };
    this.drawHealthBar = function(x, y) {
        let remainingHealth = 100 - this.health;
        if(remainingHealth < 0 ) {
            this.health = 0;
        }
        game.properties.context3.fillStyle = "blue";
        game.properties.context3.fillRect(x + 5, y - 20, 30, 10);
        if(this.health !== 100) {
            game.properties.context3.fillStyle = "red";
            game.properties.context3.fillRect(x + 35, y - 20, - 0.35 * (100 - this.health), 10);
        }
    
    };
    this.checkNearBy = function() {
        console.log(gamePieces.daqloon.findIndex(this.findOtherDaqloons));
    };
    this.draw = function() {
        if(this.health <= 0) return false;
        if(Math.abs(this.x - gamePieces.player.xpos) < 20 && Math.abs(this.y - gamePieces.player.ypos) < 20 &&
           this.attack == false) {
            this.attack = true;
            gamePieces.player.takeDamage(this.attackDamage);
            
            // 5
            // 6
        }
        else if(this.health > 0)  {
            this.moveToPlayer();
        }
        if(duration % 3 == 0) {
            // If player ypos is greater than y pos of the NPC set the spriteYIndex to 0
            if(this.y + 10 < gamePieces.player.ypos) {
                this.spriteYIndex = 0;
            }
            else {
                this.spriteYIndex = 1;
            }
            if(this.attack == true) {
                if(this.spriteXIndex < 5) {
                    this.spriteXIndex = 5;
                }
                else if(this.spriteXIndex > 6) {
                    this.spriteXIndex = 1;
                    this.attack = false;
                }
            }
            else if(this.spriteXIndex > 4) {
                this.spriteXIndex = 1;
            }
            game.properties.context3.drawImage(this.sprite,
                                           this.spriteXIndex * 32,
                                           this.spriteYIndex * 32,
                                           32 * viewport.scale,
                                           32 * viewport.scale,
                                           this.drawX - (gamePieces.player.xMovement * viewport.scale),
                                           this.drawY - (gamePieces.player.yMovement * viewport.scale), this.width, this.height);
            this.drawHealthBar(this.drawX - (gamePieces.player.xMovement * viewport.scale),
                               this.drawY - (gamePieces.player.yMovement * viewport.scale));
            this.spriteXIndex++;
        }
        else {
            if(this.spriteXIndex > 4 && this.attack == false) {
                this.spriteXIndex = 1;
            }
            game.properties.context3.drawImage(this.sprite,
                                           this.spriteXIndex * 32,
                                           this.spriteYIndex * 32,
                                           32 * viewport.scale,
                                           32 * viewport.scale,
                                           this.drawX - (gamePieces.player.xMovement * viewport.scale),
                                           this.drawY - (gamePieces.player.yMovement * viewport.scale), this.width, this.height);
            this.drawHealthBar(this.drawX - (gamePieces.player.xMovement * viewport.scale),
                               this.drawY - (gamePieces.player.yMovement * viewport.scale));
        }
    };
    this.moveToPlayer = function() {
        let distanceX = gamePieces.player.xpos - this.x;
        let distanceY = gamePieces.player.ypos - this.y;
        
        // Check if there are nearby daqloons, if so prevent them from "crashing"
        // let nearbyIndex = this.findOtherDaqloons();
        let nearbyX = 1;
        let nearbyY = 1;
        
        // Check wether the enemy NPC is far away from player. If so move to player, else attack player.
        /*if(Math.abs(distanceX) < 0 && Math.abs(distanceX) < 0) {
            return;
        }*/
        if(Math.abs(distanceX) !== 5 || Math.abs(distanceY) !== 5) {
            if(distanceX > 3 || distanceX < -3) {
                this.oldX = this.x;
                if(distanceX > 3) {
                    this.oldX = this.x;
                    /*this.x+= 2;
                    this.drawX += 2;*/
                    this.x += 1 * (delta * this.movementSpeed) * nearbyX;
                    this.drawX += 1 * (delta * this.movementSpeed) * nearbyX;
                }
                else {
                    /*this.x -= 2;
                    this.drawX-= 2;*/
                    this.x -= 1 * (delta * this.movementSpeed) * nearbyX;
                    this.drawX -= 1 * (delta * this.movementSpeed) * nearbyX;
                }
            }
            if(distanceY > 3 || distanceY < -3) {
                this.oldY = this.y;
                if(distanceY > 11) {
                    this.y += 1 * (delta * this.movementSpeed) * nearbyY;
                    this.drawY += 1 * (delta * this.movementSpeed) * nearbyY;
                }
                else {
                    this.y -= 1 * (delta * this.movementSpeed) * nearbyY;
                    this.drawY -= 1 * (delta * this.movementSpeed) * nearbyY;
                }
            }
        }
    };
    this.findOtherDaqloons = function() {
        function check(object) {
            return (Math.abs(this.drawX - object.drawX) < 5 && Math.abs(this.drawY - object.drawY) < 5);
        }
        let nearby = gamePieces.daqloon.findIndex(check, this);
        let nearbyX = 1;
        let nearbyY = 1;
        if(nearby != -1) {
            let nearbyDaqloon = gamePieces.daqloon[nearby];
            if(Math.abs(nearbyDaqloon.drawX - this.drawX) < 5) {
                nearbyX = 0;
            }
            if(Math.abs(nearbyDaqloon.drawY - this.drawY) < 5) {
                nearbyY = 0;
            }
        }
        return [nearbyX, nearbyY];
    };
}