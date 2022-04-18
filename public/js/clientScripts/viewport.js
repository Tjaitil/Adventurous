const viewport = {
    counter: 0,
    // scale: game.properties.canvasWidth / 700,
    scale: 1,
    zoom: 1.2,
    worldImage: new Image(3200, 3200),
    width: 0,
    height: 0,
    left: 0,
    offsetX: 0,
    offsetY: 0,
    elements: {
        background: null,
        player: null,
        daqloon: null,
        frontObjects: null,
        text: null,  
    },
    layer: {
        background: null,
        player: null,
        daqloon: null,
        frontObjects: null,
        text: null,
    },
    setInitalDimensions() {
        let screen = window.screen;
        let newWidth;
        if (screen.width < 800) {
            newWidth = document.getElementsByTagName("section")[0].offsetWidth * 0.97;
        }
        else {
            newWidth = document.getElementsByTagName("section")[0].offsetWidth * 0.68;
        }
        let canvasContainer = document.getElementById("game-screen");
        if(canvasContainer) {
            this.left = canvasContainer.offsetLeft;
        }
        let newHeight;
        // If the device is mobile check for the shortest dimension of height and width to compensate for already rotated devices
        if (game.properties.device == "mobile") {
            newHeight = (screen.width < screen.height) ? screen.width - 20 : screen.height - 20;
        }
        else {
            newHeight = screen.height - 20;
        }
        if (newHeight > 600) {
            newHeight = 550;
        }
        game.properties.canvasWidth = this.width = newWidth;
        game.properties.canvasHeight = this.height = newHeight;
    },
    setup(layers) {
        // Set layers and elements
        this.setInitalDimensions();
        this.layer.background = layers[0].getContext("2d");
        this.layer.background.fillStyle = "black";
        this.elements.background = layers[0];
        this.layer.player = layers[1].getContext("2d");
        this.elements.player = layers[1];
        this.layer.daqloon = layers[2].getContext("2d");
        this.elements.daqloon = layers[2];
        this.layer.frontObjects = layers[3].getContext("2d");
        this.elements.frontObjects = layers[3];
        this.layer.text = layers[4].getContext("2d");
        this.elements.text = layers[4];

        this.elements.background.width = this.width;
        this.elements.background.height = this.height;
        this.elements.background.style.left = this.left + "px";
        this.elements.player.width = this.width;
        this.elements.player.height = this.height;
        this.elements.player.style.left = this.left + "px";
        this.elements.daqloon.width = this.width;
        this.elements.daqloon.height = this.height;
        this.elements.daqloon.style.left = this.left + "px";
        this.elements.frontObjects.width = this.width;
        this.elements.frontObjects.height = this.height;
        this.elements.frontObjects.style.left = this.left + "px";

        this.layer.background.scale(this.zoom, this.zoom);
        this.layer.player.scale(this.zoom, this.zoom);
        this.layer.daqloon.scale(this.zoom, this.zoom);
        this.layer.frontObjects.scale(this.zoom, this.zoom);
    },
    adjustViewport(xbase, ybase, src) {
        this.charX = (Math.floor((this.width / 2) - 45));
        this.charY = (Math.floor((this.height / 2) - 45));
        this.offsetX = xbase - this.charX; 
        this.offsetY = ybase - this.charY;
        this.worldImage.src = src;
    },
    init() {
        this.drawBackground();
        this.drawEdge();
        this.checkViewportGamePieces(true);
    },
    drawBackground(xMovement, yMovement) {
        this.layer.background.fillRect(0, 0, this.width, this.height);
        this.layer.background.drawImage(this.worldImage, this.offsetX + xMovement,
            this.offsetY + yMovement, 
            this.width, this.height, 0, 0,
            this.width, this.height);
    },
    drawPlayer(img, spriteX, spriteY, sWidth, sHeight, width, height) {
        this.layer.player.clearRect(0, 0, 700, 700);
        this.layer.player.drawImage(img, spriteX, spriteY,
            sWidth, sHeight, this.charX, this.charY, width, height);
    },
    drawEdge() {
        if (gamePieces.player.ypos > 3160 ||
            (gamePieces.player.ypos < 3100 && gamePieces.player.ypos < 10) ||
            gamePieces.player.xpos > 3160 ||
            (gamePieces.player.xpos < 3100 && gamePieces.player.xpos < 10)) {
            game.getNextMap();
        }
    },
    checkViewportGamePieces(first = true) {
        // If player has moved a certain amount of pixels update object that will be drawn
        if (Math.abs(gamePieces.player.xTracker) > 100 ||
        Math.abs(gamePieces.player.yTracker) > 100 ||
        first == true) {
            gamePieces.nearObjects = gamePieces.objects.filter((object) => {
                return ((Math.abs(object.diameterRight - gamePieces.player.xpos) <= this.width + 50 ||
                Math.abs(object.diameterLeft - gamePieces.player.xpos) <= this.width + 50) &&
                (Math.abs(object.diameterUp - gamePieces.player.ypos) <= this.height + 50 ||
                Math.abs(object.diameterDown - gamePieces.player.ypos) <= this.height + 50));
            });
            // Visible object is only the objects that are visible
            gamePieces.visibleObjects = gamePieces.nearObjects.filter(object => {
                return (object.type !== 'figure');
            })
            gamePieces.player.xTracker = 0;
            gamePieces.player.yTracker = 0;
        }
    }
};
