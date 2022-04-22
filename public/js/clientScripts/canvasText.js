const canvasTextHeader = {
    opacity: 0.0,
    intervalID: null,
    text: null,
    transition: false,
    timer: 0,
    setDraw(text, timer) {
        if(!text.length > 0) {
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
        }
        else {
            clearInterval(canvasTextHeader.intervalID);
        }
    },
    draw() {
        viewport.layer.text.clearRect(0, 0, viewport.width, viewport.height);
        let contentY = game.properties.canvasHeight * 0.15;
        viewport.layer.text.fillStyle = "rgba(38, 38, 38," + this.opacity + ")";
        viewport.layer.text.fillRect(0, contentY, viewport.width, 200);
        viewport.layer.text.fillStyle = "rgba(255,255,255" + this.opacity + ")";
        viewport.layer.text.font = "35px Times New Roman";
        viewport.layer.text.textAlign = "center";
        viewport.layer.text.fillText(jsUcfirst(this.text), viewport.width * 0.50 - 20, contentY + 100);
    },
    unsetDraw() {
        viewport.layer.text.clearRect(0, 0, viewport.width, viewport.height);
        this.opacity = 0;
    }
};
const loadingCanvas = {
    opacity: 1,
    intervalID: null,
    lastCall: null,
    duration: 0,
    text: null,
    curtainEffect: 'open',
    animationDuration: 0,
    set(state) {
        if(state === 'loading') {
            this.loadingScreen();
        } else if(state === 'close') {
            this.curtainEffect = 'close'
            this.opacity = 0;
            this.intervalID = window.requestAnimationFrame(() => this.drawCurtain());

        } else if(state === 'open') {
            this.curtainEffect = 'open';
            this.opacity = 1;
            this.intervalID = window.requestAnimationFrame(() => this.drawCurtain());
        }
    },
    loadingScreen() {
        viewport.layer.text.fillStyle = "black";
        viewport.layer.text.fillRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        viewport.layer.text.font = "30px Comic Sans MS";
        viewport.layer.text.fillStyle = "white";
        viewport.layer.text.textAlign = "center";
        viewport.layer.text.fillText("Loading ...", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
    },
    drawCurtain() {
        if (this.opacity < 0 && this.curtainEffect === 'open') {
            return false;
        } else if(this.opacity > 1 && this.curtainEffect === 'close') {
            this.loadingScreen();
            return false;
        }
        if (this.curtainEffect === 'open') {
            // Ease-in effect on loading
            if (this.opacity > 0.8) {
                this.opacity -= 0.005;
            }
            else if (this.opacity > 0.4) {
                this.opacity -= 0.015;
            }
            else {
                this.opacity -= 0.02;
            }
        }
        else {
            // Ease-in effect on loading
            if (this.opacity < 0.2) {
                this.opacity += 0.005;
            }
            else if (this.opacity < 0.6) {
                this.opacity += 0.015;
            }
            else {
                this.opacity += 0.02;
            }
        }
        viewport.layer.text.fillStyle = "rgba(0, 0, 0, " + this.opacity + ")";
        viewport.layer.text.clearRect(0, 0, viewport.width, viewport.height);
        viewport.layer.text.fillRect(0, 0, viewport.width, viewport.height);
        this.intervalID = window.requestAnimationFrame(() => this.drawCurtain());
    }
}