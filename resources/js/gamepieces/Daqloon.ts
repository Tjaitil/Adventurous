import { MovingGameObject, DirectionBlockedCheck } from "../types/gamepieces/MovingGameObject.js";
import { Game } from "../advclient.js";
import viewport from "../clientScripts/viewport.js";
import { getRandomInteger } from "../utilities/getRandomInteger.js";
import { GamePieces } from "../clientScripts/gamePieces.js";
import { collisionCheck } from "../clientScripts/collision.js";
import { AssetPaths } from "../clientScripts/ImagePath.js";

export class Daqloon implements MovingGameObject {
    id: number;
    index = 1;
    attackDamage = 15;
    defence = 10;
    x: number;
    y: number;
    width = 54;
    height = 54;
    diameterUp: number;
    diameterLeft: number;
    diameterDown: number;
    diameterRight: number;
    drawX = 0;
    drawY = 0;
    spriteXIndex = 0;
    spriteYIndex = 2;
    sprite = new Image(160, 64);
    src = "";
    type = "daqloon";
    visible = true;
    noCollision = true;
    oldX = 0;
    oldY = 0;
    up: DirectionBlockedCheck;
    right: DirectionBlockedCheck;
    down: DirectionBlockedCheck;
    left: DirectionBlockedCheck;
    controlsUp = false;
    controlsRight = false;
    controlsDown = false;
    controlsLeft = false;
    health: number;
    maxHealth = 100;
    movementSpeed = 60;
    currentAnimation: string = "idle"
    speedX = 0;
    speedY = 0;
    dead = false;
    attack = false;
    cooldown = false;
    spawn = true;
    nearbyPlayer = true;
    moveX = 0;
    hitMessage = -1;
    // TODO: Fix object
    fighting_area = null;

    constructor(id: number, x: number, y: number, fighting_area: Object) {
        this.id = id;
        this.x = x;
        this.y = y;

        this.diameterUp = y;
        this.diameterLeft = x;
        this.diameterDown = x + this.height;
        this.diameterRight = x + this.width;
        this.fighting_area = fighting_area;

        this.health = 10;
        this.drawX = Math.round(this.x - viewport.offsetX);
        this.drawY = Math.round(this.y - viewport.offsetY);
        this.sprite.src = AssetPaths.getImagePath("daqloon sprite.png");
    }

    setDiameter() {
        this.diameterUp = this.y;
        this.diameterLeft = this.x;
        this.diameterDown = this.y + this.height;
        this.diameterRight = this.x + this.width;
    }

    resetDirections() {
        this.up = "";
        this.right = "";
        this.down = "";
        this.left = "";
    }

    hit(direction: string) {
        if (this.dead) return false;
        if (direction === "up") {
            this.y -= 10;
            this.drawY -= 10;
        } else {
            this.drawY += 10;
            this.y += 10;
        }
        this.setDiameter();
        this.calculateNewPosition();
        this.setStartAnimationPoint("damage");
        this.drawOnCanvas();

        let damage = getRandomInteger(0, GamePieces.player.attackDamage);
        this.health -= damage;
        this.hitMessage = Game.properties.duration;
        viewport.drawText(
            "18px Times New Roman",
            "#FFFFFF",
            damage,
            this.drawX - GamePieces.player.xMovement * viewport.scale + 40,
            this.drawY - GamePieces.player.yMovement * viewport.scale + 20
        );
        this.drawHealthBar(
            this.drawX - GamePieces.player.xMovement * viewport.scale,
            this.drawY - GamePieces.player.yMovement * viewport.scale
        );
        if (this.health <= 0) {
            this.spriteXIndex = 0;
            this.dead = true;
        }
    }

    locater() {
        let direction = "Located ";
        // Find locater;
        if (GamePieces.player.ypos < this.y) {
            direction += "south";
            if (GamePieces.player.xpos - this.x > -200) {
                direction += ", east";
            } else if (GamePieces.player.xpos - this.x < 200) {
                direction += ", west";
            }
        } else {
            direction += "north";
            if (GamePieces.player.xpos < this.x) {
                direction += ", east";
            } else if (GamePieces.player.xpos > this.x) {
                direction += ", west";
            }
        }
        if (
            this.fighting_area.diameterUp > GamePieces.player.ypos ||
            this.fighting_area.diameterDown < GamePieces.player.ypos ||
            this.fighting_area.diameterRight < GamePieces.player.xpos ||
            this.fighting_area.diameterLeft > GamePieces.player.xpos
        ) {
            direction = "Not in daqloon area";
        }
        // TODO: This causes performance issues. Need to find a better way to do this.
        // HUD.elements.huntedLocator.innerHTML = direction;
    }

    drawHealthBar(x: number, y: number) {
        if (this.health <= 0) return false;
        let remainingHealth = 100 - this.health;
        if (this.health !== 100) {
            viewport.drawDaqloonHealthbar("#4d0000", x + 5, y - 20, 0.35 * 100, 10);
        }
        viewport.drawDaqloonHealthbar("red", x + 5, y - 20, 0.35 * this.health, 10);
    }

    public drawOnCanvas() {
        viewport.drawSprite(
            this.sprite,
            this.spriteXIndex * 32,
            this.spriteYIndex * 32,
            32 * viewport.scale,
            32 * viewport.scale,
            this.drawX - GamePieces.player.xMovement,
            this.drawY - GamePieces.player.yMovement,
            this.width,
            this.height
        );
        if (GamePieces.player.attackedBy === this.id) {
            this.locater();
            this.drawHealthBar(
                this.drawX - GamePieces.player.xMovement * viewport.scale,
                this.drawY - GamePieces.player.yMovement * viewport.scale
            );
        }
        if (this.hitMessage !== -1 && Game.properties.duration - this.hitMessage > 10) {
            this.hitMessage = -1;
            viewport.resetTextLayer();
        }
        this.setDiameter();
        this.resetDirections();
    }

    draw() {
        // TODO: This detection is way too low
        if (
            ((Math.abs(this.diameterLeft - GamePieces.player.xpos) < 20 ||
                Math.abs(this.diameterRight - GamePieces.player.xpos) < 20
            ) && Math.abs(this.diameterUp + (this.height / 2) - GamePieces.player.diameterUp) < 20)
            &&
            this.attack === false &&
            this.cooldown === false &&
            this.spawn === false
        ) {
            this.attack = true;
            this.cooldown = true;
            if (GamePieces.player.combatActions.block) {
                console.log("BLOCKED");
                viewport.drawText("18px Times New Roman", "#FFFFFF", "BLOCKED",
                    20, viewport.height / 2);
            } else {
                GamePieces.player.takeDamage(this.attackDamage);
            }
        }
        // If health is over 10 calculateMovement
        if (this.health > 0) {
            this.calculateMovement();
        }
        if (this.currentAnimation === "damage") {
            this.setStartAnimationPoint("idle");
        } else if (this.spawn == true && this.dead == false) {
            if (Game.properties.duration % 10 === 0) {

                if (this.spriteXIndex >= 2) {
                    this.spriteYIndex = 2;
                    this.spawn = false;
                    this.health = 10;
                    GamePieces.daqloon_fighting_area.findHuntingDaqloon();
                    this.setStartAnimationPoint("idle");
                } else {
                    this.spriteXIndex++;
                }
            }
        } else if (this.dead === true && Game.properties.duration % 10 === 0) {
            // If spriteXIndex is 5, the death animation is complete
            this.setStartAnimationPoint("death");
            if (this.spriteXIndex == 5) {
                this.spriteXIndex = 3;
                // let lootItem = getRandomInteger(0, 30) === 30 ? "daqloon horns" : "daqloon scale";
                // GamePieces.items.push(new item(this.drawX, this.drawY, lootItem));
                this.dead = false;
                this.spawn = true;
                this.setStartAnimationPoint("spawn");
            }
            this.spriteXIndex++;
        } else if (
            Game.properties.duration % 6 === 0 &&
            this.attack === true &&
            this.dead === false &&
            this.spawn === false
        ) {
            // Set start xIndex for attack animation
            if (this.spriteXIndex < 5) {
                this.spriteXIndex = 5;
                setTimeout(() => {
                    this.cooldown = false;
                }, 3000);
            } else {
                // Quit attack animation and set cooldown
                if (this.spriteXIndex === 6) {
                    this.spriteXIndex = 1;
                    this.attack = false;
                } else {
                    this.spriteXIndex++;
                }
            }
        } else if (
            Game.properties.duration % 10 === 0 &&
            this.dead === false &&
            this.spawn === false &&
            this.attack === false
        ) {
            this.spriteXIndex++;
            if (this.spriteXIndex > 4) {
                this.spriteXIndex = 1;
            }
        }

        this.drawOnCanvas();
    }

    private calculateNewPosition() {
        collisionCheck(this);
        this.x += this.speedX;
        this.drawX += this.speedX;
        this.y += this.speedY;
        this.drawY += this.speedY;
    }

    private calculateMovement() {
        let distanceX = GamePieces.player.xpos - this.x;
        let distanceY = GamePieces.player.ypos - this.y;
        let debug = true;

        // Variables to determine how much a daqloon should move if it is able to
        let move = this.movementSpeed * Game.properties.delta;
        if ((Math.abs(distanceX) < 250 || Math.abs(distanceY) < 250) || GamePieces.player.attackedBy === this.id) {
            this.nearbyPlayer = true;

            if (distanceX > 6 && this.x + 1 < this.fighting_area.diameterRight) {
                if (this.right !== "blocked") {
                    this.speedX = move;
                }
            } else if (distanceX < -6 && this.x - 1 > this.fighting_area.diameterLeft) {
                if (this.left !== "blocked") {
                    this.speedX = -move;
                }
            }
            if (distanceY > 6 && this.y + 1 < this.fighting_area.diameterDown) {
                if (this.down !== "blocked") {
                    this.speedY = move;
                }
            } else if (distanceY < -6 && this.y - 1 > this.fighting_area.diameterUp) {
                if (this.up !== "blocked") {
                    this.speedY = -move;
                }
            }

            // If player ypos is greater than y pos of the NPC set the spriteYIndex to 0
            if (this.diameterUp < GamePieces.player.diameterUp + 30) {
                this.spriteYIndex = 0;
            } else {
                this.spriteYIndex = 1;
            }
        } else {
            if (Game.properties.duration % 30 === 0) {
                this.spriteYIndex = getRandomInteger(0, 1);
                this.moveX = getRandomInteger(0, 2);
            }
            switch (this.spriteYIndex) {
                case 0:
                    if (this.y < this.fighting_area.diameterDown) {
                        this.speedY = move;
                    } else {
                        this.speedY = 0;
                    }
                    break;
                case 1:
                    if (this.y > this.fighting_area.diameterUp) {
                        this.speedY = -move;
                    } else {
                        this.speedY = 0;
                    }
                    break;
            }
            switch (this.moveX) {
                case 0:
                    break;
                case 1:
                    if (this.x > this.fighting_area.diameterLeft) {
                        this.speedX = -move;
                    }
                    break;
                case 2:
                    if (this.x < this.fighting_area.diameterRight) {
                        this.speedX = move;
                    }
                    break;
            }
        }

        this.calculateNewPosition();
    }

    setStartAnimationPoint(type: "spawn" | "death" | "attack" | "damage" | "idle") {

        if (type === "damage" && this.currentAnimation !== "damage") {
            this.currentAnimation = "damage";
            this.spriteXIndex = 7;
        }
        else if (type === "spawn" && this.currentAnimation !== "spawn") {
            this.currentAnimation = "spawn";
            this.spriteXIndex = 0;
            this.spriteYIndex = 2;
        } else if (type === "death" && this.currentAnimation !== "death") {
            this.currentAnimation = "death";
            this.spriteXIndex = 3;
            this.spriteYIndex = 2;
        } else if (type === "attack" && this.currentAnimation !== "attack") {
            this.currentAnimation = "attack";
            this.spriteXIndex = 5;
            this.spriteYIndex = 2;
        } else if (type === "idle" && this.currentAnimation !== "idle") {
            this.currentAnimation = "idle";
        }
    }
}
