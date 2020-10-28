
// Prevent user from scrolling with arrow keys on site
window.addEventListener("keydown", function(e) {
    // space and arrow keys
    if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
        e.preventDefault();
    }
}, false);
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
            case 88:
                game.getNextMap();
                break;
            case 69:
                game.checkBuilding();
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
    /*if(game.properties.device !== "pc") {
        window.addEventListener('click', function() {
            console.log('hello');    
        });
    }*/
document.getElementById("game_canvas3").addEventListener("click",
                                                         function() {
                                                            let element = document.getElementById("game_canvas3");
                                                            let ElementPos = element.getBoundingClientRect();
                                                            let mouseY = event.clientY - ElementPos.top;
                                                            let mouseX = event.clientX - ElementPos.left;
                                                            let x = mouseX + (player.xpos - (game.properties.canvasWidth / 2) + 32);
                                                            let y = mouseY + (player.ypos - (game.properties.canvasHeight / 2));
                                                            /*console.log('xLeft:' + x);
                                                            console.log('yTop:' + y);
                                                            console.log('mouseX: ' + mouseX);
                                                            console.log('mouseY: ' + mouseY);*/
                                                            let object = gamePieces.objects[356];
                                                            /*console.log(object.drawY);
                                                            console.log(object.drawX);*/
                                                            if (y > object.diameterTop && y < object.diameterDown &&
                                                                x > object.diameterLeft && x < object.diameterRight &&
                                                                Math.abs(player.ypos - object.diameterDown) < 32)
                                                            {
                                                                game.fetchBuilding(object.src.split(".png")[0]);
                                                            }

                                                         });

document.getElementById("control").addEventListener("touchmove", move);
document.getElementById("control").addEventListener("touchend", endMove);
var click = 0;
function doubleClickDetect() {
    click++;
    if(click == 2) {
        click = 0;
        console.log('doubleclick');
    }
    else {
        setTimeout(function() {
            click = 0;  
        }, 1000);
    }
}
/*window.addEventListener("scroll", function() {
    let element = document.getElementById("hunger");
    let hungerPos = element.getBoundingClientRect();
    console.log(hungerPos.top);
    if(hungerPos.top < 0) {
        console.log(window.screenY);
        element
    }
});*/
    var click = false;   
    function move() {
        let button = document.getElementById("control_button");
        let element = event.target.closest("#control");
        let elementPos = element.getBoundingClientRect();
        let buttonPos = button.getBoundingClientRect();
        let eventY = event.targetTouches[0].clientY - elementPos.y;
        let eventX = event.targetTouches[0].clientX - elementPos.x; 
        // eventYTrigger/eventXTrigger is the minimum width the button gets moved before movement happens;
        let eventYTrigger = 50;
        let eventXTrigger = 50;
        // a is the distance from eventY to diameter
        // b is the distance from eventX to diaemter
        let a = Math.abs(eventY - (elementPos.height / 2));
        let b = Math.abs(eventX - (elementPos.width / 2));
        button.style.top = (event.targetTouches[0].clientY - elementPos.y - 25) + "px";
        button.style.left = (event.targetTouches[0].clientX - 50) + "px";
        
        if(a < 5 && b < 5) {
            game.controls.left = false;
            game.controls.up = false;
            game.controls.right = false;
            game.controls.down = false;
            return false;    
        }
        if(a > 110 || b > 110) {
            game.controls.left = false;
            game.controls.up = false;
            game.controls.right = false;
            game.controls.down = false;
            endMove();
            return false;
        }
        /*console.log(elementPos);
        console.log("eventY: " + eventY);
        console.log("a:" + a);
        console.log("b:" + b);*/
        
        let angle;
        if(eventX > eventXTrigger && eventY < eventYTrigger) {
            angle = (Math.atan(a/b) / (2 * 3.14)) * 360;
        }
        if(eventX < eventXTrigger && eventY < eventYTrigger) {
            angle = (Math.atan(b/a) / (2 * 3.14)) * 360 + 90;
        }
        if(eventX < eventXTrigger && eventY > eventYTrigger) {
            angle = (Math.atan(a/b) / (2 * 3.14)) * 360 + 180;
        }
        if(eventX > eventXTrigger && eventY > eventYTrigger) {
            angle = (Math.atan(b/a) / (2 * 3.14)) * 360 + 270;
        }
        if(337.5 < angle || angle < 22.5) {
            game.controls.left = false;
            game.controls.up = false;
            game.controls.right = true;
            game.controls.down = false;
        }
        if(22.5 < angle && angle < 67.5) {
            game.controls.left = false;
            game.controls.up = true;
            game.controls.right = true;
            game.controls.down = false;
        }
        if(67.5 < angle && angle < 112.5) {
            game.controls.left = false;
            game.controls.up = true;
            game.controls.right = false;
            game.controls.down = false;
        }   
        if(112.5 < angle && angle < 157.5) {
            game.controls.left = true;
            game.controls.up = true;
            game.controls.right = false;
            game.controls.down = false;
        }
        if(157.5 < angle && angle < 202.5) {
            game.controls.left = true;
            game.controls.up = false;
            game.controls.right = false;
            game.controls.down = false;
        }
        if(202.5 < angle && angle < 247.5) {
            game.controls.left = true;
            game.controls.up = false;
            game.controls.right = false;
            game.controls.down = true;
        }
        if(247.5 < angle && angle < 292.5) {
            game.controls.left = false;
            game.controls.up = false;
            game.controls.right = false;
            game.controls.down = true;
        }
        if(292.5 < angle && angle < 337.5) {
            game.controls.left = false;
            game.controls.up = false;
            game.controls.right = true;
            game.controls.down = true;
        }
    }
    function endMove() {
        var button = document.getElementById("control_button");
        button.style.top = "25%";
        button.style.left = "25%";
        game.controls.left = false;
        game.controls.up = false;
        game.controls.right = false;
        game.controls.down = false;
    }   
    var timeCount = 0;
    var inBuilding = false;
    var world = new Image(3200, 3200);
    var player_img = new Image(32, 32);
    player_img.src = "public/img/character test3.png";
    var tree_img = new Image(64, 64);
    tree_img.src = "public/img/tree_pix2.png";
    var smithy_img = new Image(128, 128);
    smithy_img.src = "public/img/smithy pix.png";
    var character = new Image(96, 128);
    character.src = "public/img/character sprite.png";
    var images = [];
    var eastImg = new Image(2000, 1000);
    eastImg.src = "public/img/1.2.png";
    var player;
    var obstaclesPos = [];
    var obstaclesSize = [];
    var linksPos = [];
    var linksSize = [];
    var lastCalledTime;
    var fps;
    var keys = [];
    var interval = false;
    var animationEnd = true;
    // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
    // 1 is normal then the picture will be painted in 1024 width and height.
    var render = 0;
    var gamePause = false;
    var loopIndex = 0;
    var counter = 0;
    var direction = 'none';
    var loopArray = [0, 1, 0, 2];
    var indexX = 32;
    var indexY = 0;
    var oldXbase;
    var oldYbase;
    CookieTicket = {
        checkCookieTicket(cookieNoob = "getOut") {
            var today = new Date();
            var cookieTicket;
            if(CookieTicket.sweetCookie === null) {
                cookieTicket = today.getMonth() + today.getDate() + "|";
                console.log(today.getMonth());
                console.log(today.getDate());
                for(var i = 0; i < 10; i++) {
                    cookieTicket += Math.floor(Math.random() * (10 - 1) + 1);
                }
                CookieTicket.sweetCookie = cookieTicket;
            }
            else {
                cookieTicket = CookieTicket.sweetCookie;
            }
            let data = "model=cookieMaker" + "&method=yummyCookies" + "&cookieTicket=" + cookieTicket + "&cookieNoob=" + cookieNoob;
            ajaxP(data, function(response) {
                console.log(response);
                console.log(response[1]);
                if(response[1] === "false") {
                    location.reload();
                }
                else {
                    return;
                }
            });
        },
        disposeGarbage() {
            let ego = event.target.innerText;
            if(ego === "") {
                checkCookieTicket();
            }
            else {
                burntCookie();
            }
        },
        burntCookie() {
            let data = "model=cookieMaker" + "&method=delicCookies" + "&cookieTicket=" + CookieTicket.sweetCookie;
            ajaxP(data, function(response) {
                console.log(response);
                if(response[1] == false) {
                    window.cancelAnimationFrame(game.properties.requestId);
                    game.loadWorld();
                }
                else {
                    return;
                }
            });    
        },
        sweetCookie: null,
    };
    game = {};
    game.fetchBuilding =  function(building = false) {
        inBuilding = true;
        if(building == false) {
            building = 'test';
        }
        // Legge til at man sjekker om man er i nærheten av en by
        console.log(building);
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var responseText = this.responseText.split("|");
                console.log(responseText);
                var link;
                if(document.getElementById("fetch_stylesheet") === null) {
                    console.log('create link');
                    link = document.createElement("link");
                    link.type = "text/css";
                    link.rel = "stylesheet";
                    link.setAttribute("id", "fetch_stylesheet");
                    link.href = "public/css/" + responseText[0].trim();
                }
                else {
                    link = document.getElementById("fetch_stylesheet");
                    link.href = "public/css/" + responseText[0].trim();    
                }
                document.getElementsByTagName("head")[0].appendChild(link);
                var script;
                var script2;
                var scripts = responseText[1].split("%");
                openNews(responseText[2], true);
                if(document.getElementById("fetch_script") === null) {
                    script = document.createElement("script");
                    script.src = "public/js/" + scripts[0].trim();
                    script.id = "fetch_script";
                    document.getElementsByTagName("section")[0].appendChild(script);
                }
                else {
                    script = document.createElement("script");
                    document.getElementById("fetch_script");
                    script.src = "public/js/" + scripts[0].trim();
                    script.id = "fetch_script";
                    document.getElementsByTagName("section")[0].replaceChild(script, document.querySelector("#fetch_script"));
                }
                if(document.getElementById("fetch_script2") === null && scripts.length > 1) {
                    script2 = document.createElement("script");
                    script2.src = "public/js/" + scripts[1].trim();
                    script2.id = "fetch_script2";
                }
                else if(scripts.length > 1) {
                    script2 = document.getElementById("fetch_script2");
                    script2.src = "public/js/" + scripts[1].trim();
                }
                if(script2 !== undefined) {
                    document.getElementsByTagName("section")[0].appendChild(script2);    
                }
                /*script.onload = function() {
                };*/    
            }
        };
        ajaxRequest.open('GET', "handlers/handler_v.php?" + "&building=" + building);
        ajaxRequest.send();
    };
    function draw(mx, my, sx, sy) {
        var ctx = game.properties.context;
        ctx.moveTo(mx, my);
        ctx.lineTo(sx, sy);
        ctx.stroke();
    }
    game.setCanvasDimensions = function() {
        let newWidth;
        if(screen.width < 830) {
            newWidth = document.getElementsByTagName("section")[0].offsetWidth * 0.97;
        }
        else {
            newWidth = document.getElementsByTagName("section")[0].offsetWidth * 0.68;
        }
        let newHeight = screen.height - 20;
        if(newHeight > 400) {
            newHeight = 400;
        }
        game.properties.canvasWidth = newWidth;
        game.properties.canvasHeight = newHeight;
        // Align all canvases
        let gameCanvas = document.querySelectorAll("canvas");
        for(var i = 0; i < gameCanvas.length; i++) {
            gameCanvas[i].width = newWidth;
            gameCanvas[i].height = newHeight;
            if(i > 0) {
                gameCanvas[i].style.top = gameCanvas[0].offsetTop + "px";
                gameCanvas[i].style.left = gameCanvas[0].offsetLeft + "px";
            }
        }
        game.properties.scale = newWidth / 700;
        game.properties.charX = Math.floor((newWidth / 2) - 32);
        game.properties.charY = Math.floor((newHeight / 2) - 32);
        console.log(document.getElementById("control"));
        if(document.getElementById("control").style.display !== "none") {
            let control = document.getElementById("control");
            control.style.top = gameCanvas[0].offsetTop + game.properties.canvasHeight - 125 +  "px";
            control.style.left = "10px";
            document.getElementById("inventory").style.top = gameCanvas[0].offsetTop + "px";
        }
        document.getElementById("game_text").style.maxWidth = game.properties.canvasWidth + "px";
    };
    var stateModule = (function () {
        var state = "DC"// Private Variable
        let humour = null;
        let load = false;
        let xbase = null;
        let ybase = null;
        
        var pub = {};// public object - returned at end of module

        pub.changeState = function (newstate) {
            state = newstate;
        };

        pub.getState = function() {
            return state;
        };
        const loadHumour = function(x, y) {
            if(load === true) {
                console.log('Function already set!');
                return;
            }
            xbase = x;
            ybase = y;
            load = true;
        };
        const getHumour = function() {
            return [xbase, ybase];
        };
        const setHumour = function(mood) {
            humour = mood;
        };
        return {
            getHumour: getHumour,
            loadHumour: loadHumour
        };
    }());
    game.properties = {
        context: document.getElementById("game_canvas").getContext("2d"),
        fillStyle1: "red",
        fillstyle2: "black",
        context2: document.getElementById("game_canvas2").getContext("2d"),
        context3: document.getElementById("game_canvas3").getContext("2d"),
        /*context4: document.getElementById("test_canvas").getContext("2d"),*/
        canvasWidth: document.getElementById("game_canvas").width,
        canvasHeight: document.getElementById("game_canvas").height,
        requestId: null,
        nagivateNext: false,
        xbase: 320,
        ybase: 200,
        // charX and charY where the character is drawn on canvas (middle);
        /*charX: 320,
        charY: 200,*/
        charX: 320,
        charY: 200,
        // MapMin/MapMax variables holds the coordinates of furtherst loaded chunks
        xMapMin: this.xbase - 320,
        xMapMax: this.xbase + 320,
        yMapMin: this.ybase - 320,
        yMapMax: this.ybase + 320,
        // xcamMove/ycamMove is the variables that holds how much the picture is moved. It is - 320 because the player is drawn is the middle on
        // x axis and - 200 on the y axis.
        xcamMove: null,
        ycamMove: null,
        loading: false,
        scale: null,
        device: "pc"
    };
    window.addEventListener("load", function() {
        // Check if there is phone
        if(window.screen.width > 830 ||
           (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == false)) {
            document.getElementById("control").style.display = "none";
            game.properties.actionText = "Press x";
            game.properties.device = "pc";
        }
        else {
            game.properties.actionText = "Tap to";
            game.properties.device = "mobile";
        }
        calculateHunger();
        game.setCanvasDimensions();
        game.loadWorld();
        game.inactivityTime(true);
        game.properties.startTime = new Date().getTime();
        CookieTicket.checkCookieTicket('checkMeOut');
    });
    gamePieces = {
        obstacles : [],
        links: [],
        objects: [],
        player: null
    };
    game.loadWorld = function(newxBase = false, newyBase = false, method = false, newMap = false)  {
        window.cancelAnimationFrame(game.properties.requestId);
        let data;
        let map;
        if(method !== false) {
            method = method;
            data = "model=worldLoader" + "&method=changeMap" + "&newMap=" + JSON.stringify(newMap);
        }
        else {
            method = "loadWorld";
            data = "model=worldLoader" + "&method=loadWorld";
        }
        ajaxG(data, function(response) {
            var responseText = response[1];
            if(response[0] != false) {
                responseText = responseText.split("|");
                console.log(responseText);
                map = responseText[0];
                if(responseText[1].length > 1) {
                    document.title = jsUcWords(responseText[1]);
                }
                var obj = JSON.parse(responseText[2]);
                gamePieces.buildings = [];
                gamePieces.objects = obj['objects'];
                if(newxBase === false) {
                    // Legge til xbase i JSON map filene
                    game.properties.xbase = 1000;    
                }
                else {
                    game.properties.xbase = newxBase;
                }
                if(newyBase === false) {
                    // Legge til ybase i JSON map filene
                    game.properties.ybase = 1500;    
                }
                else {
                    game.properties.ybase = newyBase;
                }
                /*stateModule.load(game.properties.xbase, game.properties.ybase);*/
                //
                // to compensate for the player starting in another position you must subtract  (xbase - (xbase -320));
                game.properties.xcamMove = game.properties.xbase - game.properties.charX;
                game.properties.ycamMove = game.properties.ybase - game.properties.charY;
                for(var i = 0; i < gamePieces.objects.length; i++) {
                    gamePieces.objects[i].img = new Image(gamePieces.objects[i].width, gamePieces.objects[i].height);
                    gamePieces.objects[i].img.src = "public/images/" + gamePieces.objects[i].src;
                    // The diameters of the object is based on player starting on 320,
                    
                    /* drawX/drawY is the position of object on canvas and where it is drawn.
                     * The is in the same place the only change is where it
                        is drawn*/
                    gamePieces.objects[i].drawX = (gamePieces.objects[i].x - game.properties.xcamMove);
                    gamePieces.objects[i].drawY = (gamePieces.objects[i].y - game.properties.ycamMove);
                    console.log(game.properties.ycamMove);
                    if(game.properties.scale < 1) {
                        /*gamePieces.objects[i].diameterTop -= 10;
                        gamePieces.objects[i].diameterDown -= 10;
                        gamePieces.objects[i].diameterRight -= 8;
                        gamePieces.objects[i].diameterLeft -= 6;*/
                    }
                
                     /*
                     *gamePieces.objects[i].diameterRight -= (xbase - (xbase - 320));
                    gamePieces.objects[i].diameterLeft -= (xbase - (xbase - 320));
                    gamePieces.objects[i].diameterTop -= (ybase - (ybase - 150));
                    gamePieces.objects[i].diameterDown -= (ybase - (ybase - 150));*/
                    if(gamePieces.objects[i].type == "building") {
                        gamePieces.buildings.push(gamePieces.objects[i]);
                    }
                }
                console.log(game.properties.scale);
                
            }
            else {
                // Load game picture
                world.src = false;
                //responseText[1];
                // Load obstacles and pieces
                console.log(response[0]);
                
            }
            xMapMin = game.properties.xbase - 320;
            xMapMax = game.properties.xbase + 320;
            yMapMin = game.properties.ybase - 320;
            yMapMax = game.properties.ybase + 320;
            world = new Image(3201, 3201);
            world.src = "public/images/" + map + ".png";
            console.log(world);
            world.onload = function() {
                gamePieces.player = new newPlayer(30, 30, "#0000A0", game.properties.xbase, game.properties.ybase);
                game.startGame();    
            };
        });
    };
    game.controls = {
        left: false,
        up: false,
        right: false,
        down: false
    };
    game.viewport = {
        counter: 0,
        scale: game.properties.canvasWidth / 700,
        draw: function() {
            ctx = game.properties.context;
            ctx.clearRect( - gamePieces.player.xMovement, - gamePieces.player.yMovement, 700, 700);
            game.properties.context3.clearRect(0, 0, 700, 700);
            ctx.save();
            //Draw world and translate the image according to players movement
            ctx.drawImage(world, game.properties.xcamMove + gamePieces.player.xMovement,
                          game.properties.ycamMove + gamePieces.player.yMovement, 1024 * this.scale, 1024 * this.scale, 0, 0,
                          1024 * this.scale, 1024 * this.scale);
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
            /*ctx.fillRect(x, y, width, height)*/
            if(game.properties.xbase + gamePieces.player.xMovement < game.properties.charX) {
                    ctx.fillStyle = "black";
                    ctx.fillRect(0, 0, (game.properties.xbase + gamePieces.player.xMovement - game.properties.charX) * -1, 600);
            }
            if(game.properties.ybase + gamePieces.player.yMovement < 200) {
                ctx.fillStyle = "black";
                ctx.fillRect(0, 0, game.properties.canvasWidth, (game.properties.ybase + gamePieces.player.yMovement - 168) * -1);
            }
            if(gamePieces.player.xMovement + game.properties.xbase > 3200 - game.properties.charX - 100) {
                ctx.fillStyle = "black";
                ctx.fillRect(game.properties.canvasWidth, 0, - (game.properties.xbase + gamePieces.player.xMovement -
                                                              (3200 - game.properties.charX - 64)), 600);
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
    game.loadChunk = function(x, y, xpos, ypos) {
        var data = "model=worldLoader" + "&method=loadChunks" +
                    "&xMapMin=" + xMapMin + "&yMapMin=" + yMapMin + "&xbase=" + xpos +  "&ybase=" + ypos;
        ajaxG(data, function(response) {
            var responseText = JSON.parse(response[1]);
            for(var i = 0; i < gamePieces.objects.length; i++) {
                let object = gamePieces.objects[i];
                if(object.y > yMapMax + 400 || object.y < yMapMin - 400 || object.x > xMapMax + 400 || object.x < xMapMin - 400) {
                    console.log('helasaslo');
                    gamePieces.objects.splice(i, 1);
                }
            }
            gamePieces.objects = gamePieces.objects.concat(responseText);
            console.log(gamePieces.objects.length);
            console.log(gamePieces.objects);
        });
    };
    game.startGame = function () {
        game.viewport.draw();
        game.viewport.drawEdge();
        player = gamePieces.player;
        gamePieces.player.newPos();
        game.properties.requestId = window.requestAnimationFrame(game.update);
        game.updateGamePiece();
        game.properties.loading = false;
    };
    game.loadingScreen = function() {
        game.properties.loading = true;
        let ctx = game.properties.context;
        ctx.fillStyle = "black";
        ctx.fillRect(0, 0, 700, 700);
        ctx.font = "30px Comic Sans MS";
        ctx.fillStyle = "white";
        ctx.textAlign = "center";
        ctx.fillText("Loading ...", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
    };
    game.loadGamePieces = function() {
        var links = [];
        
        /*// push game obstacles objects into array
        for(var i = 0; i < list.length; i++) {
            gamePieces.obstacles[i] = new gameObstacle(list[i][0], list[i][1], list[i][2], list[i][3],
                                                                          list[i][4], list[i][5]);
        }*/
        // push game links objects into array
        for(var x = 0; x < links.length; x++) {
            gamePieces.links[x] = new gameLinks(links[x][0], links[x][1], links[x][2], links[x][3],
                                                                          links[x][4], links[x][5], links[x][6]);
        }
    };
    game.inactivityTime = function (pause) {
        var time;
        document.onkeydown = resetTimer;
        document.ontouchmove = resetTimer;
        if(pause == true) {
            resetTimer();
        }
        function pauseGame() {
            console.log(game.controls.up + " " + game.controls.right + " " + game.controls.down + " " + game.controls.left);
            if((game.controls.up !== false || game.controls.right !== false ||
                game.controls.down !== false || game.controls.left !== false) && game.properties.device == "pc") {
                resetTimer();
                return false;
            }
            else if((document.getElementById("control_button").style.top !== "25%" &&
                document.getElementById("control_button").style.display != "none") && game.properties.device == "mobile") {
                resetTimer();
                return false;
            }
            updateHunger();
            ctx = game.properties.context;
            ctx.font = "30px Comic Sans MS";
            ctx.fillStyle = "pink";
            ctx.textAlign = "center";
            ctx.fillText("Game Paused", game.properties.canvasWidth / 2 - 50, game.properties.canvasHeight / 2);
            ctx.font = "20px Comic Sans MS";
            ctx.fillText("Press any key to continue", game.properties.canvasWidth / 2 - 50, game.properties.canvasHeight / 2 + 35);
            window.cancelAnimationFrame(game.properties.requestId);
            gamePause = true;
        }
        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(pauseGame, 10000);
            // 1000 milliseconds = 1 second
        }
    };
    game.canvasText = {
        textDrawn: false,
        intervalID: 0,
        gameText: document.getElementById("game_text"),
        showText(text, timer = true, textDrawn = false) {
            if(textDrawn === false) {
                let canvas = document.getElementById("game_canvas");
                this.gameText.style.top = canvas.offsetTop + 50 + "px";
                this.gameText.innerHTML = text;
                this.gameText.style.opacity = 1;
                /*this.intervalID = setInterval(this.changeTextOpactiy, 100);*/
                // Set textDrawn status to true so it can be cleared
                this.textDrawn = true;
            }
            if(timer == true) {
                // Set textDrawn to false, so that hideText wont be called from viewport.drawEdge
                this.textDrawn = false;
                setTimeout(this.hideText, 3000);    
            }
        },
        hideText() {
            this.textDrawn = false;
            console.log('hideText');
            game.canvasText.gameText.style.opacity = 0;
            setTimeout(function() {
                
            game.canvasText.gameText.innerHTML = "";}, 500);
        },
        changeTextOpactiy() {
            let opacity = game.canvasText.gameText.style.opacity = Number(game.canvasText.gameText.style.opacity) + 0.1;
            if(opacity > 1) {
                clearInterval(game.canvasText.intervalID);
            }
        }
    };
    function resumeGame() {
        game.inactivityTime();
        gamePause = false;
        game.properties.requestId = window.requestAnimationFrame(game.update);
        game.properties.startTime = new Date().getTime();
    }
    function gameObstacle (name, x, y, width, height, style) {
        this.name = name;
        this.x = x;
        this.y = y;
        this.width = width / game.viewport.scale;
        this.height = height / game.viewport.scale;
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
        /*linksPos.push([x, y, this.diameterTop, this.diameterRight, this.diameterDown, this.diameterLeft, width, height]);*/
    }
    function newPlayer(width, height, color, x, y) {
        this.width = width;
        this.height = height;
        this.speedX = 0;
        this.speedY = 0;
        this.speed = 2;
        this.x = x;
        this.y = y;
        this.xMovement = 0;
        this.yMovement = 0;
        this.top = "open";
        this.left = "open";
        this.down = "open";
        this.right = "open";
        this.diameterTop = y + 20;
        this.diameteRight = x + width;
        this.diameterDown = y + height;
        this.diameterLeft = x;
        // xpos and ypos is the position of the player in the world
        this.xpos = game.properties.xbase + this.xMovement;
        this.ypos = game.properties.ybase + this.yMovement;
        this.first = function() {
            ctx = game.properties.context;
            ctx.drawImage(character, indexX * 0, indexY, 32, 32, game.properties.charX, game.properties.charY, 32, 32);
        };
        this.newPos = function(newPos = true) {
            ctx2 = game.properties.context2;
            this.top = "open";
            this.left = "open";
            this.down = "open";
            this.right = "open";
            //drawing starts at x (diameterLeft) and y (diameterTop) line
            if(newPos !== false) {
                this.xpos = game.properties.xbase + gamePieces.player.xMovement; /*(gamePieces.player.xMovement / game.viewport.scale)*/
                this.ypos = game.properties.ybase + gamePieces.player.yMovement; /*(gamePieces.player.yMovement / game.viewport.scale)*/
                game.properties.xMapMin = this.xpos - 320;
                game.properties.xMapMax = this.xpos + 320;
                game.properties.yMapMin = this.ypos - 320;
                game.properties.yMapMax = this.ypos + 320;
                player.diameterTop = this.ypos + 20;
                player.diameterRight = this.xpos + width;
                player.diameterDown = this.ypos + height;
                player.diameterLeft = this.xpos;
            }
            var newdirection = 'none';
            if(game.controls.left == true && game.controls.down == true) {
                newdirection = 'left, down';
            }
            if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
                newdirection = 'right';
                indexY = 32;
            }
            if(game.controls.left == true && game.controls.up == false && game.controls.down == false) {
                newdirection = 'left';
                indexY = 64;
            }
            if(game.controls.down == true) {
                newdirection = 'right, down';
                indexY = 0;
            }
            if(game.controls.up == true) {
                newdirection = 'right, top';
                indexY = 96;
            }
            if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
                newdirection = 'right';
                indexY = 32;
            }
            if(game.controls.up == false && game.controls.left == false && game.controls.right == false && game.controls.down == false) {
                newdirection = 'none';
            }
            if(newdirection != 'none' && ((oldYbase != this.ypos) || (oldXbase != this.xpos))) {
                // Change image when counter is past every 15
                if(newdirection != 'none' && counter % 15 == 0) {
                    ctx2.clearRect(0, 0, 700, 700);
                    ctx2.drawImage(character, indexX * loopArray[loopIndex],
                                   indexY, 32 * game.viewport.scale, 32 * game.viewport.scale, game.properties.charX,
                                   game.properties.charY, 32, 32);
                    loopIndex++;
                }
                else if(newdirection != direction) {
                    ctx2.clearRect(0, 0, 700, 700);
                    loopIndex = 0;
                    direction = newdirection;
                    ctx2.drawImage(character, indexX * loopIndex, indexY, 32 * game.viewport.scale, 32 * game.viewport.scale,
                                   game.properties.charX, game.properties.charY, 32, 32);
                    loopIndex++;
                }
                if(loopIndex == 4 && newdirection != 'none') {
                    loopIndex = 0;
                }
                counter++;
                animationEnd = false;
            }
            else {
                ctx2.clearRect(0, 0, 700, 700);
                ctx2.drawImage(character, 0, indexY, 32 * game.viewport.scale, 32 * game.viewport.scale, game.properties.charX,
                               game.properties.charY, 32, 32);
                animationEnd = true;
            }
            oldYbase = gamePieces.player.ypos;
            oldXbase = gamePieces.player.xpos;
        };
    }
    game.updateGamePiece = function() {
            ctx = game.properties.context;
            function checkPos(object) {
                return ((Math.abs(object.diameterRight - player.xpos) <= game.properties.canvasWidth || 
                         Math.abs(object.diameterLeft - player.xpos) <= game.properties.canvasWidth) &&
                            (Math.abs(object.y - player.ypos) <= game.properties.canvasHeight ||
                             Math.abs(object.diameterTop - player.ypos) <= game.properties.canvasHeight));
            }
            // IMPORTANT!
            // Using fillRect, canvas will render element from top down. Meaning y = 0 is top of element
            // Using drawImage, canvas will render element from bottom down, meaning y = 0 is bottom of element
            let draw = false;
            /*let visibleObjects = gamePieces.objects.filter(checkPos);*/
            let visibleObjects = gamePieces.objects;
            for(var i = 0; i < visibleObjects.length; i++) {
                /*if(object.y > yMapMax + 400 || object.y < yMapMin - 400  || object.x > xMapMax + 400 || object.x < xMapMin - 400) {
                    continue;
                }*/
                if(draw == true) {
                    ctx.fillStyle = "red";
                    ctx.fillRect(visibleObjects[i].drawX - (player.xMovement / game.viewport.scale),
                                 visibleObjects[i].drawY - (player.yMovement / game.viewport.scale),
                                 visibleObjects[i].width, visibleObjects[i].height);
                    ctx.font = "10px Comic Sans MS";
                    ctx.fillStyle = "white";
                    ctx.fillText(i + ' | ' + visibleObjects[i].id,
                                 visibleObjects[i].drawX - (player.xMovement / game.viewport.scale) +
                                 (visibleObjects[i].width / 2),
                                 visibleObjects[i].drawY + (visibleObjects[i].height / 2) -
                                 (player.yMovement / game.viewport.scale));
                }
                /*if(visibleObjects[i].id == 157) {
                    let drawContext;
                    // If building is behind player, then draw on the first canvas instead of the third
                    if(visibleObjects[i].diameterTop < player.ypos) {
                        drawContext = ctx;
                    }
                    else{
                        drawContext = game.properties.context3;  
                    }
                    drawContext.drawImage(visibleObjects[i].img,
                                                        visibleObjects[i].drawX - (player.xMovement * game.viewport.scale),
                                  visibleObjects[i].drawY - (player.yMovement * game.viewport.scale));
                }*/
                /*if(typeof gamePieces.objects[i].src === 'undefined' && draw === true) {
                    ctx.fillStyle = "red";
                    ctx.fillRect(gamePieces.objects[i].drawX - (player.xMovement / game.viewport.scale),
                                 gamePieces.objects[i].drawY - (player.yMovement / game.viewport.scale),
                                 gamePieces.objects[i].width, gamePieces.objects[i].height);
                    ctx.font = "10px Comic Sans MS";
                    ctx.fillStyle = "white";
                    ctx.fillText(i + ' | ' + gamePieces.objects[i].id,
                                 gamePieces.objects[i].drawX - (player.xMovement / game.viewport.scale) +
                                 (gamePieces.objects[i].width / 2),
                                 gamePieces.objects[i].drawY + (gamePieces.objects[i].height / 2) -
                                 (player.yMovement / game.viewport.scale));
                }*/
                
            }
            /*if(player.y < gamePieces.objects[4].drawY) {
                game.properties.context3.clearRect(0, 0, 700, 700);
                 game.properties.context3.drawImage(gamePieces.objects[4].img,
                                                    gamePieces.objects[4].drawX - 64 - (player.xMovement * game.viewport.scale),
                                  gamePieces.objects[4].drawY - 128 - (player.yMovement * game.viewport.scale));
            }
            else {
                ctx.drawImage(gamePieces.objects[4].img,
                                                    gamePieces.objects[4].drawX - 64 - (player.xMovement * game.viewport.scale),
                                  gamePieces.objects[4].drawY - 128 - (player.yMovement * game.viewport.scale));
            }*/
            /*game.properties.context3.drawImage(tree_img, 2 - (xMovement / scale), 2 - (yMovement / scale), 64, 64);*/
            /*for(var x = 0; x < linksPos.length; x++) {
                //Change xpos and ypos for links
                ctx = game.properties.context;
                /*ctx.drawImage(gamePieces.links[x].img, linksPos[x][0] - xMovement, linksPos[x][1] - yMovement, 400, 400);*/
                /*ctx.translate(linksPos[x][0] - xMovement, linksPos[x][1] - yMovement);*/
                /*ctx.fillStyle = "none";
                ctx.fillRect(linksPos[x][0] - xMovement, linksPos[x][1] - yMovement, linksPos[x][6], linksPos[x][7]);
            }*/
    };
    game.checkBuilding = function() {
        if(inBuilding != true) {
            for(i = 0; i < gamePieces.buildings.length; i++) {
                let object = gamePieces.buildings[i];
                if(player.ypos > object.diameterTop && player.ypos < object.diameterDown &&
                   player.xpos > object.diameterLeft && player.xpos < object.diameterRight &&
                   Math.abs(player.ypos - object.diameterDown) < 32) {
                        if(inBuilding == false) {
                            game.fetchBuilding(object.src.split(".png")[0]);
                        }
                        break;
                    }
            }
        }  
    };
    game.calculateDistance = function() {
        // Collision detection, if user is less than 1px from object prevent movement
    
        
        /*let nearObjects = [];
        let width = game.properties.canvasWidth / 2 - 16;
        let height = game.properties.canvasHeight / 2 - 16;
        let objectIndexX = xpos;
        let objectIndexY = ypos;
        if(Math.abs(objectIndexX - xpos) >= 400 && Math.abs(objectIndexY - ypos) >= 400) {
            
        }
        for(i = 0; i < gamePieces.objects.length; i++) {
            if(Math.abs(gamePieces.objects[i][x] - player.x) <= width && Math.abs(gamePieces.objects[i][y] - player.y) <= height) {
                nearObjects.push(gamePieces.objects[i]);    
            }
        }*/
        /*function checkPos(object) {
             return (Math.abs(object.x - player.xpos) <= 400 && Math.abs(object.y - player.ypos) <= 400);
        }
        */
        
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
        // Triangle collision detection
        /*if(xtmax > player.diameterLeft && ytmin < player.diameterDown && (hYPos + 1 >= player.diameterLeft || )) {
            
        }*/
        
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
            
            gamePieces.player.xMovement += player.speedX;
            gamePieces.player.yMovement += player.speedY;
    };
    game.update = function () {
        /*timeCount++;
        console.log(timeCount);*/
        player.speedX = 0;
        player.speedY = 0;
        if(game.controls.left == true) {
            player.speedX = - player.speed;
        }
        if(game.controls.right == true) {
            player.speedX = player.speed;
        }
        if(game.controls.up == true) {
            player.speedY = - player.speed;
        }
        if(game.controls.down == true) {
            player.speedY = player.speed;
        }
        if(player.speedX != 0 || player.speedY != 0 && inBuilding == false) {
            game.calculateDistance();
            game.viewport.draw();
            game.viewport.drawEdge();
            player.newPos();
            game.updateGamePiece();
        }
        else if(animationEnd != true) {
            player.newPos(false);
        }
        game.properties.requestId = window.requestAnimationFrame(game.update);
    };
    game.getNextMap = function() {
        /*let coordinatesArray = map.split(",");
        let x = coordinatesArray[0];
        let y = coordinatesArray[1];*/
        let newX = 0;
        let newY = 0;
        let newxBase = gamePieces.player.xpos;
        let newyBase = gamePieces.player.ypos;
        let match = false;
        if(gamePieces.player.ypos > 3160) {
            newY += 1;
            newyBase = 0;
            match = true;
        }
        else if(gamePieces.player.ypos < 3160 && gamePieces.player.ypos < 32) {
            newY -= 1;
            newyBase = 3160;
            match = true;
        }
        if(gamePieces.player.xpos > 3150) {
            newX += 1;
            newxBase = 0;
            match = true;
        }
        else if(gamePieces.player.xpos < 3160 && gamePieces.player.xpos < 32) {
            newX -= 1;
            newxBase = 3160;
            match = true;
        }
        console.log(match);
        if(match !== true) {
            return false;
        }
        else {
            game.loadWorld(newxBase, newyBase, "changeMap", {"new_x": newX, "new_y": newY});    
        }
    };
    function checkChunk() {
        // Scale ?
        var xDifference = Math.abs((xbase + xMovement - 320) - xMapMin);
        var yDifference = Math.abs((ybase + yMovement - 320) - yMapMin); 
        if(xDifference > 160 || yDifference > 160) {
            /*console.log(xMapMin);
            console.log(yMapMin);*/
            /*game.loadChunk(xMapMin, yMapMin, xbase + xMovement, ybase + yMovement);*/
            if(xDifference > 160) {
                xMapMin = xbase + xMovement - 320;
                xMapMax = xbase + xMovement + 320;
            }
            else {
                yMapMin = ybase + yMovement - 320;
                yMapMax = ybase + yMovement + 320;
            }
            /*console.log(xMapMin);
            console.log(yMapMin);*/
        }
    }
    function renderPlayer(x, y) {
        player = gamePieces.player;
        player.speedX = x;
        player.speedY = y;
        if(game.controls.left == true) { player.speedX = - player.speed; }
        if(game.controls.right == true) { player.speedX = player.speed; }
        if(game.controls.up == true) { player.speedY = - player.speed; }
        if(game.controls.down == true) { player.speedY = player.speed; }
        if(player.speedX != 0 || player.speedY != 0) {
            game.calculateDistance();
            game.viewport.draw();
            game.viewport.drawEdge();
            player.newPos();
            game.updateGamePiece(game.properties.xbase, game.properties.ybase);
        }
    }
    function getObjectPoints(object) {
        if(object.type === "triangle") {
            let slope = object.width / object.height;
            object.slope = slope;
        }
        checkCollision(object);
    }
    function checkCollision(object) {
        let objectPositionX;
        let objectPositionY;
        for(var i = 0; i < object.width; i++) {
            objectPositionX = objectPositionX * (i * object.slope);
            objectPositionY = objectPositionY * (i * object.slope);
            if(player.x < object.x) {
                if(objectPositionX === player.diameterRight && (player.diameterDown == objectPositionY || 
                                                                player.diameterTop == objectPositionY)) {
                    console.log("player.right = blocked");    
                }
            }
            else {
                
            }
            if(player.y < object.y) {
                if(objectPositionY === player.diameterDown && (player.diameteRight == objectPositionX ||
                                                               player.diameterLeft == objectPositionX)) {
                    console.log("player.left = blocked");
                }
            }
            else {
                
            }
        }
    }
    var santaClaus = "santa";