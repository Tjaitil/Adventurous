const spritesContainer = {
    sprites: [],
    loadDefaultSprites() {
        // this.spriteMaker('coin', 108, 40, 'coin');
        this.spriteMaker('shadow', 96, 32, 'shadow sprite')
        this.spriteMaker('daqloon scale', 32, 32, 'daqloon scale');
        this.spriteMaker('daqloon horns', 32, 32, 'daqloon horns');
    },
    spriteMaker(name, width, height, src) {
        let sprite = name;
        let image = new Image(width, height);
        image.src = "./public/images/" + src + ".png";
        image.onload = () => {
            this.sprites.push({'spriteName': name, 'sprite': image, 'width': width, 'height': height});
        }
        image.onerror = () => {
            console.log(image.src + ' Sprite loading failed');
        }
    },
}
function getRndInteger(min, max) {
    return Math.floor(Math.random() * (max - min + 1) ) + min;
}
function item(drawX, drawY, name) {
    this.x = drawX + game.properties.xcamMove;
    this.y = drawY + game.properties.ycamMove;
    this.drawX = drawX;
    this.drawY = drawY;
    this.id = gamePieces.items.length + 1;
    this.spriteObject = spritesContainer.sprites.filter((sprite) => {return sprite.spriteName === name})[0];
    this.shadow = new shadowAnimation(this.drawX, this.drawY);
    this.width = 32;
    this.height = 32;
    this.scale = 0.6;
    this.loopIndex = 0;
    this.loopArray = [1, 4, 6, 4];
    this.checking = false;
    this.draw = function() {
        if(this.loopIndex > 3) this.loopIndex = 0;
        // let context = (this.drawX + this.width < gamePieces.player.diameterDown) ? game.properties.context : game.properties.context3;

        game.properties.context3.drawImage(this.spriteObject.sprite, 
            0, 0, 
            32 * viewport.scale, 32 * viewport.scale, 
            this.drawX - (gamePieces.player.xMovement * viewport.scale) + 5, 
            this.drawY - (gamePieces.player.yMovement * viewport.scale) + this.loopArray[this.loopIndex],
            this.width * this.scale, this.height * this.scale);
        this.shadow.draw();
        if(this.checking === false) this.pickUpItem();
        if(game.properties.duration % 20 === 0) this.loopIndex++;
    };
    this.pickUpItem = function() {
        if(Math.abs(this.x - gamePieces.player.xpos) <= 10 && Math.abs(this.y - gamePieces.player.ypos) <= 10) {
            this.checking = true;
            let inInventory = false;
            if(document.getElementById("inventory").querySelectorAll(".inventory_item").length === 18) {
                let array = document.getElementById("inventory").querySelectorAll(".inventory_item");
                for(let i = 0; i < document.getElementById("inventory").querySelectorAll(".inventory_item").length; i++) {
                    if(array[i].innerHTML.indexOf(this.spriteObject.spriteName) !== -1) {
                        inInventory = true;
                    }
                }
            }
            console.log(inInventory);
            // if(inInventory === false) {
            // gameLogger.addMessage("ERROR: You don't have any free inventory spaces");
            // gameLogger.logMessages();
            //     return false;
            // } 
            // else if(inInventory === true) {
            let data = "model=Loot" + "&method=addLoot" + "&item=" + this.spriteObject.spriteName;
            gamePieces.items = gamePieces.items.filter((item) => {return item.id != this.id});
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    updateInventory();
                }
            });
            // }
        }
    };
}
function shadowAnimation(x, y) {
    this.x = x;
    this.y = y;
    this.drawX = this.x;
    this.drawY = this.y;
    this.indexX = 0;
    this.spriteObject = spritesContainer.sprites.filter((sprite) => {return sprite.spriteName === 'shadow'})[0];
    this.height = this.spriteObject.height;
    this.width = this.spriteObject.width;
    this.loopArray = [0, 1, 2, 1];
    this.draw = function() { 
        if(this.indexX > 3) this.indexX = 0;
        game.properties.context3.drawImage(this.spriteObject.sprite, 
        this.loopArray[this.indexX] * 32, 0, 
        32 * viewport.scale, 32 * viewport.scale, 
        this.drawX - (gamePieces.player.xMovement * viewport.scale) - 8, 
        this.drawY - (gamePieces.player.yMovement * viewport.scale) - 8,
        48, 48);
        if(game.properties.duration % 20 === 0) {
            this.indexX++; 
        }
    };
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
        if(game.properties.duration % 2 == 0) {
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
function checkDaqloon(amount, id = false) {
    amount = parseInt(amount);
    console.log(amount);
    if(amount === 0) return false;
    if(gamePieces.daqloon.length > 5) {
        return false;
    }
    let daqloon;
    let daqloonId;
    let x;
    let y;
    for(i = 0; i < amount; i++) {
        if(id === false) {
           daqloonId = i;
           daqloon = new createDaqloon(i, 0, 0);
        }
        else {
            daqloonId = id;
            daqloon = gamePieces.daqloon[daqloonId];
        }
        daqloon.y = getRndInteger(gamePieces.daqloon_fighting_area.diameterUp, gamePieces.daqloon_fighting_area.diameterDown - 32);
        daqloon.x = getRndInteger(gamePieces.daqloon_fighting_area.diameterLeft, gamePieces.daqloon_fighting_area.diameterRight);
        daqloon.setDiameter();
        daqloon.resetDirections();
        daqloon.dead = false;
        daqloon.drawX = (daqloon.x - game.properties.xcamMove);
        daqloon.drawY = (daqloon.y - game.properties.ycamMove);
        daqloon.sprite.src = "public/images/daqloon sprite.png";
        if(id === false) {
            gamePieces.daqloon.push(daqloon);     
        }
        else {
            daqloon.health = 10;
            gamePieces.daqloon[daqloonId] = daqloon;
        }
    }
}
function getNearestDaqloon(skip = false) {
    let nearestDaqloon = null;
    let hypo = null;
    let newHypo;
    for(let i = 0; i < gamePieces.daqloon.length; i++) {
        if(gamePieces.daqloon[i].dead === true && gamePieces.daqloon[i].spawn === true) {
            continue;
        }
        newHypo = Math.sqrt(Math.pow(Math.abs(gamePieces.daqloon[i].x - gamePieces.player.xpos), 2) 
               + Math.pow(Math.abs(gamePieces.daqloon[i].y - gamePieces.player.ypos), 2));
        if(hypo === null || newHypo < hypo) {
            hypo = newHypo;
            nearestDaqloon = gamePieces.daqloon[i].id;
        }
    }
    console.log(nearestDaqloon);
    return nearestDaqloon;
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
    this.diameterUp = this.y;
    this.diameterLeft = this.x;
    this.diameterDown = this.y + this.height;
    this.diameterRight = this.x + this.width;
    this.drawX = 0;
    this.drawY = 0;
    this.spriteXIndex = 0;
    this.spriteYIndex = 2;
    this.sprite = new Image(160, 64);
    this.oldX = 0;
    this.oldY = 0;
    this.up = false,
    this.right = false;
    this.down = false;
    this.left = false;
    this.health = 10;
    this.movementSpeed = 90;
    this.dead = false;
    this.attack = false;
    this.cooldown = false;
    this.spawn = true;
    this.nearbyPlayer = true;
    this.moxeX = 0;
    this.hitMessage = false;
    this.setDiameter = function () {
        this.diameterUp = this.y;
        this.diameterLeft = this.x;
        this.diameterDown = this.y + this.height;
        this.diameterRight = this.x + this.width;
    };
    this.resetDirections = function() {
        this.up = false;
        this.right = false;
        this.down = false;
        this.left = false;
    };
    this.hit = function() {
        game.properties.context3.drawImage(this.sprite,
                                           7 * 32,
                                           this.spriteYIndex * 32,
                                           32 * viewport.scale,
                                           32 * viewport.scale,
                                           this.drawX - (gamePieces.player.xMovement * viewport.scale),
                                           this.drawY - (gamePieces.player.yMovement * viewport.scale), this.width, this.height);
        let damage  = getRndInteger(0, gamePieces.player.attackDamage);
        this.health -= damage;
        this.hitMessage = game.properties.duration;
        game.properties.textContext.font = "18px Times New Roman";
        game.properties.textContext.fillStyle = "#FFFFFF";
        game.properties.textContext.fillText(damage, this.drawX - (gamePieces.player.xMovement * viewport.scale), 
                                             this.drawY - (gamePieces.player.yMovement * viewport.scale));

        this.drawHealthBar(this.drawX - (gamePieces.player.xMovement * viewport.scale),
                           this.drawY - (gamePieces.player.yMovement * viewport.scale));
        if(this.health <= 0) {
            console.log('health 0');
            this.spriteXIndex = 0;
            this.dead = true;   
        }
    };
    this.locater = function() {
        let direction = "Located "
        // Find locater;
        if(gamePieces.player.ypos < this.y) {
            direction += "south";
            if(gamePieces.player.xpos - this.x > -200 ) {
                direction += ", east";
            } else if(gamePieces.player.xpos - this.x < 200) {
                direction += ", west";
            }
        } else {
            direction += "north";
            if(gamePieces.player.xpos < this.x) {
                direction += ", east";
            } else if(gamePieces.player.xpos > this.x) {
                direction += ", west";
            }
        }
        if(gamePieces.daqloon_fighting_area.diameterUp > gamePieces.player.ypos ||
           gamePieces.daqloon_fighting_area.diameterDown < gamePieces.player.ypos ||
           gamePieces.daqloon_fighting_area.diameterRight < gamePieces.player.xpos ||
           gamePieces.daqloon_fighting_area.diameterLeft > gamePieces.player.xpos) {
            direction = "Not in daqloon area";
        }
        document.getElementById("HUD_hunted_locater").innerHTML = direction;
    };
    this.drawHealthBar = function(x, y) {
        if(this.health <= 0) return false;
        let remainingHealth = 100 - this.health;
        if(this.health !== 100) {
            game.properties.context3.fillStyle = "#4d0000";
            game.properties.context3.fillRect(x + 5, y - 20, 0.35 * 100, 10);
        }
        game.properties.context3.fillStyle = "red";
        game.properties.context3.fillRect(x + 5, y - 20, 0.35 * this.health, 10);
    
    };
    this.checkNearBy = function() {
        console.log(gamePieces.daqloon.findIndex(this.findOtherDaqloons));
    };
    this.drawOnCanvas = function() {
        game.properties.context3.drawImage(this.sprite,
        this.spriteXIndex * 32,
        this.spriteYIndex * 32,
        32 * viewport.scale,
        32 * viewport.scale,
        this.drawX - (gamePieces.player.xMovement * viewport.scale),
        this.drawY - (gamePieces.player.yMovement * viewport.scale), this.width, this.height);
        if(gamePieces.player.attackedBy === this.id) {
            this.locater();
            this.drawHealthBar(this.drawX - (gamePieces.player.xMovement * viewport.scale),
            this.drawY - (gamePieces.player.yMovement * viewport.scale));
        }
        if(this.hitMessage !== false && game.properties.duration - this.hitMessage > 10) {
            this.hitMessage = false;
            game.properties.textContext.clearRect(0, 0, 700, 700);
        }
        this.setDiameter();
        this.resetDirections();
    }
    this.draw = function() {
        if(Math.abs(this.x - gamePieces.player.xpos) < 20 && Math.abs(this.y - gamePieces.player.ypos) < 20 &&
            this.attack === false && this.cooldown === false && this.spawn === false) {
            this.attack = true;
            this.cooldown = true;
            gamePieces.player.takeDamage(this.attackDamage);
            // 5
            // 6
        }
        // If health is over 10 calculateMovement
        if(this.health > 0)  {
            this.calculateMovement();
        }
        
        if(this.spawn == true && this.dead == false && game.properties.duration % 15 == 0) {
            if(this.spriteXIndex >= 2) {
                this.spriteXIndex = 0;
                this.spawn = false;
                this.spriteYIndex = 0;
            }
            else {
                this.spriteXIndex++;
            }
        }
        else if(this.dead === true && game.properties.duration % 10 === 0) {
            // If spriteXIndex is 5, the death animation is complete
            if(this.spriteXIndex == 5) {
                this.spriteXIndex = 3;
                gamePieces.daqloon[this.id].spawn = true;
                gamePieces.player.attackedBy = getNearestDaqloon();
                let lootItem = (getRndInteger(0, 30) === 30) ? 'daqloon horns' : 'daqloon scale';
                gamePieces.items.push(new item(this.drawX, this.drawY, lootItem));
                setTimeout(() => {
                    checkDaqloon(1, this.id), 5000;
                });
                return false;
            }
            else if(this.spriteXIndex < 3) {
                this.spriteXIndex = 3;
            }
            this.spriteYIndex = 2;
            this.spriteXIndex++;
        }
        else if(game.properties.duration % 6 === 0 && this.attack === true && this.dead === false && this.spawn === false) {
            // Set start xIndex for attack animation
            if(this.spriteXIndex < 5) {
                this.spriteXIndex = 5;
                setTimeout(() => {
                    this.cooldown = false;
                }, 3000);
            }
            else {
                // Quit attack animation and set cooldown
                if(this.spriteXIndex === 6) {
                    this.spriteXIndex = 1;
                    this.attack = false;
                }
                else {
                    this.spriteXIndex++;
                }
            }
        }
        else if(game.properties.duration % 10 === 0 && this.dead === false && this.spawn === false && this.attack === false) {
            this.spriteXIndex++;
        }
        if(this.spriteXIndex > 4 && this.attack == false && this.dead == false && this.spawn === false) {
            this.spriteXIndex = 1;
        }
        this.drawOnCanvas();
    };
    this.calculateMovement = function() {
        let distanceX = gamePieces.player.xpos - this.x;
        let distanceY = gamePieces.player.ypos - this.y;
        // Check if there are nearby daqloons, if so prevent them from "crashing"
        // let nearbyIndex = this.findOtherDaqloons();
        let nearbyX = 1;
        let nearbyY = 1;
        let debug = true;
        // game.calculateDistance();
        if((Math.abs(distanceX) < 250 || Math.abs(distanceY) < 250) && gamePieces.player.attackedBy === this.id) {
            for(let i = 0; i < gamePieces.nearObjects.length; i++) {
                if(Math.abs(this.diameterRight - gamePieces.nearObjects[i].diameterLeft) <= 2 &&
                this.diameterUp <= gamePieces.nearObjects[i].diameterDown &&
                this.diameterDown >= gamePieces.nearObjects[i].diameterUp) {
                    this.right = "blocked";
                }
                if(Math.abs(this.diameterDown - gamePieces.nearObjects[i].diameterUp) <= 2 &&
                this.diameterRight >= gamePieces.nearObjects[i].diameterLeft &&
                this.diameterLeft <= gamePieces.nearObjects[i].diameterRight) {
                    this.down = "blocked";
                }
                if(Math.abs(this.diameterUp - gamePieces.nearObjects[i].diameterDown) <= 2 &&
                this.diameterRight >= gamePieces.nearObjects[i].diameterLeft &&
                this.diameterLeft <= gamePieces.nearObjects[i].diameterRight) {
                    this.up = "blocked";
                    if (debug == true) {
                    }
                }
                if(Math.abs(this.diameterLeft - gamePieces.nearObjects[i].diameterRight) <= 2 &&
                this.diameterUp <= gamePieces.nearObjects[i].diameterDown &&
                this.diameterDown >= gamePieces.nearObjects[i].diameterUp) {
                    this.left = "blocked";
                    if (debug == true) {
                    }
                }
            }
            this.nearbyPlayer = true;
            this.oldX = this.x;
            if(distanceX > 3 && this.x + 1 < gamePieces.daqloon_fighting_area.diameterRight) {
                /*this.x+= 2;
                this.drawX += 2;*/
                if(this.right !== "blocked") {
                    this.x += 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                    this.drawX += 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                }
            }
            else if(distanceX < -3 && this.x - 1 > gamePieces.daqloon_fighting_area.diameterLeft){
                /*this.x -= 2;
                this.drawX-= 2;*/
                if(this.left !== "blocked") {
                    this.x -= 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                    this.drawX -= 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                }
            }

            this.oldY = this.y;
            if(distanceY > 3 && this.y + 1 < gamePieces.daqloon_fighting_area.diameterDown) {
                if(this.down !== "blocked") {
                    this.y += 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                    this.drawY += 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                }
            }
            else if(distanceY < -3 && this.y - 1 > gamePieces.daqloon_fighting_area.diameterUp){
                if(this.up !== "blocked") {
                    this.y -= 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                    this.drawY -= 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                }
            }
            // If player ypos is greater than y pos of the NPC set the spriteYIndex to 0
            if(this.diameterUp  < gamePieces.player.diameterUp+ 30) {
                this.spriteYIndex = 0;
            }
            else {
                this.spriteYIndex = 1;
            }
        }
        else {
            if(game.properties.duration % 30 === 0) {
                this.spriteYIndex = getRndInteger(0,1);
                this.moveX = getRndInteger(0, 2);
            }
            switch(this.spriteYIndex) {
                case 0: 
                    if(this.y < gamePieces.daqloon_fighting_area.diameterDown) {
                        this.y += 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                        this.drawY += 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                    }
                    break;
                case 1:
                    if(this.y > gamePieces.daqloon_fighting_area.diameterUp) {
                        this.y -= 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                        this.drawY -= 1 * (game.properties.delta * this.movementSpeed) * nearbyY;
                    }
                    break;
            }
            switch(this.moveX) {
                case 0:

                    break;
                case 1:
                    if(this.x > gamePieces.daqloon_fighting_area.diameterLeft) {
                        this.x -= 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                        this.drawX -= 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                    } 
                    break;
                case 2:
                    if(this.x < gamePieces.daqloon_fighting_area.diameterRight) {
                        this.x += 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                        this.drawX += 1 * (game.properties.delta * this.movementSpeed) * nearbyX;
                    }
                    break;
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