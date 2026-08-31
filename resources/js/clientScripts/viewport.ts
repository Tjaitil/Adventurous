import type { CanvasSprite } from '../types/CanvasSprite';
import { AssetPaths } from './ImagePath';

type device = 'mobile' | 'pc';

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
    world: HTMLCanvasElement;
    text: HTMLCanvasElement;
  };
  layer: {
    world: CanvasRenderingContext2D;
    text: CanvasRenderingContext2D;
  };
  setInitalDimensions(device: device);
  setup(layers: layers, device: device);
  adjustViewport(xbase: number, ybase: number, src: string);
  drawBackground();
  drawPlayer();
  adjustViewport(xbase, ybase, src);
  drawWorldImage();
  drawWorldSprite();
  drawText();
  resetTextLayer();
  resetHudLayer();
  drawDaqloonHealthbar();
}

interface layers {
  world: HTMLCanvasElement;
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
  height: 850,
  top: 0,
  left: 0,
  offsetX: 0,
  offsetY: 0,
  playerCanvasX: 0,
  playerCanvasY: 0,
  elements: {
    world: null as HTMLCanvasElement,
    text: null as HTMLCanvasElement,
    hud: null as HTMLCanvasElement,
  },
  layer: {
    world: null as CanvasRenderingContext2D,
    text: null as CanvasRenderingContext2D,
    hud: null as CanvasRenderingContext2D,
  },
  setInitalDimensions(device: device) {
    const screen = window.screen;
    let newWidth;
    if (screen.width < 800) {
      newWidth = document.getElementsByTagName('section')[0].offsetWidth * 0.97;
    }

    const gameCanvas = document.getElementById('game_canvas');
    if (!(gameCanvas instanceof HTMLCanvasElement)) {
      throw new Error('game_canvas is not an HTMLCanvasElement');
    }
    this.left = 6;

    const canvasContainer = document.getElementById('game-screen');
    if (canvasContainer) {
      this.top = canvasContainer.offsetTop;
    }
    newWidth = gameCanvas.parentElement?.offsetWidth - 12;
    let newHeight;
    // If the device is mobile check for the shortest dimension of height and width to compensate for already rotated devices
    if (device == 'mobile') {
      newHeight =
        screen.width < screen.height ? screen.width - 20 : screen.height - 20;
    } else {
      newHeight = screen.height - 20;
    }
    if (newHeight > 600) {
      newHeight = 550;
    }
    this.width = newWidth;
    this.height = newHeight;
  },
  setup(layers: layers, device: device) {
    // Set layers and elements
    this.setInitalDimensions(device);

    this.layer.world = layers.world.getContext('2d');
    this.layer.world.fillStyle = 'black';
    this.elements.world = layers.world;

    this.layer.text = layers.text.getContext('2d');
    this.elements.text = layers.text;
    this.layer.hud = layers.hud.getContext('2d');
    this.elements.hud = layers.hud;

    const gameScreenContainer = document.getElementById(
      'game-screen-container',
    );
    if (gameScreenContainer) {
      gameScreenContainer.style.width = this.width + 'px';
      gameScreenContainer.style.height = this.height + 'px';
    }

    this.elements.world.width = this.width;
    this.elements.world.height = this.height;
    this.elements.world.style.left = this.left + 'px';

    this.elements.text.width = this.width;
    this.elements.text.height = this.height;
    this.elements.text.style.left = this.left + 'px';
    this.elements.text.style.zIndex = '5';

    this.elements.hud.width = this.width;
    this.elements.hud.height = this.height;
    this.elements.hud.style.left = this.left + 'px';
    this.elements.hud.style.zIndex = '6';

    document.getElementById('canvas-border').style.width =
      this.width + 16 + 'px';
    document.getElementById('canvas-border').style.height =
      this.height + 16 + 'px';

    this.layer.world.scale(this.zoom, this.zoom);
    this.layer.hud.scale(this.zoom, this.zoom);
  },
  setImageWorldSrc(currentMap: string) {
    this.worldImage.src = AssetPaths.getImagePath(currentMap + '.png');
  },
  adjustViewport(xbase, ybase) {
    this.playerCanvasX = Math.floor(this.width / 2 - 45);
    this.playerCanvasY = Math.floor(this.height / 2 - 45);
    this.offsetX = xbase - this.playerCanvasX;
    this.offsetY = ybase - this.playerCanvasY;
  },
  // Must remain the first draw call of every frame — it is the sole
  // full-canvas clear for the merged world canvas.
  drawBackground(xMovement: number, yMovement: number) {
    // fillStyle must be re-set every frame: the merged world context is shared
    // with drawDaqloonHealthbar(), which leaves fillStyle as 'red'. Without this
    // the full-canvas fillRect below paints the map edges red instead of black.
    this.layer.world.fillStyle = 'black';
    this.layer.world.fillRect(0, 0, this.width, this.height);
    this.layer.world.drawImage(
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
  // canvasSprite.spriteX/spriteY are source-rect coordinates (matches
  // drawWorldSprite's meaning, not drawWorldImage's destination coordinates).
  drawPlayer(canvasSprite: CanvasSprite) {
    this.drawWorldSprite(
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
  // spriteX/spriteY here are destination coordinates (5-arg drawImage form) —
  // NOT source-rect coordinates like drawWorldSprite's spriteX/spriteY.
  drawWorldImage(img, spriteX, spriteY, width, height) {
    try {
      if (!img.src.includes('/.png')) {
        this.layer.world.drawImage(img, spriteX, spriteY, width, height);
      }
    } catch (error: unknown) {
      throw new Error(
        `Error drawing object ${img.src} on the world layer: ${error instanceof Error ? error.message : String(error)}`,
      );
    }
  },
  // spriteX/spriteY here are source-rect coordinates (9-arg drawImage form).
  drawWorldSprite(img, spriteX, spriteY, sWidth, sHeight, x, y, width, height) {
    try {
      this.layer.world.drawImage(
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
    } catch (error: unknown) {
      throw new Error(
        `Error drawing sprite ${img.src} on the world layer: ${error instanceof Error ? error.message : String(error)}`,
      );
    }
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
  resetHudLayer() {
    this.layer.hud.clearRect(0, 0, this.width, this.height);
  },
  drawAttackCoolDown(cooldown) {
    this.layer.hud.fillStyle = 'orange';
    this.layer.hud.fillRect(10, 60, 100 - (100 - cooldown), 10);
  },
  drawBlockCoolDown(cooldown) {
    this.layer.hud.fillStyle = 'red';
    this.layer.hud.fillRect(10, 90, 100 - (100 - cooldown), 10);
  },
  drawDaqloonHealthbar(fillstyle, x, y, width, height) {
    this.layer.world.fillStyle = fillstyle;
    this.layer.world.fillRect(x, y, width, height);
  },
};

export default viewport;
