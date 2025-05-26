import { jsUcfirst } from '../utilities/uppercase';
import viewport from './viewport';

export const canvasTextHeader = {
  opacity: 0.0,
  intervalID: null,
  text: null,
  transition: false,
  timer: 0,
  setDraw(text: string, timer: number) {
    if (text.length > 0) {
      return false;
    }
    this.text = text;
    this.intervalID = setInterval(() => this.adjustOpacity(), 75);
    setTimeout(() => this.unsetDraw(), timer * 1000);
  },
  adjustOpacity() {
    if (this.opacity <= 0.4) {
      this.opacity += 0.1;
      this.draw();
    } else {
      clearInterval(canvasTextHeader.intervalID);
    }
  },
  draw() {
    viewport.layer.text.clearRect(0, 0, viewport.width, viewport.height);
    const contentY = viewport.height * 0.15;
    viewport.layer.text.fillStyle = 'rgba(38, 38, 38,' + this.opacity + ')';
    viewport.layer.text.fillRect(0, contentY, viewport.width, 200);
    viewport.layer.text.fillStyle = 'rgba(255,255,255' + this.opacity + ')';
    viewport.layer.text.font = '35px Times New Roman';
    viewport.layer.text.textAlign = 'center';
    viewport.layer.text.fillText(
      jsUcfirst(this.text),
      viewport.width * 0.5 - 20,
      contentY + 100,
    );
  },
  unsetDraw() {
    viewport.layer.text.clearRect(0, 0, viewport.width, viewport.height);
    this.opacity = 0;
  },
};
export const loadingCanvas = {
  opacity: 1,
  intervalID: null,
  lastCall: null,
  duration: 0,
  text: null,
  curtainEffect: 'open',
  animationDuration: 0,
  loadingAnimationTracker: {
    resolve: undefined,
    start(state) {
      if (state === 'loading') {
        loadingCanvas.loadingScreen();
      } else if (state === 'close') {
        loadingCanvas.curtainEffect = 'close';
        loadingCanvas.opacity = 0.01;
      } else if (state === 'open') {
        loadingCanvas.curtainEffect = 'open';
        loadingCanvas.opacity = 0.99;
      }
      return new Promise((resolve, reject) => {
        this.resolve = resolve;
        loadingCanvas.intervalID = window.requestAnimationFrame(() =>
          loadingCanvas.drawCurtain(),
        );
      });
    },
    finish() {
      if (loadingCanvas.curtainEffect === 'close')
        loadingCanvas.loadingScreen();
      this.resolve();
    },
  },
  loadingScreen() {
    viewport.layer.text.fillStyle = 'black';
    viewport.layer.text.fillRect(0, 0, viewport.width, viewport.height);
    viewport.layer.text.font = '30px Comic Sans MS';
    viewport.layer.text.fillStyle = 'white';
    viewport.layer.text.textAlign = 'center';
    viewport.layer.text.fillText(
      'Loading ...',
      viewport.width / 2,
      viewport.height / 2,
    );
  },
  drawCurtain() {
    if (
      (this.opacity < 0 && this.curtainEffect === 'open') ||
      (this.opacity > 1 && this.curtainEffect === 'close')
    ) {
      this.loadingAnimationTracker.finish();
      return;
    } else if (this.curtainEffect === 'open') {
      // Ease-in effect on loading
      if (this.opacity > 0.8) {
        this.opacity -= 0.005;
      } else if (this.opacity > 0.4) {
        this.opacity -= 0.015;
      } else {
        this.opacity -= 0.02;
      }
    } else {
      // Ease-in effect on loading
      if (this.opacity < 0.2) {
        this.opacity += 0.005;
      } else if (this.opacity < 0.6) {
        this.opacity += 0.015;
      } else {
        this.opacity += 0.02;
      }
    }
    viewport.layer.text.fillStyle = 'rgba(0, 0, 0, ' + this.opacity + ')';
    viewport.layer.text.clearRect(0, 0, viewport.width, viewport.height);
    viewport.layer.text.fillRect(0, 0, viewport.width, viewport.height);
    this.intervalID = window.requestAnimationFrame(() => this.drawCurtain());
  },
};
