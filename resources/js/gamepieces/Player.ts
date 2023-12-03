import { MovingGameObject, DirectionBlockedCheck } from "../types/gamepieces/MovingGameObject";
import { canvasTextHeader } from "../clientScripts/canvasText";
import { controls } from "../clientScripts/controls";
import { Game } from "../advclient";
import viewport from "../clientScripts/viewport";
import { GamePieces } from "../clientScripts/gamePieces";
import { HUD } from "../clientScripts/HUD";
import { AssetPaths } from "../clientScripts/ImagePath";

export class Player implements MovingGameObject {
    width = 36;
    height = 36;
    speedX = 0;
    speedY = 0;
    speed = 1.5;
    x = null;
    y = null;
    travel = false;
    attack = false;
    xMovement = 0;
    yMovement = 0;
    drawX: 0;
    drawY: 0;
    src: "";
    sprite = new Image(96, 128);
    headSprite = new Image(32, 224);
    bodySprite = new Image(32, 224);
    legsSprite = new Image(32, 224);
    spriteAttack = new Image(114, 32);
    type: "player";
    visible: true;
    // xTracker and yTracker used for tracking x and y l
    xTracker = 0;
    yTracker = 0;
    up: DirectionBlockedCheck;
    left: DirectionBlockedCheck;
    down: DirectionBlockedCheck;
    right: DirectionBlockedCheck;
    playerSize = 36;
    diameterUp = this.y;
    diameterRight = this.x + this.width - 5;
    diameterDown = this.y + 28;
    diameterLeft = this.x + 5;
    // xpos and ypos is the position of the player in the world
    xpos: number;
    ypos: number;
    oldXbase = 0;
    oldYbase = 0;
    animationEnd = true;
    attackedBy = -1;
    hunted = false;
    loopIndex = 0;
    counter = 0;
    direction = "none";
    loopArray = [0, 1, 0, 2];
    indexX = 32;
    indexY = 0;
    index = 0;
    attackLoop = 0;
    lastAttack = 0;
    attackSpeed = 10;
    combat = false;
    attackDamage = 10;
    cooldown = 0;
    combatActions = {
        attack: false,
        block: false,
    };
    combatActionsAnimationStarted = {
        attack: false,
        block: false,
    };
    isInCombatAction = false;
    cooldowns = {
        block: 0,
    };
    movementSpeed = 60;
    currentAnimation: string = "idle";
    health = 100;
    regenerateCoundown = false;
    newDirection: string;
    // imageFix to adjust character sprite not being in 32 x 32 format
    imageFix = 0;
    noCollision = true;
    ranged = true;

    setup() {
        this.setHuntedStatus(false);
        this.draw();
        this.sprite.src = AssetPaths.getImagePath("character1.png");
        this.spriteAttack.src = AssetPaths.getImagePath("character attack2.png");
        this.headSprite.src = AssetPaths.getImagePath("character head.png");
        this.bodySprite.src = AssetPaths.getImagePath("character body.png");
        this.legsSprite.src = AssetPaths.getImagePath("character legs.png");
        this.diameterUp = this.y;
        this.diameterRight = this.x + this.width - 5;
        this.diameterDown = this.y + 28;
        this.diameterLeft = this.x + 5;
        (<any>window).player = typeof Player;
    }

    load(xbase, ybase, nearestDaqloon) {
        this.xMovement = 0;
        this.yMovement = 0;
        this.attackedBy = nearestDaqloon;
        this.x = this.xpos = xbase;
        this.y = this.ypos = ybase;
        this.diameterUp = this.y + 20;
        this.diameterRight = this.x + this.width;
        this.diameterDown = this.y + this.height;
        this.diameterLeft = this.x;
        this.setup();
    }

    setIsInCombatAction(status) {
        this.isInCombatAction = status;
    }

    checkPosition() {
        if (
            this.ypos > 3170 ||
            (this.ypos < 3100 && this.ypos < 10) ||
            this.xpos > 3170 ||
            (this.xpos < 3170 && this.xpos < 10)
        ) {
            return true;
        } else {
            return false;
        }
    }

    takeDamage(damage) {
        if (isNaN(damage) || damage === 0) return false;
        // Draw sprite that takes damage
        viewport.drawPlayer({
            img: this.spriteAttack,
            spriteX: 41 * 2,
            spriteY: 38 * 1,
            sWidth: 32,
            sHeight: 32,
            width: this.playerSize,
            height: this.playerSize,
        });
        this.health -= damage;
        if (this.health < 0) this.health = 0;

        // Player died
        if (this.health <= 0) {
            canvasTextHeader.setDraw("You died!", 2);

            this.health = 100;
            let newX;
            let newY;
            // Locate nearest town
            switch (Game.properties.currentMap) {
                case "6.7":
                    newX = 5;
                    newY = 7;
                    break;
                case "8.3":
                    newX = 8;
                    newY = 2;
                    break;
                case "3.10":
                    newX = 4;
                    newY = 9;
                    break;
                default:
                    break;
            }
            setTimeout(
                () =>
                    Game.setWorld({
                        method: "changeMap",
                        newxBase: newX,
                        newyBase: newY,
                    }),
                2000
            );
        }
        HUD.elements.healthProgressBar.setCurrentValue(this.health);
    }

    drawHead() {
        viewport.drawPlayer({
            img: this.headSprite,
            spriteX: this.indexX * this.loopIndex,
            spriteY: 0,
            sWidth: 32,
            sHeight: 32,
            width: this.playerSize,
            height: this.playerSize,
        });
    }

    drawBody() {
        viewport.drawPlayer({
            img: this.bodySprite,
            spriteX: this.indexX * this.loopIndex,
            spriteY: 0,
            sWidth: 32,
            sHeight: 32,
            width: this.playerSize,
            height: this.playerSize,
        });
    }

    drawLegs() {
        viewport.drawPlayer({
            img: this.legsSprite,
            spriteX: this.indexX * this.loopIndex,
            spriteY: 0,
            sWidth: 32,
            sHeight: 32,
            width: this.playerSize,
            height: this.playerSize,
        });
    }

    draw() {
        viewport.resetPlayerLayer();
        // this.drawLegs();
        // this.drawBody();
        // this.drawHead();

        let drawImage = this.sprite;
        let spriteX;
        let spriteY;

        // Determine which image and calculate spriteX and spriteY there after
        if (this.combat === true) {
            drawImage = this.spriteAttack;
            spriteX = 41 * this.loopIndex + this.imageFix;
            spriteY = 38 * this.indexY;
        } else {
            drawImage = this.sprite;
            spriteX = this.indexX * this.loopIndex;
            spriteY = this.indexY;
        }

        viewport.drawPlayer({
            img: drawImage,
            spriteX,
            spriteY,
            sWidth: 32,
            sHeight: 32,
            width: this.playerSize,
            height: this.playerSize,
        });
    }

    drawCooldown() {
        this.cooldown -= 3;
        viewport.drawAttackCoolDown(this.cooldown);
    }

    drawBlock() {
        this.cooldowns.block -= 3;
        viewport.drawBlockCoolDown(this.cooldowns.block);
    }

    setHuntedStatus(status: boolean) {
        this.hunted = status;
        if (this.hunted === true) {
            HUD.elements.huntedIcon.style.visibility = "visible";
        } else {
            HUD.elements.huntedIcon.style.visibility = "hidden";
        }
    }

    regenerateHealth() {
        if (this.health <= 0) return;
        this.health + 10 > 100 ? (this.health = 100) : (this.health += 10);
        HUD.elements.healthProgressBar.setCurrentValue(this.health);

        this.regenerateCoundown = false;
    }

    newPos(newPos = true) {
        // if (this.health < 100 && this.regenerateCoundown === false) {
        //     this.regenerateCoundown = true;
        //     setTimeout(() => this.regenerateHealth(), 7000);
        // }
        this.up = "";
        this.left = "";
        this.down = "";
        this.right = "";
        // //drawing starts at x (diameterLeft) and y (diameterUp) line
        if (newPos !== false) {
            this.xpos = Game.properties.xbase + this.xMovement;
            this.ypos = Game.properties.ybase + this.yMovement;
            // game.properties.xMapMin = this.xpos - 320;
            // game.properties.xMapMax = this.xpos + 320;
            // game.properties.yMapMin = this.ypos - 320;
            // game.properties.yMapMax = this.ypos + 320;
            this.diameterUp = this.ypos + 20;
            this.diameterRight = this.xpos + this.width - 4;
            this.diameterDown = this.ypos + this.height;
            this.diameterLeft = this.xpos + 4;
        }
        this.determineDirection();
        if (this.combat === true && !this.ranged && this.combatActions.attack) {
            let newDirection = "none";
            if (controls.playerDown === true) {
                newDirection = "down";
                this.direction = "down";
                this.indexY = 0;
                this.imageFix = 10;
            } else if (controls.playerUp === true) {
                newDirection = "up";
                this.direction = "up";
                this.indexY = 2;
                this.imageFix = 5;
            }
            // If direction direction is left or right then draw sprite heading down
            if (
                (newDirection === "undefined" || newDirection == "none") &&
                (controls.playerRight === true || controls.playerLeft === true)
            ) {
                if (this.direction === "up") {
                    this.indexY = 2;
                    this.imageFix = 5;
                } else {
                    this.indexY = 0;
                    this.imageFix = 10;
                }
            }
            if (this.attack === true && Game.properties.duration % 2 === 0 && this.cooldown <= 0) {
                this.indexY += 1;
                if (this.attackLoop === 0) {
                    this.loopIndex = 0;
                    let direction;
                    for (let i = 0; i < GamePieces.daqloon.length; i++) {
                        if (
                            (this.direction === "up" &&
                                GamePieces.daqloon[i].y < this.ypos &&
                                Math.abs(GamePieces.daqloon[i].x - this.xpos) < 30 &&
                                Math.abs(GamePieces.daqloon[i].diameterDown - this.ypos) < 16) ||
                            (this.direction === "down" &&
                                Math.abs(GamePieces.daqloon[i].x - this.xpos) < 64 &&
                                Math.abs(GamePieces.daqloon[i].y - this.ypos + 32) < 60)
                        ) {
                            if (this.direction === "down") {
                                direction = "down";
                            } else {
                                direction = "up";
                            }
                            GamePieces.daqloon[i].hit(direction);
                        }
                    }
                }
                // this.draw();
                this.loopIndex++;
                // Attack is finished on attackLoop 2
                if (this.attackLoop === 1) {
                    this.attack = false;
                    this.cooldown = 100;
                    this.attackLoop = 0;
                } else {
                    this.attackLoop++;
                }
            } else if (Game.properties.duration % 10 === 0 && this.attack === false) {
                if (this.loopIndex > 3) {
                    this.loopIndex = 0;
                }
                // this.draw();
                this.loopIndex++;
            }
        } else if (this.combatActions.block) {
            if (this.combatActionsAnimationStarted.block === false) {
                this.combatActionsAnimationStarted.block = true;
                this.cooldowns.block = 100;
            } else if (this.cooldowns.block <= 0) {
                this.combatActionsAnimationStarted.block = false;
            } else if (Game.isGameDuration(60)) {
                console.log('block down');
                this.combatActions.block = false;
            }
            this.draw();
        } else {
            if (this.newDirection != "none" && (this.oldYbase != this.ypos || this.oldXbase != this.xpos)) {
                if (this.newDirection != "none" && Game.isGameDuration(10)) {
                    this.draw();
                    this.loopIndex++;
                } else if (this.newDirection != this.direction) {
                    this.loopIndex = 1;
                    this.direction = this.newDirection;
                    this.draw();
                    this.loopIndex++;
                }
                if (this.loopIndex == 5 && this.newDirection != "none") {
                    this.loopIndex = 1;
                }
                this.counter++;
                this.animationEnd = false;
            } else {
                this.loopIndex = 0;
                this.draw();

                this.animationEnd = true;
            }
        }

        if (this.cooldown > 0) {
            this.drawCooldown();
        }
        if (this.cooldowns.block > 0) {
            this.drawBlock();
        }
        this.oldYbase = this.ypos;
        this.oldXbase = this.xpos;
    }

    determineDirection() {
        this.newDirection = "none";
        if (controls.playerLeft == true && controls.playerDown == true) {
            this.newDirection = "left, down";
        }
        if (controls.playerRight == true && controls.playerUp == false && controls.playerDown == false) {
            this.newDirection = "right";
            this.indexY = 32;
        }
        if (controls.playerLeft == true && controls.playerUp == false && controls.playerDown == false) {
            this.newDirection = "left";
            this.indexY = 64;
        }
        if (controls.playerDown == true) {
            this.newDirection = "right, down";
            this.indexY = 0;
        }
        if (controls.playerUp == true) {
            this.newDirection = "right, up";
            this.indexY = 96;
        }
        if (controls.playerRight == true && controls.playerUp == false && controls.playerDown == false) {
            this.newDirection = "right";
            this.indexY = 32;
        }
        if (
            controls.playerUp == false &&
            controls.playerLeft == false &&
            controls.playerRight == false &&
            controls.playerDown == false
        ) {
            this.newDirection = "none";
        }
    }
}
