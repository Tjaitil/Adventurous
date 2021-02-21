
var click = 0;
function doubleClickDetect() {
    click++;
    if(click == 2) {
        click = 0;
        return true;
    }
    else {
        setTimeout(function() {
            click = 0;  
        }, 300);
    }
}
/*window.addEventListener("click", doubleClickDetect);*/
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
    var timeCount = 0;
    var inBuilding = false;
    var world = new Image(3200, 3200);
    let duration = 0;
    /*var player_img = new Image(32, 32);
    player_img.src = "public/img/character test3.png";
    var tree_img = new Image(64, 64);
    tree_img.src = "public/img/tree_pix2.png";
    var smithy_img = new Image(128, 128);
    smithy_img.src = "public/img/smithy pix.png";
    var character = new Image(96, 128);
    character.src = "public/img/character sprite.png";
    var images = [];
    var eastImg = new Image(2000, 1000);
    eastImg.src = "public/img/1.2.png";*/
    var player;
    var obstaclesPos = [];
    var obstaclesSize = [];
    var linksPos = [];
    var linksSize = [];
    var lastCalledTime;
    var fps;
    var keys = [];
    var interval = false;
    var picture = new Image(108, 40);
    picture.src = "public/images/coin.png";
    
    CookieTicket = {
        checkCookieTicket(cookieNoob = "getOut") {
            var today = new Date();
            var cookieTicket;
            if(CookieTicket.sweetCookie === null) {
                cookieTicket = today.getMonth() + today.getDate() + "|";
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
                    // Session check return false, go to logout
                    location.href = "/logout";
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
                    game.loadWorld(false);
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
        let h = document.createElement("h2");
        h.innerText = "Loadding!";
        openNews(h);
        /*let test = game.checkBuilding();*/
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var responseText = this.responseText.split("|");
                if(responseText.indexOf("ERROR") != -1) {
                    gameLog("Something unexpected happened, please try again", true);
                }
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
        let newHeight;
        // If the device is mobile check for the shortest dimension of height and width to compensate for already rotated devices
        if(game.properties.device == "mobile") {
            newHeight = (screen.width < screen.height) ? screen.width - 20 : screen.height - 20;
            console.log(newHeight);
        }
        else {
            newHeight = screen.height - 20;    
        }
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
        game.properties.scale = 2;
        game.properties.charX = Math.floor((newWidth / 2) - 32);
        game.properties.charY = Math.floor((newHeight / 2) - 32);
        if(document.getElementById("control").style.display !== "none") {
            let control = document.getElementById("control");
            control.style.top = gameCanvas[0].offsetTop + game.properties.canvasHeight - 125 +  "px";
            control.style.left = "10px";
            document.getElementById("inventory").style.top = gameCanvas[0].offsetTop + "px";
        }
        document.getElementById("control_text").style.top = gameCanvas[0].offsetTop + game.properties.canvasHeight - 50 +  "px";
        document.getElementById("control_text").style.left = gameCanvas[0].offsetLeft + newWidth - 140 + "px";
        document.getElementById("game_text").style.maxWidth = game.properties.canvasWidth + "px";
        /* If screen is less than 830 set sidebar to be the same top as inventory so that the two are aligned
         * Also align cont_exit button in news content to middle instead of right */
        if(window.screen.width < 830) {
            document.getElementById("sidebar").style.top = gameCanvas[0].offsetTop + "px";
            document.getElementById("inv_toggle_button_container").style.top = gameCanvas[0].offsetTop + "px";
            let cont_exit_button = document.getElementById("cont_exit");
            cont_exit_button.style.zIndex = "1";
            cont_exit_button.style.cssFloat = "";
            cont_exit_button.style.margin = "0 auto";
            cont_exit_button.style.marginBottom = "20px";
        }
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
        textContext: document.getElementById("text_canvas").getContext("2d"),
        canvasWidth: document.getElementById("game_canvas").width,
        canvasHeight: document.getElementById("game_canvas").height,
        requestId: null,
        nagivateNext: false,
        timestamp: 0,
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
        gamePause: false,
        // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
        // 1 is normal then the picture will be painted in 1024 width and height.
        scale: 1.5,
        device: "pc"
    };
    gamePieces = {
        events: [],
        obstacles : [],
        links: [],
        objects: [],
        daqloon: [],
        player: {
            width: 32,
            height: 32,
            speedX: 0,
            speedY: 0,
            speed: 3.5,
            character: new Image(96, 128),
            characterAttack: new Image(114, 32),
            x: null,
            y: null,
            attack: false,
            xMovement: 0,
            yMovement: 0,
            top: "open",
            left: "open",
            down: "open",
            right: "open",
            playerSize: 32,
            diameterTop: this.y + 20,
            diameteRight: this.x + this.width,
            diameterDown: this.y + this.height,
            diameterLeft: this.x,
            // xpos and ypos is the position of the player in the world
            xpos: game.properties.xbase + this.xMovement,
            ypos: game.properties.ybase + this.yMovement,
            oldXbase: 0,
            oldYbase: 0,
            animationEnd: true,
            loopIndex: 0,
            counter: 0,
            direction: 'none',
            loopArray: [0, 1, 0, 2],
            indexX: 32,
            indexY: 0,
            index: 0,
            attackLoop: 0,
            lastAttack: 0,
            combat: false,
            attackDamage: 10,
            movementSpeed: 60,
            startCombat: function() {
                
            },
            takeDamage: function() {
                // Draw sprite that takes damage
                ctx2.clearRect(0, 0, 700, 700);
                ctx2.drawImage(this.characterAttack,
                                   41 * 2,
                                   38 * 1,
                                   32 * viewport.scale,
                                   32 * viewport.scale,
                                   game.properties.charX,
                                   game.properties.charY,
                                   this.playerSize,
                                   this.playerSize);
                
            },
            newPos: function(newPos = true) {
                ctx2 = game.properties.context2;
                this.top = "open";
                this.left = "open";
                this.down = "open";
                this.right = "open";
                //drawing starts at x (diameterLeft) and y (diameterTop) line
                if(newPos !== false) {
                    gamePieces.player.xpos =
                        game.properties.xbase + gamePieces.player.xMovement;
                    gamePieces.player.ypos =
                        game.properties.ybase + gamePieces.player.yMovement; 
                    game.properties.xMapMin = this.xpos - 320;
                    game.properties.xMapMax = this.xpos + 320;
                    game.properties.yMapMin = this.ypos - 320;
                    game.properties.yMapMax = this.ypos + 320;
                    gamePieces.player.diameterTop = this.ypos + 20;
                    gamePieces.player.diameterRight = this.xpos + this.width - 4;
                    gamePieces.player.diameterDown = this.ypos + this.height;
                    gamePieces.player.diameterLeft = this.xpos + 4;
                }
                if(this.combat == true) {
                    /*if(this.loopIndex == 2 && this.attack == false && duration % 5 == 0) {
                        this.loopIndex = 0;
                        ctx2.clearRect(0, 0, 700, 700);
                        ctx2.drawImage(this.characterAttack,
                                       38 * this.loopIndex,
                                       0,
                                       32 * viewport.scale,
                                       32 * viewport.scale,
                                       game.properties.charX,
                                       game.properties.charY,
                                       38,
                                       38);
                        this.loopIndex = 1;
                    }*/
                    if(this.attack == true && duration % 2 == 0) {
                        if(this.attackLoop == 0) {
                            for(var i = 0; i < gamePieces.daqloon.length; i++) {
                                if(Math.abs(gamePieces.daqloon[i].x - this.xpos) < 30 &&
                                   Math.abs(gamePieces.daqloon[i].y - this.ypos) < 30) {
                                    gamePieces.daqloon[i].x -= 20;
                                    gamePieces.daqloon[i].drawX -= 20;
                                    gamePieces.daqloon[i].y -= 20;
                                    gamePieces.daqloon[i].drawY -= 20;
                                    gamePieces.daqloon[i].hit();
                                }   
                        }   
                        }
                        if(this.loopIndex > 1) {
                            this.loopIndex = 0;
                        }
                        ctx2.clearRect(0, 0, 700, 700);
                        ctx2.drawImage(this.characterAttack,
                                           41 * this.loopIndex,
                                           38 * 1,
                                           32 * viewport.scale,
                                           32 * viewport.scale,
                                           game.properties.charX,
                                           game.properties.charY,
                                           this.playerSize,
                                           this.playerSize);
                        this.loopIndex++;
                        this.attackLoop++;
                        if(this.attackLoop == 2) {
                            this.attack = false;
                            this.attackLoop = 0;
                        }
                    }
                    else if(duration % 10 == 0) {
                        if(this.loopIndex > 3) {
                            this.loopIndex = 0;
                        }
                        ctx2.drawImage(this.characterAttack, 0, 0);
                        /*ctx2.drawImage(this.character,
                                       0,
                                       this.indexY,
                                       32 * viewport.scale,
                                       32 * viewport.scale,
                                       game.properties.charX,
                                       game.properties.charY,
                                       30,
                                       30);*/
                        ctx2.clearRect(0, 0, 700, 700);
                        ctx2.drawImage(this.characterAttack,
                                       41 * this.loopIndex,
                                       0,
                                       32 * viewport.scale,
                                       32 * viewport.scale,
                                       game.properties.charX,
                                       game.properties.charY,
                                       this.playerSize,
                                       this.playerSize);
                        this.loopIndex++;
                    }
                }
                else {
                    var newdirection = 'none';
                    if(game.controls.left == true && game.controls.down == true) {
                        newdirection = 'left, down';
                    }
                    if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
                        newdirection = 'right';
                        this.indexY = 32;
                    }
                    if(game.controls.left == true && game.controls.up == false && game.controls.down == false) {
                        newdirection = 'left';
                        this.indexY = 64;
                    }
                    if(game.controls.down == true) {
                        newdirection = 'right, down';
                        this.indexY = 0;
                    }
                    if(game.controls.up == true) {
                        newdirection = 'right, top';
                        this.indexY = 96;
                    }
                    if(game.controls.right == true && game.controls.up == false && game.controls.down == false) {
                        newdirection = 'right';
                        this.indexY = 32;
                    }
                    if(game.controls.up == false && game.controls.left == false && game.controls.right == false && game.controls.down == false) {
                        newdirection = 'none';
                    }
                    if(newdirection != 'none' && ((this.oldYbase != this.ypos) || (this.oldXbase != this.xpos))) {
                        // Change image when counter is past every 15
                        if(newdirection != 'none' && this.counter % 10 == 0) {
                            ctx2.clearRect(0, 0, 700, 700);
                            ctx2.drawImage(this.character, this.indexX * this.loopArray[this.loopIndex],
                                           this.indexY, 32 * viewport.scale, 32 * viewport.scale, game.properties.charX,
                                           game.properties.charY, this.playerSize, this.playerSize);
                            this.loopIndex++;
                        }
                        else if(newdirection != this.direction) {
                            ctx2.clearRect(0, 0, 700, 700);
                            this.loopIndex = 0;
                            this.direction = newdirection;
                            ctx2.drawImage(this.character, this.indexX * this.loopIndex, this.indexY,
                                           32 * viewport.scale, 32 * viewport.scale,
                                           game.properties.charX, game.properties.charY, this.playerSize, this.playerSize);
                            this.loopIndex++;
                        }
                        if(this.loopIndex == 4 && this.newdirection != 'none') {
                            this.loopIndex = 0;
                        }
                        this.counter++;
                        this.animationEnd = false;
                    }
                    else {
                        ctx2.clearRect(0, 0, 700, 700);
                        ctx2.drawImage(this.character, 0, this.indexY, 32 * viewport.scale, 32 * viewport.scale,
                                       game.properties.charX, game.properties.charY, this.playerSize, this.playerSize);
                        this.animationEnd = true;
                    }   
                }
                this.oldYbase = gamePieces.player.ypos;
                this.oldXbase = gamePieces.player.xpos;
            }
        }
    };
    game.loadWorld = function(pause = true, newxBase = false, newyBase = false, method = false, newMap = false)  {
        game.properties.loading = true;
        let ctx = game.properties.context;
        ctx.fillStyle = "black";
        ctx.fillRect(0, 0, 700, 700);
        ctx.font = "30px Comic Sans MS";
        ctx.fillStyle = "white";
        ctx.textAlign = "center";
        ctx.fillText("Loading ...", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
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
                    console.log(jsUcWords(responseText[1]));
                }
                var obj = JSON.parse(responseText[2]);
                gamePieces.buildings = [];
                gamePieces.characters = [];
                let events = JSON.parse(responseText[3]);
                for(let x = 0; x < events.length; x++) {
                    eventHandler.events.push(events[x]);
                }
                gamePieces.objects = obj['objects'];
                let startPoint;
                function findStartPoint(object) {
                    return (object.type === "start_point");
                }
                function removeStartPoint(object) {
                    return (object.type !== "start_point");
                }
                let startPoints = gamePieces.objects.filter(findStartPoint);
                gamePieces.objects = gamePieces.objects.filter(removeStartPoint);
                if(newxBase !== false) {
                    // Legge til xbase i JSON map filene
                    game.properties.xbase = newxBase;
                    console.log(newxBase);
                }
                /*else if(startPoints.length > 0) {
                    console.log('startPoint');
                    game.properties.xbase = startPoints[0].x;
                }*/
                else {
                    game.properties.xbase = 1856; 
                }
                if(newyBase !== false) {
                    // Legge til ybase i JSON map filene
                    game.properties.ybase = newyBase;
                    console.log(newyBase);
                }
                /*else if(startPoints.length > 0) {
                    console.log('startPoint');
                    game.properties.ybase = startPoints[0].y;
                }*/
                else {
                    game.properties.ybase = 2563; 
                }
                
        
                /*stateModule.load(game.properties.xbase, game.properties.ybase);*/
                //
                // to compensate for the player starting in another position you must subtract  (xbase - (xbase -320));
                console.log(game.properties.charY);
                game.properties.xcamMove = game.properties.xbase - game.properties.charX;
                game.properties.ycamMove = game.properties.ybase - game.properties.charY;
                console.log(game.properties.ybase);
                console.log(game.properties.charY);
                for(var i = 0; i < gamePieces.objects.length; i++) {
                    if(gamePieces.objects[i].src != undefined && gamePieces.objects[i].src.length > 1) {
                        gamePieces.objects[i].img = new Image(gamePieces.objects[i].width, gamePieces.objects[i].height);
                        gamePieces.objects[i].img.src = "public/images/" + gamePieces.objects[i].src;
                    }
                    // The diameters of the object is based on player starting on 320,
                    
                    
                    /* drawX/drawY is the position of object on canvas and where it is drawn.
                     * The is in the same place the only change is where it
                        is drawn*/
                    gamePieces.objects[i].drawX = (gamePieces.objects[i].x - game.properties.xcamMove);
                    gamePieces.objects[i].drawY = (gamePieces.objects[i].y - game.properties.ycamMove);
                    /*if(game.properties.scale < 1) {
                        gamePieces.objects[i].diameterTop -= 10;
                        gamePieces.objects[i].diameterDown -= 10;
                        gamePieces.objects[i].diameterRight -= 8;
                        gamePieces.objects[i].diameterLeft -= 6;
                    }*/
                
                     /*
                     *gamePieces.objects[i].diameterRight -= (xbase - (xbase - 320));
                    gamePieces.objects[i].diameterLeft -= (xbase - (xbase - 320));
                    gamePieces.objects[i].diameterTop -= (ybase - (ybase - 150));
                    gamePieces.objects[i].diameterDown -= (ybase - (ybase - 150));*/
                    if(gamePieces.objects[i].type == "building") {
                        gamePieces.buildings.push(gamePieces.objects[i]);
                    }
                    else if(gamePieces.objects[i].type == "character") {
                        gamePieces.characters.push(gamePieces.objects[i]);
                    }
                }
                gamePieces.objects.sort(function(a, b) {return a.drawY - b.drawY;});
            }
            else {
                world.src = false;
                location.reload();
                return;
            }
            let daqloon;
            for(x = 0; x < 2; x++) {
                daqloon = new createDaqloon(x, 1810 + (x * 10), 2550);
                daqloon.drawX = (daqloon.x - game.properties.xcamMove);
                daqloon.drawY = (daqloon.y - game.properties.ycamMove);
                gamePieces.daqloon.push(daqloon);
                gamePieces.daqloon[x].sprite.src = "public/images/daqloon sprite.png";
            }
            xMapMin = game.properties.xbase - 320;
            xMapMax = game.properties.xbase + 320;
            yMapMin = game.properties.ybase - 320;
            yMapMax = game.properties.ybase + 320;
            world = new Image(3201, 3201);
            world.src = "public/images/" + map + ".png";
            world.onload = function() {
                /*gamePieces.player = new newPlayer(30, 30, "#0000A0", game.properties.xbase, game.properties.ybase);*/
                gamePieces.player.x = game.properties.xbase;
                gamePieces.player.y = game.properties.ybase;
                gamePieces.player.diameterTop = gamePieces.player.y + 20;
                gamePieces.player.diameteRight = gamePieces.player.x + gamePieces.player.width;
                gamePieces.player.diameterDown = gamePieces.player.y + gamePieces.player.height;
                gamePieces.player.diameterLeft = gamePieces.player.x;
                if(newMap == false) {
                    gamePieces.player.character.src = "public/img/character sprite.png";
                    gamePieces.player.characterAttack.src = "public/images/character attack2.png";
                    gamePieces.player.character.onload = function() {
                        game.startGame();
                        console.log(map);
                        if(map == "6.4") {
                            tutorial.startTutorial();  
                        }
                    };
                }
                else {
                    game.startGame();
                }
                
            };
        });
    };
    coinAnimation = {
        index: 0,
        draw: function() {
            if(duration % 6 == 0) {
                this.index++;
                game.properties.context3.clearRect(35, 35, 18, 20);
                game.properties.context3.drawImage(picture, this.index * 18, 0, 18, 20, 35, 35, 18, 20);
                if(this.index == 5) {
                    this.index = 0;
                }
            }
        }
    };
    game.loadGame = function() {   
        game.checkDeviceType();
        calculateHunger();
        game.setCanvasDimensions();
        game.loadWorld();
        CookieTicket.checkCookieTicket('checkMeOut');
    };
    game.checkDeviceType = function() {
        // Check if there is phone
        if(window.screen.width > 830 ||
           (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == false)) {
            document.getElementById("control").style.display = "none";
            game.properties.actionText = "Press x";
            game.properties.enterText = "E - enter building";
            game.properties.enterButton = "E -";
            game.properties.personText = "W - press to talk";
            game.properties.personButton = "W -";
            game.properties.device = "pc";
        }
        else {
            game.properties.actionText = "Double tap";
            game.properties.enterText = "Tap on building to enter";
            game.properties.enterButton = "Tap on";
            game.properties.personText = "Tap on screen to talk";
            game.properties.personButton = "Tap on";
            game.properties.device = "mobile";
        }
        if(game.properties.device === "mobile") {
            document.getElementById("text_canvas").addEventListener("click", 
                function() {
                    // If the conversation_container visibility is visible, conversation is happening. Prevent other actions
                    if(conversation.checkConversation()) {
                        return false;
                    }
                    // If game is loading, return false
                    if(game.properties.loading == false) {
                        return false;
                    }
                    doubleClickDetect();
                    let clickTimer;
                    if(click == 1) {
                        let clientX = event.clientX;
                        let clientY = event.clientY;
                        /*let clientX = event.touches[0].clientX;
                        let clientY = event.touches[0].clientY;*/
                        console.log(clientX);
                        console.log(clientY);
                        clickTimer = setTimeout(function() {
                            // Single tap
                            console.log('single tap');
                            let check = game.checkBuilding(clientX, clientY);
                            if(check == false) {
                                game.checkCharacter();
                            }
                        }, 300);
                    }
                    else {
                        // Double tap
                        clearTimeout(clickTimer);
                        // If game is loading, return false
                        if(game.properties.loading == false) {
                            game.getNextMap();    
                        }
                    }
        
                });
            document.getElementById("control").addEventListener("touchmove", game.controls.move);
            document.getElementById("control").addEventListener("touchend", game.controls.endMove);   
        }
            // Set controls
            game.controls.e = game.checkBuilding;
            game.controls.w = game.checkCharacter;
            game.controls.x = game.getNextMap;
            // Prevent user from scrolling with arrow keys on site
            window.addEventListener("keydown", function(e) {
                // space and arrow keys
                if([37, 38, 39, 40, 67].indexOf(e.keyCode) > -1 || (e.keyCode == 32 &&e.target == document.body)) {
                    e.preventDefault();
                }
            }, false);
            window.addEventListener('keydown', function (e) {
                if(game.properties.gamePause == true) {
                    game.resumeGame();
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
                    case 65:
                        // A
                        gamePieces.player.attack = true;
                        break;
                    case 67:
                        // C
                        if(gamePieces.player.combat == false){ gamePieces.player.combat = true;}
                        else{ gamePieces.player.combat = false;}
                        break;
                    case 87:
                        // W
                        if(game.properties.loading == false && conversation.checkConversation() == false) {
                            game.controls.w();    
                        }
                        break;
                    case 88:
                        // X
                        console.log('x');
                        if(game.properties.loading == false && conversation.checkConversation() == false) {
                            game.controls.x();
                        }
                        break;
                    case 69:
                        // E
                        if(game.properties.loading == false && conversation.checkConversation() == false) {
                            game.controls.e();
                        }
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
    };
    game.checkEventStatus = function() {
        if(game.properties.loading == false && conversation.checkConversation() == false) {
            
        }
        else {
            
        }
    };
    game.controls = {
        left: false,
        up: false,
        right: false,
        down: false,
        e: null,
        w: null,
        x: null,
        /* one tap -> if tap on building checkBuilding()->fetchBuilding else talk to character;
         * double tap -> enter next map
         * move and endmove for mobile
         */
        move: function() {
            if(game.properties.gamePause == true) {
                game.resumeGame();
            }
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
                game.controls.endMove();
                return false;
            }
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
        },
        endMove: function() {
            let button = document.getElementById("control_button");
            button.style.top = "25%";
            button.style.left = "25%";
            game.controls.left = false;
            game.controls.up = false;
            game.controls.right = false;
            game.controls.down = false;
        }
    };
    viewport = {
        counter: 0,
        scale: game.properties.canvasWidth / 700,
        draw: function() {
            ctx = game.properties.context;
            /*ctx.clearRect( - gamePieces.player.xMovement, - gamePieces.player.yMovement, 700, 700);*/
            /*game.properties.context3.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);*/
            ctx.save();
            //Draw world and translate the image according to players movement
            /*console.log('drawGameY:' + (game.properties.ycamMove + gamePieces.player.yMovement));
            console.log('drawGameX:' + (game.properties.xcamMove + gamePieces.player.xMovement + 200));
            console.log(gamePieces.player.yMovement);*/
            if(/Safari|Chrome/i.test(navigator.userAgent)) {
                let xPos = 0;
                let sxPos = game.properties.xcamMove + gamePieces.player.xMovement;
                let yPos = 0;
                let syPos = game.properties.ycamMove + gamePieces.player.yMovement;
                if(game.properties.xcamMove + gamePieces.player.xMovement < 0) {
                    xPos = (game.properties.xcamMove + gamePieces.player.xMovement) * -1;
                    sxPos = 0;
                }
                if(game.properties.ycamMove + gamePieces.player.yMovement < 0) {
                    yPos = (game.properties.ycamMove + gamePieces.player.yMovement) * -1;
                    syPos = 0;
                }
                ctx.drawImage(world, sxPos,
                    syPos, 1024 * this.scale, 1024 * this.scale,
                    xPos, yPos,
                    1024 * this.scale, 1024 * this.scale);       
            }
            else {
                ctx.drawImage(world, game.properties.xcamMove + gamePieces.player.xMovement,
                      game.properties.ycamMove + gamePieces.player.yMovement, 1024 * this.scale, 1024 * this.scale, 0, 0,
                      1024 * this.scale, 1024 * this.scale); 
            }
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
            if(game.properties.xbase + gamePieces.player.xMovement < game.properties.charX) {
                    ctx.fillStyle = "black";
                    ctx.fillRect(0, 0, (game.properties.xbase + gamePieces.player.xMovement - game.properties.charX) * -1, 600);
            }
            
            if(game.properties.ybase + gamePieces.player.yMovement < 200) {
                ctx.fillStyle = "black";
                ctx.fillRect(0, 0, game.properties.canvasWidth, (game.properties.ybase + gamePieces.player.yMovement -
                                                                                                game.properties.charY) * -1);
            }
            if(gamePieces.player.xMovement + game.properties.xbase > 3200 - game.properties.charX - 100) {
                ctx.fillStyle = "black";
                // Safari and chrome need 2 extra pixels for the curtain to be drawn correctly
                let widthFix;
                if(/Safari|Chrome/i.test(navigator.userAgent)) {
                    widthFix = 2;
                }
                else {
                    widthFix = 0;
                }
                ctx.fillRect(game.properties.canvasWidth, 0, - (game.properties.xbase + gamePieces.player.xMovement -
                                                              (3200 - game.properties.charX - 64 - widthFix)), 600);
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
        console.log('game.startGame');
        viewport.draw();
        viewport.drawEdge();
        canvasTextHeader.setDraw(document.title);
        player = gamePieces.player;
        gamePieces.player.newPos();
        game.updateGamePiece();
        game.properties.loading = false;
        // Resume game and start the pause timer
        game.resumeGame(first = true);
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
        console.log('inactivityTime');
        var time;
        document.onkeydown = resetTimer;
        document.ontouchmove = resetTimer;
        if(pause == true) {
            resetTimer();
        }
        else if(pause == 'pause') {
            clearTimeout(time);
            pauseGame('loading');
        }
        function pauseGame(status = false) {
            if((game.controls.up !== false || game.controls.right !== false ||
                game.controls.down !== false || game.controls.left !== false) && game.properties.device == "pc" &&
                game.properties.loading !== true) {
                resetTimer();
                return false;
            }
            else if((document.getElementById("control_button").style.top !== "25%" &&
                document.getElementById("control_button").style.display != "none") && game.properties.device == "mobile" &&
                game.properties.loading !== true) {
                resetTimer();
                return false;
            }
            if(document.getElementById("conversation_container").style.visibility == "visible") {
                resetTimer();
                return false;
            }
            updateHunger();
            console.log(status);
            if(status != 'loading') {
                game.properties.textContext.font = "30px Comic Sans MS";
                game.properties.textContext.fillStyle = "pink";
                game.properties.textContext.textAlign = "center";
                game.properties.textContext.fillText("Game Paused", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
                game.properties.textContext.font = "20px Comic Sans MS";
                game.properties.textContext.fillText("Press any key to continue", game.properties.canvasWidth / 2,
                                                     game.properties.canvasHeight / 2 + 35);    
            }
            window.cancelAnimationFrame(game.properties.requestId);
            game.properties.gamePause = true;
        }
        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(pauseGame, 10000);
            // 1000 milliseconds = 1 second
        }
    };
    game.resumeGame = function(first = false) {
        if(first == true) {
            game.inactivityTime(true);
        }
        else {
            game.properties.textContext.clearRect(0, 0, 700, 700);
            game.inactivityTime();
        }
        game.properties.gamePause = false;
        game.properties.requestId = window.requestAnimationFrame(game.update);
        game.properties.startTime = new Date().getTime();
    };
    function createDaqloon(id, x, y) {
        this.id = id;
        this.index = 1;
        this.attack = 15;
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
            console.log(this.health);
        };
        this.drawHealthBar = function(x, y) {
            let remainingHealth = 100 - this.health;
            if(remainingHealth < 0 ) {
                this.health = 0;
            }
            game.properties.context3.fillStyle = "blue";
            game.properties.context3.fillRect(x + 5, y - 20, 30, 10);
            if(this.health !== 100) {
                console.log(this.health);
                game.properties.context3.fillStyle = "red";
                game.properties.context3.fillRect(x + 35, y - 20, - 0.35 * (100 - this.health), 10);
            }
        
        };
        this.checkNearBy = function() {
            console.log(gamePieces.daqloon.findIndex(this.findOtherDaqloons));
        };
        this.draw = function() {
            if(Math.abs(this.x - gamePieces.player.xpos) < 20 && Math.abs(this.y - gamePieces.player.ypos) < 20 &&
               this.attack == false) {
                this.attack = true;
                gamePieces.player.takeDamage();
                
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
            let nearbyIndex = this.findOtherDaqloons();
            let nearbyX = nearbyIndex[0];
            let nearbyY = nearbyIndex[1];
            
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
    function gameObstacle (name, x, y, width, height, style) {
        this.name = name;
        this.x = x;
        this.y = y;
        this.width = width / viewport.scale;
        this.height = height / viewport.scale;
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
            let visibleObjects = gamePieces.objects.filter(checkPos);
            // buildingMatch variable is to check if there is at building that the player can enter
            let buildingMatch = false;
            let personMatch = false;
            for(var i = 0; i < visibleObjects.length; i++) {
                /*if(object.y > yMapMax + 400 || object.y < yMapMin - 400  || object.x > xMapMax + 400 || object.x < xMapMin - 400) {
                    continue;
                }*/
                if(draw == true) {
                    ctx = game.properties.context3;
                    ctx.fillStyle = "red";
                    ctx.fillRect(visibleObjects[i].drawX - (player.xMovement / viewport.scale),
                                 visibleObjects[i].drawY - (player.yMovement / viewport.scale),
                                 visibleObjects[i].width, visibleObjects[i].height);
                    ctx.font = "10px Comic Sans MS";
                    ctx.fillStyle = "white";
                    ctx.fillText(i + ' | ' + visibleObjects[i].id,
                                 visibleObjects[i].drawX - (player.xMovement / viewport.scale) +
                                 (visibleObjects[i].width / 2),
                                 visibleObjects[i].drawY + (visibleObjects[i].height / 2) -
                                 (player.yMovement / viewport.scale));
                }
                if(visibleObjects[i].visible != true && visibleObjects[i].type != "figure" && visibleObjects[i].src.length > 1) {
                    let drawContext;
                    // If building is behind player, then draw on the first canvas instead of the third
                    if(visibleObjects[i].diameterTop < player.ypos) {
                        drawContext = ctx;
                    }
                    else{
                        drawContext = game.properties.context3;  
                    }
                    drawContext.imageSmoothingEnabled = false;
                    /*console.log(visibleObjects[i]);*/
                    drawContext.drawImage(visibleObjects[i].img,
                                                        visibleObjects[i].drawX - (player.xMovement * viewport.scale),
                                  visibleObjects[i].drawY - (player.yMovement * viewport.scale));
                    // Check if person is near any buildings
                    
                    if(player.ypos > visibleObjects[i].diameterTop && player.ypos < visibleObjects[i].diameterDown &&
                        player.xpos > visibleObjects[i].diameterLeft && player.xpos < visibleObjects[i].diameterRight &&
                        Math.abs(player.ypos - visibleObjects[i].diameterDown) < 32 &&
                                visibleObjects[i].type === "building") {
                        buildingMatch = true;    
                    }
                    else if(player.ypos - 5 > visibleObjects[i].diameterTop && 
                        player.xpos > visibleObjects[i].diameterLeft - 5 && player.xpos < visibleObjects[i].diameterRight + 5 &&
                        Math.abs(player.ypos - visibleObjects[i].diameterDown) < 32 && visibleObjects[i].type === "character") {
                        personMatch = true;
                    }
                }   
            }
            if(buildingMatch === true) {
                document.getElementById("control_text").querySelectorAll("p")[0].innerHTML = game.properties.enterText;
            }
            else {
                document.getElementById("control_text").querySelectorAll("p")[0].innerHTML = game.properties.enterButton;
            }
            if(personMatch === true) {
                document.getElementById("control_text").querySelectorAll("p")[1].innerHTML = game.properties.personText;
            }
            else {
                document.getElementById("control_text").querySelectorAll("p")[1].innerHTML = game.properties.personButton;
            }
    };
    game.checkBuilding = function(mouseX = false, mouseY = false) {
        if(inBuilding != true && game.properties.device == "pc") {
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
        else if(inBuilding != true && game.properties.device == "mobile") {
            console.log('check building');
            let element = document.getElementById("text_canvas");
            let ElementPos = element.getBoundingClientRect();
            // Remove elementPos of the canvas so that 0.0 is in up-left corner
            mouseY = mouseY - ElementPos.top;
            mouseX = mouseX - ElementPos.left;
            let x = mouseX + (gamePieces.player.xpos - (game.properties.canvasWidth / 2) + 32);
            let y = mouseY + (gamePieces.player.ypos - (game.properties.canvasHeight / 2));
            console.log(x);
            console.log(y);
            let result = false;
            for(i = 0; i < gamePieces.buildings.length; i++) {
                object = gamePieces.buildings[i];
                if(y > object.diameterTop && y < object.diameterDown &&
                x > object.diameterLeft && x < object.diameterRight &&
                Math.abs(gamePieces.player.ypos - object.diameterDown) < 32)
                {
                    result = true;
                    game.fetchBuilding(object.src.split(".png")[0]);
                    break;
                }
            }
            return result;
        }
    };
    game.checkCharacter = function() {
        for(var i = 0; i < gamePieces.characters.length; i++) {
            let object = gamePieces.characters[i];
                if(player.ypos - 5 > object.diameterTop && 
                   player.xpos > object.diameterLeft - 5 && player.xpos < object.diameterRight + 5 &&
                   Math.abs(player.ypos - object.diameterDown) < 32) {
                        conversation.loadConversation(object.src.split(".png")[0]);
                        game.inactivityTime('pause');
                        break;
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
           if(Math.abs(player.diameterDown - gamePieces.objects[i].diameterTop) <= 2 &&
              player.diameterRight >= gamePieces.objects[i].diameterLeft && 
              player.diameterLeft <= gamePieces.objects[i].diameterRight) {
                player.down = "blocked";
           }
           if(Math.abs(player.diameterRight - gamePieces.objects[i].diameterLeft) <= 2 &&
              player.diameterTop <= gamePieces.objects[i].diameterDown &&
              player.diameterDown >= gamePieces.objects[i].diameterTop) {
                player.right = "blocked";
           }
           if(Math.abs(player.diameterTop - gamePieces.objects[i].diameterDown) <= 2 &&
              player.diameterRight >= gamePieces.objects[i].diameterLeft &&
              player.diameterLeft <= gamePieces.objects[i].diameterRight) {
                player.top = "blocked";
           }
           if(Math.abs(player.diameterLeft - gamePieces.objects[i].diameterRight) <= 2 &&
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
            gamePieces.player.xMovement += (gamePieces.player.movementSpeed * game.properties.delta) * player.speedX;
            gamePieces.player.yMovement += (gamePieces.player.movementSpeed * game.properties.delta) * player.speedY;
    };
    game.update = function (timestamp) {
        
        delta = game.properties.delta = (timestamp - game.properties.timestamp) / 1000;
        game.properties.timestamp = timestamp;
        coinAnimation.draw();
        if(duration % 2 === 0) {
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
            
            if(duration % 2 == 0) {
                game.properties.context3.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);   
            }
            for(var i = 0; i < gamePieces.daqloon.length; i++) {
                gamePieces.daqloon[i].draw();
            }
            if((player.speedX != 0 || player.speedY != 0) && inBuilding == false && conversation.index == null) {
                eventHandler.checkEvent();
                game.calculateDistance();
                viewport.draw();
                viewport.drawEdge();
                player.newPos();
                game.updateGamePiece();
            }
            else if(gamePieces.player.animationEnd != true) {
                player.newPos(false);
            }
        }
        duration++;
        game.properties.requestId = window.requestAnimationFrame(game.update);
    };
    eventHandler = {
        events: [],
        eventOngoing: false,
        checkEvent: function() {
            if(this.eventOngoing == true) {
                return;
            }
            for(let i = 0; i < this.events.length; i++) {
                if(gamePieces.player.xpos >= this.events[i].xMin && gamePieces.player.xpos <= this.events[i].xMax &&
                   gamePieces.player.ypos <= this.events[i].yMax && gamePieces.player.ypos >= this.events[i].yMin) {
                    loadEvent(this.events[i].name);
                    this.eventOngoing = true;
                    break;
                }
            }
            function loadEvent(event) {
                let data = "event=" + event; 
                ajaxJS(data, function(response) {
                    if(response[0] !== false) {
                        let responseText = JSON.parse(response[1]);
                        if(responseText.draw != false) {
                            let img = new Image(32, 32);
                            let img2;
                            responseText.draw.forEach(function(element) {
                                img2 = img.cloneNode();
                                img2.onload = function() {
                                    /*game.properties.context3.drawImage(img, 0, 0, 32, 32); */
                                    game.properties.context3.drawImage(img2, game.properties.charX + element.x,
                                                              game.properties.charY + element.y);   
                                };
                                img2.src = "public/images/" + element.src;
                            });
                        }
                        conversation.loadConversation(responseText.con);
                        this.currentEvent = true;
                    }
                }, true, 'handler_e');
            }
        }
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
            gamePieces.player.xMovement = 0;
            gamePieces.player.yMovement = 0;
            game.loadWorld(true, newxBase, newyBase, "changeMap", {"new_x": newX, "new_y": newY});
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
            viewport.draw();
            viewport.drawEdge();
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
    window.addEventListener("load", game.loadGame);