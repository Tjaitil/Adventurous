
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
    world.src = "public/img/pixela 3.png";
    console.log(world);
    var player_img = new Image(32, 32);
    player_img.src = "public/img/character test3.png";
    var tree_img = new Image(64, 64);
    tree_img.src = "public/img/tree_pix2.png";
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
    var ybase = 320;
    // charX and charY where the character is drawn on canvas (middle);
    var charX = 320;
    var charY = 320;
    var xcamMove = 0;
    var ycamMove = 0;
    var lastCalledTime;
    var fps;
    var keys = [];
    var interval = false;
    var xpos = xbase;
    var ypos = ybase;
    // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
    // 1 is normal then the picture will be painted in 1024 width and height.
    var scale = 1;
    var render = 0;
    
        
    function draw(mx, my, sx, sy) {
        var ctx = game.properties.context;
        ctx.moveTo(mx, my);
        ctx.lineTo(sx, sy);
        ctx.stroke();
    }
    
    game = {};
    document.addEventListener('DOMContentLoaded', function() {
        game.loadWorld();
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
                var objLayers = [];
                for(var i = 0; i < obj.layers.length; i++) {
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
                            }*/
                            gamePieces.objects.push(obj.layers[i].objects[x]);
                        }
                    }
                }
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
    };
    game.controls = {
        left: false,
        up: false,
        right: false,
        down: false
    };
    game.startGame = function () {
        game.properties.context.drawImage(world, 0 + xMovement, 0 + yMovement, 1024 * scale, 1024 * scale, 0, 0, 1024, 1024);
        game.loadGamePieces();
        gamePieces.player.first();
        window.requestAnimationFrame(game.update);
        if(world.complete) {
            console.log("loaded");
        }
        console.log(player_img);
    };
    game.loadGamePieces = function() {
        var list = [];
        list.push(["obstc1", 240, 0, 20, 20, "red"]);
        list.push(["obstc1", 0, 192, 64, 512, "none"]);
        /*list.push(["obstc1", 0, 256, 64, 64, "yellow"]);
        list.push(["obstc1", 0, 320, 64, 64, "yellow"]);
        list.push(["obstc1", 0, 384, 64, 64, "none"]);
        list.push(["obstc1", 0, 448, 64, 64, "yellow"]);*/
        list.push(["obstc1", 500, 448, 3, 64, "orange"]);
        list.push(["obstc1", 192, 320, 64, 32, "red"]);
        list.push(["obstc1", 288, 288, 32, 320, "purple"]);
        list.push(["obstc1", 640, 640, 64, 64, "pink"]);
        
        console.log(list);
        var links = [];
        links.push(["link2", 624, 240, 32, 48, "orange", "towhar"]);
        
        
        // push game obstacles objects into array
        for(var i = 0; i < list.length; i++) {
            gamePieces.obstacles[i] = new gameObstacle(list[i][0], list[i][1], list[i][2], list[i][3],
                                                                          list[i][4], list[i][5]);
        }
        // push game links objects into array
        for(var x = 0; x < links.length; x++) {
            gamePieces.links[x] = new gameLinks(links[x][0], links[x][1], links[x][2], links[x][3],
                                                                          links[x][4], links[x][5], links[x][6]);
        }
        console.log(gamePieces.links);
    }; 
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
    game.updateGamePiece = function() {
            /*console.log("objects :" + gamePieces.objects.length);*/
            var visibleObjects = gamePieces.objects.filter(function(object) {
                return (Math.abs(object.y - ypos) > 700) && (Math.abs(object.x - xpos) > 100);
            });
            
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
            for(var i = 0; i < obstaclesPos.length; i++) {
                ctx = game.properties.context;
                if(gamePieces.obstacles[i].style != 'none') {
                    ctx.fillStyle = gamePieces.obstacles[i].style;
                    /*ctx.fillRect(obstaclesPos[i][0] - xMovement, obstaclesPos[i][1] - yMovement,
                             obstaclesSize[i][0], obstaclesSize[i][1]);*/
                    ctx.fillRect(obstaclesPos[i][0] - (xMovement / scale), obstaclesPos[i][1] - (yMovement / scale),
                             obstaclesPos[i][6], obstaclesPos[i][7]);    
                }
            }
            ctx = game.properties.context;
            /*console.log("tree x: " + (192 - xMovement / scale));
            console.log("tree y: " + (288 - yMovement / scale));*/
            ctx.drawImage(tree_img, 192 - xMovement, 288 - yMovement);
            for(var x = 0; x < linksPos.length; x++) {
                //Change xpos and ypos for links
                ctx = game.properties.context;
                /*ctx.drawImage(gamePieces.links[x].img, linksPos[x][0] - xMovement, linksPos[x][1] - yMovement, 400, 400);*/
                /*ctx.translate(linksPos[x][0] - xMovement, linksPos[x][1] - yMovement);*/
                ctx.fillStyle = gamePieces.links[x].style;
                ctx.fillRect(linksPos[x][0] - xMovement, linksPos[x][1] - yMovement, linksPos[x][6], linksPos[x][7]);
            }
    };
    game.viewport = {
        counter: 0,
        draw: function() {
            ctx = game.properties.context;
            ctx.clearRect(-xMovement, -yMovement, 500, 300);
            ctx.save();
            
            //Draw world and translate the image according to players movement
            ctx.drawImage(world, 0 + xMovement, 0 + yMovement, 1024 * scale, 1024 * scale, 0, 0, 1024, 1024);
            ctx.translate(xbase - xMovement, ybase - yMovement);
            ctx.restore();
            xpos = xbase + (xMovement / scale);
            ypos = ybase + (yMovement / scale);
            /*ctx.fillStyle = gamePieces.obstacles[0].style;
            ctx.fillRect(obstaclesPos[0][0] - xMovement, obstaclesPos[0][1] - yMovement,
                             obstaclesSize[0][0], obstaclesSize[0][1]);*/
            /*console.log(xpos + " : " + ypos);*/
            ctx.drawImage(player_img, charX, charY);
            /*game.properties.context.fillStyle = "#0000A0";
            game.properties.context.fillRect(xbase, ybase, player.width, player.height);*/
        },
    };
    
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
            ctx.drawImage(player_img, charX, charY);
        };
    }
    
    game.calculateDistance = function() {
        if(inBuilding != true) {
            for(i = 0; i < linksPos.length; i++) {
                if(player.diameterTop >= linksPos[i][2] &&
                   player.diameterRight <= linksPos[i][3] &&
                   player.diameterDown <= linksPos[i][4] &&
                   player.diameterLeft >= linksPos[i][5]){
                        if(inBuilding == false) {
                            fetch();    
                        }
                        return;
                   }
            }
        }
        
        // Collision detection, if user is less than 3px from object prevent movement
        for(i = 0; i < obstaclesPos.length; i++) {
           if(Math.abs(player.diameterDown - obstaclesPos[i][2]) <= 1 &&
              player.diameterRight >= obstaclesPos[i][5] && 
              player.diameterLeft <= obstaclesPos[i][3]) {
                player.down = "blocked";
                /*console.log("Down blocked");*/
           }
           if(Math.abs(player.diameterRight - obstaclesPos[i][5]) <= 1 &&
              player.diameterTop <= obstaclesPos[i][4] &&
              player.diameterDown >= obstaclesPos[i][2]) {
                player.right = "blocked";
                /*console.log("right blocked");*/
           }
           if(Math.abs(player.diameterTop - obstaclesPos[i][4]) <= 1 &&
              player.diameterRight >= obstaclesPos[i][5] &&
              player.diameterLeft <= obstaclesPos[i][3]) {
                player.top = "blocked";
                /*console.log("top blocked");*/
           }
           if(Math.abs(player.diameterLeft - obstaclesPos[i][3]) <= 1 &&
              player.diameterTop <= obstaclesPos[i][4] &&
              player.diameterDown >= obstaclesPos[i][2]) {
                player.left = "blocked";
                /*console.log("left blocked");*/
           }
        }
            if(game.controls.left && player.left == "blocked") {
                player.speedX = 0;
            }
            if (game.controls.right && player.right == "blocked") {
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
        player = gamePieces.player;
        player.speedX = 0;
        player.speedY = 0;
        if(render != false) {
            player.speedX = 32;
            player.speedY = 32;
        }
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
        }
        characterAnimation();
        window.requestAnimationFrame(game.update);
    };
    
    var loopIndex = 0;
    var direction = 'none';
    var colors = ['#4d4dff', '#0000b3', '#000033'];
    function characterAnimation() {
        console.log('characterAnimation');
        var div = document.getElementById("demo");
        var newDirection = 'none';
        if(game.controls.left == true && game.controls.down == true) {
            div.innerHTML = newdirection = 'left, down';
        }
        if(game.controls.left == true && game.controls.up == true) {
            div.innerHTML = newdirection = 'left, up';
        }
        if(game.controls.left == true && game.controls.up == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'left';
        }
        if(game.controls.right == true && game.controls.down == true) {
            div.innerHTML = newdirection = 'right, down';
        }
        if(game.controls.right == true && game.controls.up == true) {
            div.innerHTML = newdirection = 'right, top';
        }
        if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'right';
        }
        if(game.controls.down == true && game.controls.left == false && game.controls.right == false) {
            div.innerHTML = newdirection = 'down';
        }
        if(game.controls.up == true && game.controls.left == false && game.controls.right == false) {
            div.innerHTML = newdirection = 'top';
        }
        if(game.controls.up == false && game.controls.left == false && game.controls.right == false && game.controls.down == false) {
            div.innerHTML = newdirection = 'none';
        }
        
        if(direction != newDirection) {
            loopIndex = 0;
            div.innerHTML += '</br>' + loopIndex;
            loopIndex++;
        }
        else if(newdirection != 'none') {
            div.innerHTML += '</br>' + loopIndex;
            loopIndex++;
        }
        if(loopIndex == 9 && newdirection != 'none') {
            loopIndex = 0;
        }
        if(newdirection == 'none') {
            document.getElementById("demo2").style.backgroundColor = "#FFFFFF";
        }
        else {
            document.getElementById("demo2").style.backgroundColor = colors[loopIndex];    
        }
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
/*    var cars = [];
    
    var car = {
		0: {
    name: 'wild',
    x: 100,
    y: 300
    },
    1: {
    name: 'wild1',
    x: 2000,
    y: 2000
    },
    2: {
    name: 'wild2',
    x: 100,
    y: 300
    },
    3: {
    name: 'wild3',
    x: 400,
    y: 400
    },
    4: {
    name: 'wild4',
    x: 1000,
    y: 1000
    }   
};
    for(var i = 0; i < car.length; i++) {
        cars.push(car[i]);
    }
    console.log(cars);
    console.log("objects: " + cars.length);
    var visibleObjects = cars.filter(function(object) {
        console.log(object);
        console.log(object.y);
        return object.y > 400;
    });
    
    console.log("Objects rendered: " + visibleObjects.length);
    console.log(visibleObjects);*/