// game.canvasText = {
//     textDrawn: false,
//     intervalID: 0,
//     gameText: document.getElementById("game_text"),
//     showText(text, timer = true, textDrawn = false) {
//         if(textDrawn === false) {
//             let canvas = document.getElementById("game_canvas");
//             this.gameText.style.top = canvas.offsetTop + 50 + "px";
//             this.gameText.innerHTML = text;
//             this.gameText.style.left = (game.properties.canvasWidth / 2) - (this.gameText.offsetWidth / 2) + "px";
//             this.gameText.style.opacity = 1;
//             /*this.intervalID = setInterval(this.changeTextOpactiy, 100);*/
//             // Set textDrawn status to true so it can be cleared
//             this.textDrawn = true;
//         }
//         if(timer == true) {
//             // Set textDrawn to false, so that hideText wont be called from viewport.drawEdge
//             this.textDrawn = false;
//             setTimeout(this.hideText, 3000);    
//         }
//     },
//     hideText() {
//         this.textDrawn = false;
//         console.log('hideText');
//         game.canvasText.gameText.style.opacity = 0;
//         setTimeout(function() {

//         game.canvasText.gameText.innerHTML = "";}, 500);
//     },
//     changeTextOpactiy() {
//         let opacity = game.canvasText.gameText.style.opacity = Number(game.canvasText.gameText.style.opacity) + 0.1;
//         if(opacity > 1) {
//             clearInterval(game.canvasText.intervalID);
//         }
//     }
// };
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
    draw(context) {
        game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        let contentY = game.properties.canvasHeight * 0.15;
        game.properties.textContext.fillStyle = "rgba(38, 38, 38," + this.opacity + ")";
        game.properties.textContext.fillRect(0, contentY, game.properties.canvasWidth, 200);
        game.properties.textContext.fillStyle = "rgba(255,255,255" + this.opacity + ")";
        game.properties.textContext.font = "35px Times New Roman";
        game.properties.textContext.textAlign = "center";
        game.properties.textContext.fillText(jsUcfirst(this.text), game.properties.canvasWidth * 0.50 - 20, contentY + 100);
    },
    unsetDraw: function () {
        console.log('unsetDraw');
        game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
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
        game.properties.textContext.fillStyle = "black";
        game.properties.textContext.fillRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        game.properties.textContext.font = "30px Comic Sans MS";
        game.properties.textContext.fillStyle = "white";
        game.properties.textContext.textAlign = "center";
        game.properties.textContext.fillText("Loading ...", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
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
        game.properties.textContext.fillStyle = "rgba(0, 0, 0, " + this.opacity + ")";
        game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        game.properties.textContext.fillRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        
        this.intervalID = window.requestAnimationFrame(() => this.drawCurtain());
    }
}