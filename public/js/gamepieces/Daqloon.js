import { Game } from "../advclient.js";
import viewport from "../clientScripts/viewport.js";
import { getRandomInteger } from "../utilities/getRandomInteger.js";
import { GamePieces } from "../clientScripts/gamePieces.js";
import { collisionCheck } from "../clientScripts/collision.js";
export class Daqloon {
    id;
    index = 1;
    attackDamage = 15;
    defence = 10;
    x;
    y;
    width = 45;
    height = 45;
    diameterUp;
    diameterLeft;
    diameterDown;
    diameterRight;
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
    up;
    right;
    down;
    left;
    controlsUp = false;
    controlsRight = false;
    controlsDown = false;
    controlsLeft = false;
    health;
    movementSpeed = 1.5;
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
    constructor(id, x, y, fighting_area) {
        this.id = id;
        this.x = x;
        this.y = y;
        this.diameterUp = y;
        this.diameterLeft = x;
        this.diameterDown = x + this.height;
        this.diameterRight = x + this.width;
        this.fighting_area = fighting_area;
        this.health = 100;
        this.drawX = Math.round(this.x - viewport.offsetX);
        this.drawY = Math.round(this.y - viewport.offsetY);
        console.log(this);
        this.sprite.src = "public/images/daqloon sprite.png";
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
    hit(direction) {
        if (this.dead) return false;
        if (direction === "up") {
            this.speedY = 20;
            this.speedX = 20;
        } else {
            this.speedX -= 20;
            this.speedY -= 20;
        }
        this.calculateNewPosition();
        this.spriteXIndex = 7;
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
        document.getElementById("HUD_hunted_locater").innerHTML = direction;
    }
    drawHealthBar(x, y) {
        if (this.health <= 0) return false;
        let remainingHealth = 100 - this.health;
        if (this.health !== 100) {
            viewport.drawDaqloonHealthbar(
                "#4d0000",
                x + 5,
                y - 20,
                0.35 * 100,
                10
            );
        }
        viewport.drawDaqloonHealthbar(
            "red",
            x + 5,
            y - 20,
            0.35 * this.health,
            10
        );
    }
    checkNearBy() {
        console.log(GamePieces.daqloon.findIndex(this.findOtherDaqloons));
    }
    drawOnCanvas() {
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
        if (
            this.hitMessage !== -1 &&
            Game.properties.duration - this.hitMessage > 10
        ) {
            this.hitMessage = -1;
            // viewport.resetTextLayer();
        }
        this.setDiameter();
        this.resetDirections();
    }
    draw() {
        if (
            Math.abs(this.x - GamePieces.player.xpos) < 20 &&
            Math.abs(this.y - GamePieces.player.ypos) < 20 &&
            this.attack === false &&
            this.cooldown === false &&
            this.spawn === false
        ) {
            this.attack = true;
            this.cooldown = true;
            GamePieces.player.takeDamage(this.attackDamage);
            // 5
            // 6
        }
        // If health is over 10 calculateMovement
        if (this.health > 0) {
            this.calculateMovement();
        }
        if (
            this.spawn == true &&
            this.dead == false &&
            Game.properties.duration % 15 == 0
        ) {
            if (this.spriteXIndex >= 2) {
                this.spriteXIndex = 0;
                this.spawn = false;
                this.spriteYIndex = 0;
            } else {
                this.spriteXIndex++;
            }
        } else if (this.dead === true && Game.properties.duration % 10 === 0) {
            // If spriteXIndex is 5, the death animation is complete
            if (this.spriteXIndex == 5) {
                this.spriteXIndex = 3;
                GamePieces.daqloon[this.id].spawn = true;
                // GamePieces.player.attackedBy = getNearestDaqloon();
                let lootItem =
                    getRandomInteger(0, 30) === 30
                        ? "daqloon horns"
                        : "daqloon scale";
                // GamePieces.items.push(new item(this.drawX, this.drawY, lootItem));
                // setTimeout(() => {
                // checkDaqloon(1, this.id), 5000;
                // });
                return false;
            } else if (this.spriteXIndex < 3) {
                this.spriteXIndex = 3;
            }
            this.spriteYIndex = 2;
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
        }
        if (
            this.spriteXIndex > 4 &&
            this.attack == false &&
            this.dead == false &&
            this.spawn === false
        ) {
            this.spriteXIndex = 1;
        }
        this.drawOnCanvas();
    }
    calculateNewPosition() {
        collisionCheck(this);
        this.x += this.speedX;
        this.drawX += this.speedX;
        this.y += this.speedY;
        this.drawY += this.speedY;
    }
    calculateMovement() {
        let distanceX = GamePieces.player.xpos - this.x;
        let distanceY = GamePieces.player.ypos - this.y;
        // Check if there are nearby daqloons, if so prevent them from "crashing"
        // let nearbyIndex = this.findOtherDaqloons();
        let nearbyX = 1;
        let nearbyY = 1;
        let debug = true;
        // Variables to determine how much a daqloon should move if it is able to
        // let moveY = 1 * (Game.properties.delta * this.movementSpeed) * nearbyY;
        // let moveX = 1 * (Game.properties.delta * this.movementSpeed) * nearbyX;
        let moveY = this.movementSpeed;
        let moveX = this.movementSpeed;
        // game.calculateDistance();
        if (
            (Math.abs(distanceX) < 250 || Math.abs(distanceY) < 250) &&
            GamePieces.player.attackedBy === this.id
        ) {
            this.nearbyPlayer = true;
            this.oldX = this.x;
            if (
                distanceX > 3 &&
                this.x + 1 < this.fighting_area.diameterRight
            ) {
                /*this.x+= 2;
                this.drawX += 2;*/
                this.speedX;
                if (this.right !== "blocked") {
                    this.x +=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyX;
                    this.drawX +=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyX;
                }
            } else if (
                distanceX < -3 &&
                this.x - 1 > this.fighting_area.diameterLeft
            ) {
                /*this.x -= 2;
                this.drawX-= 2;*/
                if (this.left !== "blocked") {
                    this.x -=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyX;
                    this.drawX -=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyX;
                }
            }
            this.oldY = this.y;
            if (distanceY > 3 && this.y + 1 < this.fighting_area.diameterDown) {
                if (this.down !== "blocked") {
                    this.y +=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyY;
                    this.drawY +=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyY;
                }
            } else if (
                distanceY < -3 &&
                this.y - 1 > this.fighting_area.diameterUp
            ) {
                if (this.up !== "blocked") {
                    this.y -=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyY;
                    this.drawY -=
                        1 *
                        (Game.properties.delta * this.movementSpeed) *
                        nearbyY;
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
                        this.speedY = moveY;
                    } else {
                        this.speedY = 0;
                    }
                    break;
                case 1:
                    if (this.y > this.fighting_area.diameterUp) {
                        this.speedY = -moveY;
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
                        this.speedX = -moveX;
                    }
                    break;
                case 2:
                    if (this.x < this.fighting_area.diameterRight) {
                        this.speedX = moveX;
                    }
                    break;
            }
        }
        this.calculateNewPosition();
    }
    findOtherDaqloons() {
        const check = (object) => {
            return (
                Math.abs(this.drawX - object.drawX) < 5 &&
                Math.abs(this.drawY - object.drawY) < 5
            );
        };
        let nearby = GamePieces.daqloon.findIndex(check, this);
        let nearbyX = 1;
        let nearbyY = 1;
        if (nearby != -1) {
            let nearbyDaqloon = GamePieces.daqloon[nearby];
            if (Math.abs(nearbyDaqloon.drawX - this.drawX) < 5) {
                nearbyX = 0;
            }
            if (Math.abs(nearbyDaqloon.drawY - this.drawY) < 5) {
                nearbyY = 0;
            }
        }
        return [nearbyX, nearbyY];
    }
}
