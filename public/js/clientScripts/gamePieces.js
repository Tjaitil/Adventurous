
const gamePieces = {
    events: [],
    obstacles: [],
    links: [],
    items: [],
    objects: [],
    daqloon: [],
    buildings: [],
    characters: [],
    daqloon_fighting_area: [],
    player: {
        width: 32 * 1.05,
        height: 32 * 1.05,
        speedX: 0,
        speedY: 0,
        speed: 1.5,
        character: new Image(96, 128),
        characterAttack: new Image(114, 32),
        x: null,
        y: null,
        travel: false,
        attack: false,
        xMovement: 0,
        yMovement: 0,
        // xTracker and yTracker used for tracking x and y l
        xTracker: 0,
        yTracker: 0,
        up: "open",
        left: "open",
        down: "open",
        right: "open",
        playerSize: 32 * 1.05,
        diameterUp: this.y,
        diameterRight: this.x + this.width - 5,
        diameterDown: this.y + 28,
        diameterLeft: this.x + 5,
        // xpos and ypos is the position of the player in the world
        xpos: game.properties.xbase + this.xMovement,
        ypos: game.properties.ybase + this.yMovement,
        oldXbase: 0,
        oldYbase: 0,
        animationEnd: true,
        attackedBy: false,
        hunted: false,
        loopIndex: 0,
        counter: 0,
        direction: 'none',
        loopArray: [0, 1, 0, 2],
        indexX: 32,
        indexY: 0,
        index: 0,
        attackLoop: 0,
        lastAttack: 0,
        attackSpeed: 10,
        combat: false,
        attackDamage: 10,
        cooldown: 0,
        movementSpeed: 60,
        health: 100,
        regenerateCoundown: false,
        // imageFix to adjust character sprite not being in 32 x 32 format
        imageFix: 0,
        setup() {
            this.setHuntedStatus(false);
            this.character.src = "public/images/character1.png";
            this.characterAttack.src = "public/images/character attack2.png";
        },
        load(xbase, ybase, nearestDaqloon) {
            this.xMovement = 0;
            this.yMovement = 0;
            this.attackedBy = nearestDaqloon;
            this.x = xbase;
            this.y = ybase;
            this.diameterUp = this.y + 20;
            this.diameteRight = this.x + this.width;
            this.diameterDown = this.y + this.height;
            this.diameterLeft = this.x;
        },
        startCombat() {
        },
        takeDamage(damage) {
            if (isNaN(damage) || damage === 0) return false;
            // Draw sprite that takes damage
            viewport.drawPlayer(this.characterAttack, 41 * 2, 38 * 1, 32, 32, this.playerSize, this.playerSize);
            this.health -= damage;
            progressBar.calculateProgress(document.getElementById("health_progressBar"),
                (this.health > 0 ? this.health : 0), 100, false);
            // Player died
            if (this.health <= 0) {
                canvasTextHeader.setDraw("You died!");
                document.getElementById("health_progressBar").querySelectorAll(".progressBar_currentValue")[0].innerHTML = 100;
                this.health = 100;
                let new_x;
                let new_y;
                // Locate nearest town
                switch (game.properties.currentMap) {
                    case "6.7":
                        new_x = 5;
                        new_y = 7;
                        break;
                    case "8.3":
                        new_x = 8
                        new_y = 2;
                        break;
                    case "3.10":
                        new_x = 4
                        new_y = 9;
                        break;
                    default:
                        break;
                }
                setTimeout(() => game.loadWorld(false, false, "changeMap", { "new_x": newX, "new_y": newY }), 2000);
            }
        },
        draw() {
            if (this.combat === true) {
                viewport.drawPlayer(this.characterAttack, (41 * this.loopIndex) + this.imageFix, 38 * this.indexY,
                                    32, 32, this.playerSize, this.playerSize);
            }
            else {
                viewport.drawPlayer(this.character, this.indexX * this.loopIndex, this.indexY, 32, 32, 
                                    this.playerSize, this.playerSize)
            }
        },
        drawCooldown() {
            this.cooldown -= 3;
            game.properties.context2.fillStyle = "orange";
            game.properties.context2.fillRect(10, 60, 100 - (100 - this.cooldown), 10);
        },
        setHuntedStatus(status) {
            this.hunted = status;
            console.log(this.hunted);
            if(this.hunted === true) {
                document.getElementById("HUD_hunted_icon").style.visibility = "visible";
            } else {
                document.getElementById("HUD_hunted_icon").style.visibility = "hidden";
            }
        },
        regenerateHealth() {
            if(this.health <= 0) return;
            (this.health + 10 > 100) ? this.health = 100 : this.health += 10;
            progressBar.calculateProgress(document.getElementById("health_progressBar"),
                (this.health > 0 ? this.health : 0), 100, false);  
            this.regenerateCoundown = false;
        },
        newPos(newPos = true) {
            if(this.health < 100 && this.regenerateCoundown === false) {
                this.regenerateCoundown = true;
                setTimeout(() => this.regenerateHealth(), 7000);
            }
            this.up = "open";
            this.left = "open";
            this.down = "open";
            this.right = "open";
            //drawing starts at x (diameterLeft) and y (diameterUp) line
            if (newPos !== false) {
                gamePieces.player.xpos =
                    game.properties.xbase + gamePieces.player.xMovement;
                gamePieces.player.ypos =
                    game.properties.ybase + gamePieces.player.yMovement;
                game.properties.xMapMin = this.xpos - 320;
                game.properties.xMapMax = this.xpos + 320;
                game.properties.yMapMin = this.ypos - 320;
                game.properties.yMapMax = this.ypos + 320;
                this.diameterUp = this.ypos + 20;
                this.diameterRight = this.xpos + this.width - 4;
                this.diameterDown = this.ypos + this.height;
                this.diameterLeft = this.xpos + 4;
            }
            if (this.combat === true) {
                let newDirection = 'none';
                if (controls.playerDown === true) {
                    newDirection = 'down';
                    this.direction = 'down';
                    this.indexY = 0;
                    this.imageFix = 10;
                }
                else if (controls.playerUp === true) {
                    newDirection = 'up';
                    this.direction = 'up';
                    this.indexY = 2;
                    this.imageFix = 5;
                }
                // If direction direction is left or right then draw sprite heading down
                if ((newDirection === 'undefined' || newDirection == 'none')
                    && (controls.playerRight === true || controls.playerLeft === true)) {
                        if(this.direction === 'up') {
                            this.indexY = 2;
                            this.imageFix = 5;
                        }
                        else {
                            this.indexY = 0;
                            this.imageFix = 10;
                        }
                }
                if (this.attack === true && game.properties.duration % 2 === 0 && this.cooldown <= 0) {
                    this.indexY += 1;
                    if (this.attackLoop === 0) {
                        this.loopIndex = 0;
                        let number = 0;
                        let number2 = 0;
                        for (let i = 0; i < gamePieces.daqloon.length; i++) {
                            if(i === 0) {
                            }
                            if ((this.direction === 'up' && gamePieces.daqloon[i].y < this.ypos &&
                                  Math.abs(gamePieces.daqloon[i].x - this.xpos) < 30 &&
                                  Math.abs(gamePieces.daqloon[i].y - this.ypos) < 30) 
                                ||
                                (this.direction === 'down' &&
                                  Math.abs(gamePieces.daqloon[i].x - this.xpos) < 64 &&
                                  Math.abs(gamePieces.daqloon[i].y - this.ypos + 32) < 60)) {
                                if(this.direction === 'down') {
                                    number = 20;
                                    number2 = 5;
                                }
                                else {
                                    number = -20;
                                    number2 = -5;
                                }
                                gamePieces.daqloon[i].x += number;
                                gamePieces.daqloon[i].drawX += number;
                                gamePieces.daqloon[i].y += number;
                                gamePieces.daqloon[i].drawY += number;
                                gamePieces.daqloon[i].hit();
                            }
                        }
                    }
                    this.draw();
                    this.loopIndex++;
                    // Attack is finished on attackLoop 2
                    if (this.attackLoop === 1) {
                        this.attack = false;
                        this.cooldown = 100;
                        this.attackLoop = 0;
                    }
                    else {
                        this.attackLoop++;
                    }
                }
                else if (game.properties.duration % 10 === 0 && this.attack === false) {
                    if (this.loopIndex > 3) {
                        this.loopIndex = 0;
                    }
                    this.draw();
                    this.loopIndex++;
                }
            }
            else {
                var newdirection = 'none';
                if (controls.playerLeft == true && controls.playerDown == true) {
                    newdirection = 'left, down';
                }
                if (controls.playerRight == true && controls.playerUp == false && controls.playerDown == false) {
                    newdirection = 'right';
                    this.indexY = 32;
                }
                if (controls.playerLeft == true && controls.playerUp == false && controls.playerDown == false) {
                    newdirection = 'left';
                    this.indexY = 64;
                }
                if (controls.playerDown == true) {
                    newdirection = 'right, down';
                    this.indexY = 0;
                }
                if (controls.playerUp == true) {
                    newdirection = 'right, up';
                    this.indexY = 96;
                }
                if (controls.playerRight == true && controls.playerUp == false && controls.playerDown == false) {
                    newdirection = 'right';
                    this.indexY = 32;
                }
                if (controls.playerUp == false && controls.playerLeft == false && controls.playerRight == false
                    && controls.playerDown == false) {
                    newdirection = 'none';
                }
                if (newdirection != 'none' && ((this.oldYbase != this.ypos) || (this.oldXbase != this.xpos))) {
                    if (newdirection != 'none' &&  game.properties.duration % 10 === 0) {
                        this.draw();
                        this.loopIndex++;
                    }
                    else if (newdirection != this.direction) {
                        this.loopIndex = 1;
                        this.direction = newdirection;
                        this.draw();
                        this.loopIndex++;
                    }
                    if (this.loopIndex == 5 && this.newdirection != 'none') {
                        this.loopIndex = 1;
                    }
                    this.counter++;
                    this.animationEnd = false;
                }
                else {
                    this.loopIndex = 0;
                    this.draw();
                    this.animationEnd = true;
                }
            }
            if (this.cooldown > 0) {
                this.drawCooldown();
            }
            this.oldYbase = gamePieces.player.ypos;
            this.oldXbase = gamePieces.player.xpos;
        }
    },
    loadAssets(xbase, ybase, mapData) {
        this.loadStaticPieces();
        this.loadDaqloonFightingArea(mapData['daqloon_fighting_areas']);
        this.player.load(xbase, ybase, getNearestDaqloon());
    },
    loadDaqloonFightingArea(daqloonFightingAreas) {
        if (typeof (daqloonFightingAreas) !== "undefined") {
            this.daqloon_fighting_area = daqloonFightingAreas[0];
            checkDaqloon(gamePieces.daqloon_fighting_area.daqloon_amount);
        }
        else {
            this.daqloon_fighting_area = [];
            this.daqloon = [];
            document.getElementById("HUD_hunted_locater").innerHTML = "";
        }
    },
    loadStaticPieces() {
        for(var i = 0; i < this.objects.length; i++) {
            if (this.objects[i].src != undefined && this.objects[i].src.length > 1) {
                if (this.objects[i].type === 'character') {
                    this.objects[i].width = 38;
                    this.objects[i].height = 38;
                    this.objects[i].x -= 6;
                    this.objects[i].y -= 6;
                }
                this.objects[i].img = new Image();
                if (this.objects[i].src.indexOf('.png') == -1) this.objects[i].src += '.png';
                this.objects[i].img.src = "public/images/" + this.objects[i].src;


            }
            this.objects[i].width *= viewport.scale;
            this.objects[i].height *= viewport.scale;
            this.objects[i].drawX = Math.round(this.objects[i].x - viewport.offsetX);
            this.objects[i].drawY = Math.round(this.objects[i].y - viewport.offsetY);
            if (this.objects[i].type == "building") {
                this.buildings.push(this.objects[i]);
            }
            else if (this.objects[i].type == "character") {
                this.characters.push(this.objects[i]);
            }
        }
        this.objects.sort((a, b) => { return a.diameterDown - b.diameterDown; });
    },
    init() {
        this.drawStaticPieces();
    },
    drawStaticPieces() {
        // buildingMatch variable is to check if there is at building that the player can enter
        let buildingMatch = false;
        let personMatch = false;
        let person = null;
        game.properties.context4.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        for(let i = 0; i < gamePieces.visibleObjects.length; i++) {
            // console.log(gamePieces.visibleObjects[i]);
            if(gamePieces.visibleObjects[i].visible === true && gamePieces.visibleObjects[i].type !== "figure" &&  
                ["desert_dune", "nc_object"].indexOf(gamePieces.visibleObjects[i].type) === -1 && 
                gamePieces.visibleObjects[i].src.length > 1) {
                let drawContext;
                // If building is behind player, then draw on the first canvas instead of the third
                if(gamePieces.visibleObjects[i].diameterDown < gamePieces.player.diameterDown) {
                    drawContext = game.properties.context;
                }
                else {
                    drawContext = game.properties.context4;
                }
                if(gamePieces.visibleObjects[i].type === "character") {
                    // drawContext.imageSmoothingEnabled = false;
                    drawContext.drawImage(gamePieces.visibleObjects[i].img,
                        gamePieces.visibleObjects[i].drawX - (gamePieces.player.xMovement),
                        gamePieces.visibleObjects[i].drawY - (gamePieces.player.yMovement), 
                        gamePieces.visibleObjects[i].width, gamePieces.visibleObjects[i].height);
                    }
                else {
                    drawContext.drawImage(gamePieces.visibleObjects[i].img,
                        Math.round(gamePieces.visibleObjects[i].drawX - (gamePieces.player.xMovement)),
                        Math.round(gamePieces.visibleObjects[i].drawY - (gamePieces.player.yMovement)));
                }
                // Check if person is near any buildings
                if (gamePieces.player.ypos > gamePieces.visibleObjects[i].diameterUp
                    && gamePieces.player.ypos < gamePieces.visibleObjects[i].diameterDown &&
                    gamePieces.player.xpos > gamePieces.visibleObjects[i].diameterLeft 
                    && gamePieces.player.xpos < gamePieces.visibleObjects[i].diameterRight &&
                    Math.abs(gamePieces.player.ypos - gamePieces.visibleObjects[i].diameterDown) < 32 &&
                    gamePieces.visibleObjects[i].type === "building") {
                    buildingMatch = true;
                }
                else if (Math.abs(gamePieces.player.xpos - gamePieces.visibleObjects[i].x) < 32 &&
                        Math.abs(gamePieces.player.ypos - gamePieces.visibleObjects[i].y) < 32 &&
                    gamePieces.visibleObjects[i].conversation !== false && 
                    gamePieces.visibleObjects[i].type === "character") {
                    personMatch = true;
                    person = jsUcfirst(gamePieces.visibleObjects[i].src.split(".png")[0]);
                }
            }
        }
        if (buildingMatch === true) {
            document.getElementById("control_text_building").innerHTML = controls.enterText;
        }
        else {
            document.getElementById("control_text_building").innerHTML = controls.enterButton;
        }
        if (personMatch === true) {
            document.getElementById("control_text_conversation").innerHTML = controls.personText + " " + person;
        }
        else {
            document.getElementById("control_text_conversation").innerHTML = controls.personButton;
        }
        if(draw === true) {
            for(let i = 0; i < gamePieces.visibleObjects.length; i++) {
                if(draw == true) {
                    game.properties.context4.fillStyle = "red";
                    game.properties.context4.fillRect(gamePieces.visibleObjects[i].drawX - (gamePieces.player.xMovement / viewport.scale),
                        gamePieces.visibleObjects[i].drawY - (gamePieces.player.yMovement / viewport.scale),
                        gamePieces.visibleObjects[i].width, gamePieces.visibleObjects[i].height);
                    game.properties.context4.font = "10px Comic Sans MS";
                    game.properties.context4.fillStyle = "white";
                    game.properties.context4.fillText(i + ' | ' + gamePieces.visibleObjects[i].id,
                        gamePieces.visibleObjects[i].drawX - (gamePieces.player.xMovement / viewport.scale) +
                        (gamePieces.visibleObjects[i].width / 2),
                        gamePieces.visibleObjects[i].drawY + (gamePieces.visibleObjects[i].height / 2) -
                        (gamePieces.player.yMovement / viewport.scale));
                }
            }   
        }
    }
};
window.gamePieces = gamePieces;