
// Prevent user from scrolling with arrow keys on site
window.addEventListener("keydown", function(e) {
    // space and arrow keys
    if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
        e.preventDefault();
    }
}, false);


function fetch() {
    inBuilding = true;
    ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if(this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            openNews(this.responseText);
        }
    };
    ajaxRequest.open('GET', "handlers/handler_v.php");
    ajaxRequest.send();
}

function move() {
        console.log(event);
        var button = document.getElementById("control_button");
        var element = event.target.closest("#control");
        var elementPos = element.getBoundingClientRect();
        console.log(elementPos);
        console.log(element.scrollTop);
        var button_top = (event.clientY - elementPos.top - 12.5);
        var button_left = (event.clientX - elementPos.left - 13);
        if((event.clientY + 35) > elementPos.bottom || (event.clientY - 35) < elementPos.top ||
           (event.clientX + 35) > elementPos.right || (event.clientX - 35) < elementPos.left) {
            return false;
        }
        button.style.top = button_top + "px";
        button.style.left = button_left + "px";
    }
    function endMove() {
        var button = document.getElementById("control_button");
        button.style.top = "41%";
        button.style.left = "44%";
    }
    var inBuilding = false;
    var world = new Image(2000, 1000);
    world.src = "public/img/pixelat.png";
    console.log(world);
    var player_img = new Image(32, 32);
    player_img.src = "public/img/character test3.png";
    var tree_img = new Image(64, 64);
    tree_img.src = "public/img/tree_pix2.png";
    var smithy_img = new Image(128, 128);
    smithy_img.src = "public/img/smithy pix.png";
    var character = new Image(96, 128);
    character.src = "public/img/character sprite.png";
    var player;
    var obstaclesPos = [];
    var obstaclesSize = [];
    var linksPos = [];
    var linksSize = [];
    var xMovement = 0;
    var yMovement = 0;
    var xMovement2 = 0;
    var yMovement2 = 0;
    //
    var xbase = 320;
    var ybase = 150;
    // charX and charY where the character is drawn on canvas (middle);
    var charX = 320;
    var charY = 150;
    // MapMin/MapMax variables holds the coordinates of furtherst loaded chunks
    var xMapMin = xbase - 320;
    var xMapMax = xbase + 320;
    var yMapMin = ybase - 320;
    var yMapMax = ybase + 320;
    var xcamMove = 0;
    var ycamMove = 0;
    var lastCalledTime;
    var fps;
    var keys = [];
    var interval = false;
    // xpos and ypos is the position of the player in the world
    var xpos = xbase + xMovement;
    var ypos = ybase + yMovement;
    var animationEnd = true;
    // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
    // 1 is normal then the picture will be painted in 1024 width and height.
    var scale = 1;
    var render = 0;
    var gamePause = false;
    var loopIndex = 0;
    var counter = 0;
    var direction = 'none';
    var colors = ['#4d4dff', '#0000b3', '#000033'];
    var loopArray = [0, 1, 0, 2];
    var indexX = 32;
    var indexY = 0;
    var oldXbase;
    var oldYbase;
        
    function draw(mx, my, sx, sy) {
        var ctx = game.properties.context;
        ctx.moveTo(mx, my);
        ctx.lineTo(sx, sy);
        ctx.stroke();
    }
    
    game = {};
    document.addEventListener('DOMContentLoaded', function() {
        game.properties.canvasHeight = document.getElementById("game_canvas").height;
        game.properties.canvasWidth = document.getElementById("game_canvas").width;
        game.loadWorld();
        inactivityTime();
    });
    gamePieces = {
        obstacles : [],
        links: [],
        objects: [],
        player: new newPlayer(30, 30, "#0000A0", xbase, ybase)
    };
    game.loadWorld = function() {
        var data = "model=worldLoader" + "&method=JSONfiles";
        ajaxG(data, function(response) {
            var responseText = response[1];
            if(response[0] != false) {
                var obj = JSON.parse(responseText);
                gamePieces.links = obj['links'];
                gamePieces.objects = obj['objects'];
                console.log(gamePieces.objects);
                for(var i = 0; i < gamePieces.objects.length; i++) {
                    gamePieces.objects[i].img = new Image(gamePieces.objects[i].width, gamePieces.objects[i].height);
                    gamePieces.objects[i].img.src = "public/img/" + gamePieces.objects[i].src;
                }
                console.log(gamePieces.objects);
                /* gamePieces.objects = responseText['objects'];
                 * gamePieces.buildings = responseText['buildings'];
                /*for(var i = 0; i < obj.layers.length; i++) {
                    if(obj.layers[i].name.indexOf("Objekt") != -1) {
                        objLayers.push(obj.layers[i]);
                        for(var x = 0; x < obj.layers[i].objects.length; x++) {
                            obj.layers[i].objects[x].src = obj.layers[i].objects[x].type + ".png";
                            /*var keys = obj.layers[i][x].properties.keys();
                            if(keys.length > 1) {
                                var objectProperties = obj.layers[i].objects[x].properties;
                                for(var y = 0; y < keys.length; y++) {
                                    obj.layers[i].objects[x][y] = obj.layers[i].objects[x][objectProperties + [y]];
                                }    
                            }
                            gamePieces.objects.push(obj.layers[i].objects[x]);
                        }
                    }
                }*/
            }
            else {
                console.log(JSON.parse(responseText));
                // Load game picture
                world.src = false;
                //responseText[1];
                // Load obstacles and pieces
                
            }
            game.startGame();
        });
    };
    game.properties = {
        context: document.getElementById("game_canvas").getContext("2d"),
        canvasWidth: document.getElementById("game_canvas").width,
        canvasHeight: document.getElementById("game_canvas").height,
        requestId: null
    };
    game.controls = {
        left: false,
        up: false,
        right: false,
        down: false
    };
    console.log(document.getElementById("game_canvas").width);
    game.loadChunk = function(x, y, xpos, ypos) {
        /*var data = "model=worldLoader" + "&method=loadChunks" + "&xMapMin=" + xMapMin + "&yMapMin=" + yMapMin + "&xbase=" + xpos +  "&ybase=" + ypos;
        ajaxG(data, function(response) {
            console.log(response[1]);
            var responseText = JSON.parse(response[1]);
            gamePieces.obstacles = gamePieces.obstacles.concat(responseText);
        });*/
    };
    game.deleteChunk = function () {
        for(var i = 0; i < objects.length; i++) {
            var index = gamePieces.obstacles.indexOf(objects[i]['id']);
            if(index != -1) {
                gamePieces.obstacles.splice(index, 1);
            }
        }
    };
    /*var lastLoop = new Date();
    game.fps = function() { 
        var thisLoop = new Date();
        var fps = 1000 / (thisLoop - lastLoop);
        lastLoop = thisLoop;
        document.getElementById("frames").innerHTML = fps;
    };*/
    
    
    /*if() {
        
    }*/
    game.startGame = function () {
        game.properties.context.drawImage(world, 0 + xMovement, 0 + yMovement, 1024 * scale, 1024 * scale, 0, 0, 1024, 1024);
        game.loadGamePieces();
        gamePieces.player.first();
        player = gamePieces.player;
        game.properties.requestId = window.requestAnimationFrame(game.update);
        if(world.complete) {
            console.log("loaded");
            game.updateGamePiece();
        }
    };
    game.loadGamePieces = function() {
        /*var list = [];
        list.push(["obstc1", 240, 0, 20, 20, "red"]);
        list.push(["obstc1", 0, 192, 64, 512, "none"]);
        /*list.push(["obstc1", 0, 256, 64, 64, "yellow"]);
        list.push(["obstc1", 0, 320, 64, 64, "yellow"]);
        list.push(["obstc1", 0, 384, 64, 64, "none"]);
        list.push(["obstc1", 0, 448, 64, 64, "yellow"]);*/
        /*list.push(["obstc1", 500, 448, 3, 64, "orange"]);
        list.push(["obstc1", 192, 320, 64, 32, "red"]);
        list.push(["obstc1", 288, 288, 32, 320, "purple"]);
        list.push(["obstc1", 640, 640, 64, 64, "pink"]);
        list.push(["obstc1", 1024, 294, 112, 90, "blue"]);
        list.push(["obstc1", 1104, 384, 48, 32, "red"]);
        list.push(["obstc1", 1024, 384, 32, 32, "orange"]);
        
        var links = [];
        links.push(["link2", 624, 240, 32, 48, "orange", "towhar"]);
        links.push(["link3", 1065, 368, 32, 48, "pink", "towhar"]);
        
        
        // push game obstacles objects into array
        for(var i = 0; i < list.length; i++) {
            gamePieces.obstacles[i] = new gameObstacle(list[i][0], list[i][1], list[i][2], list[i][3],
                                                                          list[i][4], list[i][5]);
        }
        // push game links objects into array
        for(var x = 0; x < links.length; x++) {
            gamePieces.links[x] = new gameLinks(links[x][0], links[x][1], links[x][2], links[x][3],
                                                                          links[x][4], links[x][5], links[x][6]);
        }*/
    };
    var inactivityTime = function () {
        var time;
        document.onkeydown = resetTimer;

        function pauseGame() {
            if(game.controls.down == false && game.controls.left == false &&
               game.controls.right == false && game.controls.up == false) {
                return false;
            }
            ctx = game.properties.context;
            ctx.font = "30px Comic Sans MS";
            ctx.fillStyle = "pink";
            ctx.textAlign = "center";
            ctx.fillText("Game Paused", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
            ctx.font = "20px Comic Sans MS";
            ctx.fillText("Press any key to continue", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2 + 35);
            window.cancelAnimationFrame(game.properties.requestId);
            gamePause = true;
        }
        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(pauseGame, 10000);
            // 1000 milliseconds = 1 second
        }
    };
    console.log(document.getElementById("game_canvas"));
    
    function gameText(text) {
        let canvas = document.getElementById("game_canvas");
        let game_text = document.getElementById("game_text");
        game_text.style.top = canvas.offsetTop + 125 + "px";
        game_text.style.visibility = "visible";
        console.log(game_text.style.top);
        game_text.innerHTML = text;
        let opa = 0.2;
        const x = setInterval(opacity, 100);
        function opacity () {
            console.log(x);
            opa = game_text.style.opacity = opa + 0.1;
            if(opa > 1) {
                clearInterval(x);
            }
        }
        setTimeout(hideText, 3000);
    }
    function hideText() {
        let game_text = document.getElementById("game_text");
        game_text.innerHTML = "";
        game_text.style.visibility = "hidden";
    }
    function resumeGame() {
        inactivityTime();
        gamePause = false;
        game.properties.requestId = window.requestAnimationFrame(game.update);
    }
    function gameObstacle (name, x, y, width, height, style) {
        this.name = name;
        this.x = x;
        this.y = y;
        this.width = width / scale;
        this.height = height / scale;
        this.diameterTop = y;
        this.diameterRight = x + width;
        this.diameterDown = y + height;
        this.diameterLeft = x;
        this.style = style;
        ctx = game.properties.context;
        ctx.fillStyle = style;
        ctx.fillRect(x, y, width, height);
        obstaclesPos.push([x, y, this.diameterTop, this.diameterRight, this.diameterDown, this.diameterLeft, width, height]);
    }
    function gameLinks(name, x, y, width, height, style, location) {
        this.name = name;
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
        this.diameterTop = y;
        this.diameterRight = x + width;
        this.diameterDown = y + height;
        this.diameterLeft = x;
        this.style = style;
        this.img = new Image(50, 50);
        this.img.src = style + ".png";
        this.location = location;
        ctx = game.properties.context;
        ctx.fillStyle = style;
        ctx.fillRect(x, y, width, height);
        linksPos.push([x, y, this.diameterTop, this.diameterRight, this.diameterDown, this.diameterLeft, width, height]);
    }
    function newPlayer(width, height, color, x, y) {
        this.width = width;
        this.height = height;
        this.speedX = 0;
        this.speedY = 0;    
        this.x = x;
        this.y = y;
        this.top = "open";
        this.left = "open";
        this.down = "open";
        this.right = "open";
        this.diameterTop = y;
        this.diameteRight = x + width;
        this.diameterDown = y + height;
        this.diameterLeft = x;
        this.first = function() {
            ctx = game.properties.context;
            ctx.drawImage(player_img, charX, charY);
            /*ctx.fillStyle = "#0000A0";
            ctx.fillRect(xbase, ybase, this.width, this.height);*/
        };
        this.newPos = function() {
            this.top = "open";
            this.left = "open";
            this.down = "open";
            this.right = "open";
            //drawing starts at x (diameterLeft) and y (diameterTop) line
            player.diameterTop = ybase + (yMovement / scale);
            player.diameterRight = xbase + (xMovement / scale) + width;
            player.diameterDown = ybase + (yMovement / scale) + height;
            player.diameterLeft = xbase + (xMovement / scale);
            
            
            ctx = game.properties.context;
            /*ctx.drawImage(player_img, charX, charY);*/
        };
    }
    window.addEventListener('keydown', function (e) {
        if(gamePause == true) {
            resumeGame();
        }
        switch(e.keyCode) {
            case 37:
                game.controls.left = true;
                break;
            case 38:
                game.controls.up = true;
                break;
            case 39:
                game.controls.right = true;
                break;
            case 40:
                game.controls.down = true;
                break;
        }
        
    }, false);
    window.addEventListener('keyup', function (e) {
        switch(e.keyCode) {
            case 37:
                game.controls.left = false;
                break;
            case 38:
                game.controls.up = false;
                break;
            case 39:
                game.controls.right = false;
                break;
            case 40:
                game.controls.down = false;
                break;
        }
    }, false);
    game.updateGamePiece = function() {
            /*console.log("objects :" + gamePieces.objects.length);*/
            /*var visibleObjects = gamePieces.objects.filter(function(object) {
                return (Math.abs(ybase + object.y) < 360) && (Math.abs(object.x - xpos) > 100);
            });*/
            
            /* obstaclesPos indexes
                0 = default x position
                1 = default y position
                2 = diameter top
                3 = diameter right
                4 = diameter bottom
                5 = diameter left
                6 = width of obstacle
                7 = height of obstacle
            */
            
            
            // IMPORTANT!
            // Using fillRect, canvas will render element from top down. Meaning y = 0 is top of y
            // Using drawImage, canvas will render element from bottom down.
            
            ctx.fillStyle = "red";
            ctx.fillRect(1440 - (xMovement / scale), 608- (yMovement / scale),
                             128, 128);
            ctx.fillStyle = "blue";
            ctx.fillRect(802 - (xMovement / scale), 273 - (yMovement / scale),
                             32, 24);
            for(var i = 0; i < gamePieces.objects.length; i++) {
                ctx = game.properties.context;
                if(typeof gamePieces.objects[i].src === 'undefined') {
                    ctx.fillStyle = "red";
                    ctx.fillRect(gamePieces.objects[i].x - (xMovement / scale), gamePieces.objects[i].y - (yMovement / scale),
                             gamePieces.objects[i].width, gamePieces.objects[i].height);    
                }
                else {
                    ctx.drawImage(gamePieces.objects[i].img, gamePieces.objects[i].x - (xMovement / scale),
                                  gamePieces.objects[i].y - 128 - (yMovement / scale));
                }
                
            }
            /*for(var x = 0; x < linksPos.length; x++) {
                //Change xpos and ypos for links
                ctx = game.properties.context;
                /*ctx.drawImage(gamePieces.links[x].img, linksPos[x][0] - xMovement, linksPos[x][1] - yMovement, 400, 400);*/
                /*ctx.translate(linksPos[x][0] - xMovement, linksPos[x][1] - yMovement);*/
                /*ctx.fillStyle = "none";
                ctx.fillRect(linksPos[x][0] - xMovement, linksPos[x][1] - yMovement, linksPos[x][6], linksPos[x][7]);
            }*/
    };
    game.viewport = {
        counter: 0,
        draw: function() {
            ctx = game.properties.context;
            ctx.clearRect(-xMovement, -yMovement, 500, 300);
            ctx.save();
            //Draw world and translate the image according to players movement
            ctx.drawImage(world, 0 + xMovement, 0 + yMovement, 1024 * scale, 1024 * scale, 0, 0, 1024, 1024);
            /*ctx.translate(xbase - xMovement, ybase - yMovement);*/
            ctx.restore();
            xpos = xbase + (xMovement / scale);
            ypos = ybase + (yMovement / scale);
            /*ctx.drawImage(player_img, charX, charY);*/
        },
    };
    game.calculateDistance = function() {
        if(inBuilding != true) {
            for(i = 0; i < linksPos.length; i++) {
                if(player.diameterTop >= linksPos[i][2] &&
                   player.diameterRight <= linksPos[i][3] &&
                   player.diameterDown <= linksPos[i][4] &&
                   player.diameterLeft >= linksPos[i][5]) {
                        if(inBuilding == false) {
                            fetch();    
                        }
                        return;
                    }
            }
        }
        /* obstaclesPos indexes
                0 = default x position
                1 = default y position
                2 = diameter top
                3 = diameter right
                4 = diameter bottom
                5 = diameter left
                6 = width of obstacle
                7 = height of obstacle
            */
        // Collision detection, if user is less than 3px from object prevent movement
        for(i = 0; i < gamePieces.objects.length; i++) {
           if(Math.abs(player.diameterDown - gamePieces.objects[i].diameterTop) <= 1 &&
              player.diameterRight >= gamePieces.objects[i].diameterLeft && 
              player.diameterLeft <= gamePieces.objects[i].diameterRight) {
                player.down = "blocked";
           }
           if(Math.abs(player.diameterRight - gamePieces.objects[i].diameterLeft) <= 1 &&
              player.diameterTop <= gamePieces.objects[i].diameterDown &&
              player.diameterDown >= gamePieces.objects[i].diameterTop) {
                player.right = "blocked";
           }
           if(Math.abs(player.diameterTop - gamePieces.objects[i].diameterDown) <= 1 &&
              player.diameterRight >= gamePieces.objects[i].diameterLeft &&
              player.diameterLeft <= gamePieces.objects[i].diameterRight) {
                player.top = "blocked";
           }
           if(Math.abs(player.diameterLeft - gamePieces.objects[i].diameterRight) <= 1 &&
              player.diameterTop <= gamePieces.objects[i].diameterDown &&
              player.diameterDown >= gamePieces.objects[i].diameterTop) {
                player.left = "blocked";
           }
        }
            if(game.controls.left && player.left == "blocked") {
                player.speedX = 0;
            }
            if(game.controls.right && player.right == "blocked") {
                player.speedX = 0;
            }
            if(game.controls.down && player.down == "blocked") {
                player.speedY = 0;
            }
            if(game.controls.up && player.top == "blocked") {
                player.speedY = 0;
            }
            
            xMovement += player.speedX;
            yMovement += player.speedY;
    };
    game.update = function () {
        player.speedX = 0;
        player.speedY = 0;
        if(game.controls.left == true) {
            player.speedX = -2;
        }
        if(game.controls.right == true) {
            player.speedX = 2;
        }
        if(game.controls.up == true) {
            player.speedY = -2;
        }
        if(game.controls.down == true) {
            player.speedY = 2;
        }
        if(player.speedX != 0 || player.speedY != 0 && inBuilding == false) {
            game.calculateDistance();
            game.viewport.draw();
            player.newPos();
            game.updateGamePiece(xbase, ybase);
            characterAnimation();      
        }
        else if(animationEnd != true) {
            characterAnimation();
        }
        game.properties.requestId = window.requestAnimationFrame(game.update);
    };

    function trst12() {
        // Scale ?
        var xDifference = Math.abs((xbase + xMovement - 320) - xMapMin);
        var yDifference = Math.abs((ybase + yMovement - 320) - yMapMin); 
        if(xDifference > 160 || yDifference > 160) {
            console.log(xMapMin);
            console.log(yMapMin);
            game.loadChunk(xMapMin, yMapMin, xbase + xMovement, ybase + yMovement);
            if(xDifference > 160) {
                xMapMin = xbase + xMovement - 320;    
            }
            else {
                yMapMin = ybase + yMovement - 320;    
            }
            console.log(xMapMin);
            console.log(yMapMin);
        }
    }
    
    function characterAnimation() {
        let div = document.getElementById("demo");
        var newdirection = 'none';
        if(game.controls.left == true && game.controls.down == true) {
            div.innerHTML = newdirection = 'left, down';
        }
        /*if(game.controls.left == true && game.controls.up == true) {
            div.innerHTML = newdirection = 'left, up';
            indexY = 96;
        }*/
        if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'right';
            indexY = 32;
        }
        if(game.controls.left == true && game.controls.up == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'left';
            indexY = 64;
        }
        if(game.controls.right == true && game.controls.down == true) {
            div.innerHTML = newdirection = 'right, down';
            indexY = 0;
        }
        if(game.controls.right == true && game.controls.up == true) {
            div.innerHTML = newdirection = 'right, top';
            indexY = 96;
        }
        if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'right';
            indexY = 32;
        }
        if(game.controls.down == true) {
            indexY = 0;
            div.innerHTML = newdirection = 'down';
        }
        if(game.controls.up == true) {
            indexY = 96;
            div.innerHTML = newdirection = 'top';
        }
        
        if(game.controls.up == false && game.controls.left == false && game.controls.right == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'none';
        }
        ctx2 = document.getElementById("game_canvas2").getContext("2d");
        if(newdirection != 'none' && ((oldYbase != ypos) || (oldXbase != xpos))) {
            if(newdirection != 'none' && counter % 15 == 0) {
                ctx2.clearRect(0, 0, 700, 700);
                console.log(loopIndex);
                div.innerHTML += loopIndex;
                document.getElementById("demo2").style.backgroundColor = colors[loopIndex];
                ctx2.drawImage(character, indexX * loopArray[loopIndex], indexY, 32, 32, 320, 150, 32, 32);
                loopIndex++;
            }
            else if(newdirection != direction) {
                loopIndex = 0;
                direction = newdirection;
                ctx2.clearRect(0, 0, 700, 700);
                div.innerHTML += loopIndex;
                document.getElementById("demo2").style.backgroundColor = colors[loopIndex];
                ctx2.drawImage(character, indexX * loopIndex, indexY, 32, 32, 320, 150, 32, 32);
                console.log(indexX * loopIndex);
                console.log("indexY" + indexY);
                loopIndex++;
            }
            if(loopIndex == 4 && newdirection != 'none') {
                loopIndex = 0;
            }
            counter++;
            animationEnd = false;
        }
        else {
            document.getElementById("demo2").style.backgroundColor = "#FFFFFF";
            ctx2.clearRect(0, 0, 700, 700);
            ctx2.drawImage(character, 0, indexY, 32, 32, 320, 150, 32, 32);
            animationEnd = true;
        }
        oldYbase = ypos;
        oldXbase = xpos;
        div.appendChild(character);
     }
    // Render player at specific locations
    function renderPlayer(x, y) {
        player = gamePieces.player;
        player.speedX = x;
        player.speedY = y;
        if(game.controls.left == true) { player.speedX = -2; }
        if(game.controls.right == true) { player.speedX = 2; }
        if(game.controls.up == true) { player.speedY = -2; }
        if(game.controls.down == true) { player.speedY = 2; }
        if(player.speedX != 0 || player.speedY != 0) {
            game.calculateDistance();
            game.viewport.draw();
            player.newPos();
            game.updateGamePiece(xbase, ybase);
        }
    }