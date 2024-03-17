import { GamePieces } from './gamePieces';
import { Game } from '../advclient';
import { CanvasSprite } from '../types/CanvasSprite';
import { Character } from '../gamepieces/Character';
import { Building } from '../gamepieces/Building';
import { NonDrawingTypes } from '../gamepieces/NonDrawingTypes';

interface IViewport {
    counter: number;
    scale: number;
    zoom: number;
    worldImage: HTMLImageElement;
    width: number;
    height: number;
    top: number;
    left: number;
    offsetX: number;
    offsetY: number;
    playerCanvasX: number;
    playerCanvasY: number;
    elements: {
        background: HTMLCanvasElement;
        player: HTMLCanvasElement;
        sprite: HTMLCanvasElement;
        frontObjects: HTMLCanvasElement;
        text: HTMLCanvasElement;
    };
    layer: {
        background: CanvasRenderingContext2D;
        player: CanvasRenderingContext2D;
        sprite: CanvasRenderingContext2D;
        frontObjects: CanvasRenderingContext2D;
        text: CanvasRenderingContext2D;
    };
    setInitalDimensions();
    setup(layers: HTMLCanvasElement[]);
    adjustViewport(xbase: number, ybase: number, src: string);
    init: () => void;
    drawBackground();
    drawPlayer();
    adjustViewport(xbase, ybase, src);
    resetObjectLayer();
    drawObject();
    resetSpriteLayer();
    drawSprite();
    drawText();
    resetTextLayer();
    drawDaqloonHealthbar();
    checkViewportGamePieces();
}

interface layers {
    background: HTMLCanvasElement;
    player: HTMLCanvasElement;
    sprite: HTMLCanvasElement;
    frontObjects: HTMLCanvasElement;
    text: HTMLCanvasElement;
    hud: HTMLCanvasElement;
}

interface viewportDrawObject {
    img: HTMLImageElement;
    spriteX: number;
    spriteY: number;
    sWidth: number;
    sHeight: number;
    width: number;
    height: number;
}

// TODO: Convert to class
export const viewport = {
    counter: 0,
    scale: 1,
    zoom: 1.2,
    worldImage: new Image(3200, 3200),
    width: 0,
    height: 0,
    top: 0,
    left: 0,
    offsetX: 0,
    offsetY: 0,
    playerCanvasX: 0,
    playerCanvasY: 0,
    elements: {
        background: null as HTMLCanvasElement,
        player: null as HTMLCanvasElement,
        sprite: null as HTMLCanvasElement,
        frontObjects: null as HTMLCanvasElement,
        text: null as HTMLCanvasElement,
        hud: null as HTMLCanvasElement,
    },
    layer: {
        background: null as CanvasRenderingContext2D,
        player: null as CanvasRenderingContext2D,
        sprite: null as CanvasRenderingContext2D,
        frontObjects: null as CanvasRenderingContext2D,
        text: null as CanvasRenderingContext2D,
        hud: null as CanvasRenderingContext2D,
    },
    setInitalDimensions() {
        const screen = window.screen;
        let newWidth;
        if (screen.width < 800) {
            newWidth =
                document.getElementsByTagName('section')[0].offsetWidth * 0.97;
        } else {
            newWidth =
                document.getElementsByTagName('section')[0].offsetWidth * 0.68;
        }
        const canvasContainer = document.getElementById('game-screen');
        if (canvasContainer) {
            this.left = canvasContainer.offsetLeft;
            this.top = canvasContainer.offsetTop;
        }
        let newHeight;
        // If the device is mobile check for the shortest dimension of height and width to compensate for already rotated devices
        if (Game.properties.device == 'mobile') {
            newHeight =
                screen.width < screen.height
                    ? screen.width - 20
                    : screen.height - 20;
        } else {
            newHeight = screen.height - 20;
        }
        if (newHeight > 600) {
            newHeight = 550;
        }
        this.width = newWidth;
        this.height = newHeight;
    },
    setup(layers: layers) {
        // Set layers and elements
        this.setInitalDimensions();

        this.layer.background = layers.background.getContext('2d');
        this.layer.background.fillStyle = 'black';
        this.elements.background = layers.background;

        this.layer.player = layers.player.getContext('2d');
        this.elements.player = layers.player;
        this.layer.sprite = layers.sprite.getContext('2d');
        this.elements.sprite = layers.sprite;
        this.layer.frontObjects = layers.frontObjects.getContext('2d');
        this.elements.frontObjects = layers.frontObjects;
        this.layer.text = layers.text.getContext('2d');
        this.elements.text = layers.text;
        this.layer.hud = layers.hud.getContext('2d');
        this.elements.hud = layers.hud;

        this.elements.background.width = this.width;
        this.elements.background.height = this.height;
        this.elements.background.style.left = this.left + 'px';

        this.elements.player.width = this.width;
        this.elements.player.height = this.height;
        this.elements.player.style.left = this.left + 'px';
        this.elements.player.style.zIndex = '2';

        this.elements.sprite.width = this.width;
        this.elements.sprite.height = this.height;
        this.elements.sprite.style.left = this.left + 'px';
        this.elements.sprite.style.zIndex = '3';

        this.elements.frontObjects.width = this.width;
        this.elements.frontObjects.height = this.height;
        this.elements.frontObjects.style.left = this.left + 'px';
        this.elements.frontObjects.style.zIndex = '4';

        this.elements.text.width = this.width;
        this.elements.text.height = this.height;
        this.elements.text.style.left = this.left + 'px';
        this.elements.text.style.zIndex = '5';

        this.elements.hud.width = this.width;
        this.elements.hud.height = this.height;
        this.elements.hud.style.left = this.left + 'px';
        this.elements.hud.style.zIndex = '6';

        document.getElementById('canvas-border').style.width =
            this.width + 'px';
        document.getElementById('canvas-border').style.height =
            this.height + 2 + 'px';

        this.layer.background.scale(this.zoom, this.zoom);
        this.layer.player.scale(this.zoom, this.zoom);
        this.layer.sprite.scale(this.zoom, this.zoom);
        this.layer.frontObjects.scale(this.zoom, this.zoom);
        this.layer.hud.scale(this.zoom, this.zoom);
    },
    adjustViewport(xbase, ybase, src) {
        this.playerCanvasX = Math.floor(this.width / 2 - 45);
        this.playerCanvasY = Math.floor(this.height / 2 - 45);
        this.offsetX = xbase - this.playerCanvasX;
        this.offsetY = ybase - this.playerCanvasY;
        this.worldImage.src = src;
    },
    init() {
        this.drawBackground(0, 0);
        this.checkViewportGamePieces(true);
    },
    drawBackground(xMovement: number, yMovement: number) {
        this.layer.background.fillRect(0, 0, this.width, this.height);
        this.layer.background.drawImage(
            this.worldImage,
            this.offsetX + xMovement,
            this.offsetY + yMovement,
            this.width,
            this.height,
            0,
            0,
            this.width,
            this.height,
        );
    },
    drawPlayer(canvasSprite: CanvasSprite) {
        this.layer.player.drawImage(
            canvasSprite.img,
            canvasSprite.spriteX,
            canvasSprite.spriteY,
            canvasSprite.sWidth,
            canvasSprite.sHeight,
            this.playerCanvasX,
            this.playerCanvasY,
            canvasSprite.width,
            canvasSprite.height,
        );
    },
    resetPlayerLayer() {
        this.layer.player.clearRect(0, 0, this.width, this.height);
    },
    resetObjectLayer() {
        this.layer.frontObjects.clearRect(0, 0, this.width, this.height);
    },
    drawObject(layer, img, spriteX, spriteY, width, height) {
        if (!['background', 'frontObjects'].includes(layer)) return false;
        if (layer === 'background') {
            this.layer.background.drawImage(
                img,
                spriteX,
                spriteY,
                width,
                height,
            );
        } else if (layer === 'frontObjects') {
            this.layer.frontObjects.drawImage(
                img,
                spriteX,
                spriteY,
                width,
                height,
            );
        }
    },
    resetSpriteLayer() {
        this.layer.sprite.clearRect(0, 0, this.width, this.height);
    },
    drawSprite(img, spriteX, spriteY, sWidth, sHeight, x, y, width, height) {
        this.layer.sprite.drawImage(
            img,
            spriteX,
            spriteY,
            sWidth,
            sHeight,
            x,
            y,
            width,
            height,
        );
    },
    drawText(font, fillStyle, text, x, y, textAlign = false) {
        if (textAlign) {
            this.layer.text.textAlign = 'center';
        } else {
            this.layer.text.textAlign = 'start';
        }
        this.layer.text.font = font;
        this.layer.text.fillStyle = fillStyle;
        this.layer.text.fillText(text, x, y);
    },
    resetTextLayer() {
        this.layer.text.clearRect(0, 0, this.width, this.height);
    },
    drawAttackCoolDown(cooldown) {
        this.layer.player.fillStyle = 'orange';
        this.layer.player.fillRect(10, 60, 100 - (100 - cooldown), 10);
    },
    drawBlockCoolDown(cooldown) {
        this.layer.player.fillStyle = 'red';
        this.layer.player.fillRect(10, 90, 100 - (100 - cooldown), 10);
    },
    drawDaqloonHealthbar(fillstyle, x, y, width, height) {
        this.layer.sprite.fillStyle = fillstyle;
        this.layer.sprite.fillRect(x, y, width, height);
    },
    checkViewportGamePieces(first = false) {
        // If player has moved a certain amount of pixels update object that will be drawn

        if (
            Math.abs(GamePieces.player.xTracker) > 100 ||
            Math.abs(GamePieces.player.yTracker) > 100 ||
            first == true
        ) {
            GamePieces.nearObjects = GamePieces.objects.filter(object => {
                return (
                    (Math.abs(object.diameterRight - GamePieces.player.xpos) <=
                        this.width + 50 ||
                        Math.abs(
                            object.diameterLeft - GamePieces.player.xpos,
                        ) <=
                            this.width + 50) &&
                    (Math.abs(object.diameterUp - GamePieces.player.ypos) <=
                        this.height + 50 ||
                        Math.abs(
                            object.diameterDown - GamePieces.player.ypos,
                        ) <=
                            this.height + 50)
                );
            });

            GamePieces.nearBuildings = [];
            GamePieces.nearCharacters = [];
            GamePieces.visibleObjects = [];

            GamePieces.nearObjects.forEach(object => {
                if (
                    object instanceof Character &&
                    object.type === 'character'
                ) {
                    GamePieces.nearCharacters.push(object);
                } else if (
                    object instanceof Building &&
                    object.type === 'building'
                ) {
                    GamePieces.nearBuildings.push(object);
                }

                if (!NonDrawingTypes.includes(object.type) && object.visible) {
                    GamePieces.visibleObjects.push(object);
                }
            });

            GamePieces.player.xTracker = 0;
            GamePieces.player.yTracker = 0;
        }
    },
};

export default viewport;
