game.canvasText = {
    textDrawn: false,
    intervalID: 0,
    gameText: document.getElementById("game_text"),
    showText(text, timer = true, textDrawn = false) {
        if(textDrawn === false) {
            let canvas = document.getElementById("game_canvas");
            this.gameText.style.top = canvas.offsetTop + 50 + "px";
            this.gameText.innerHTML = text;
            this.gameText.style.left = (game.properties.canvasWidth / 2) - (this.gameText.offsetWidth / 2) + "px";
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
canvasTextHeader = {
    opacity: 0.0,
    intervalID: null,
    text: null,
    transition: false,
    timer: 0,
    setDraw: function(text, timer) {
        this.text = text;
        this.intervalID = setInterval(() => this.adjustOpacity(), 75);
        setTimeout(() => this.unsetDraw(), timer * 1000);
    },
    adjustOpacity() {
        if(this.opacity <= 0.4) {
            this.opacity += 0.1;
            this.draw();
        }
        else {
            clearInterval(canvasTextHeader.intervalID);
        }
    },
    draw: function() {
        game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        let contentY = game.properties.canvasHeight * 0.15;
        game.properties.textContext.fillStyle = "rgba(38, 38, 38," + this.opacity + ")";
        game.properties.textContext.fillRect(0, contentY, game.properties.canvasWidth, 200);
        game.properties.textContext.fillStyle = "rgba(255,255,255" + this.opacity + ")";
        game.properties.textContext.font = "35px Times New Roman";
        game.properties.textContext.textAlign = "center";
        game.properties.textContext.fillText(jsUcfirst(this.text), game.properties.canvasWidth * 0.50 - 20, contentY + 100);
    },
    unsetDraw: function() {
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
    setLoading() {
        game.properties.loading = false;
        this.intervalID = window.requestAnimationFrame(() => this.draw());
    },
    draw() {
        if(duration % 3 === 0) {
            if(this.opacity <= 0) {
                window.cancelAnimationFrame(this.intervalID);
                // Set header where you are at
                canvasTextHeader.setDraw(document.title, 5);
                this.opacity = 1;
                return false;
            }
            // Ease-in effect on loading
            if(this.opacity > 0.8) {
                this.opacity -= 0.015;
            }
            else if(this.opacity > 0.4) {
                this.opacity -= 0.04;
            }
            else {
                this.opacity -= 0.06;
            }
            game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
            game.properties.textContext.fillStyle = "rgba(0, 0, 0, " + this.opacity + ")";
            game.properties.textContext.fillRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        }
        this.intervalID = window.requestAnimationFrame(() => this.draw());
    }
}