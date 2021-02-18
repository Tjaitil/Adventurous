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
textHeader = {
    opacity: 0.0,
    intervalID: null,
    setDraw: function() {
        this.intervalID = setInterval(this.draw, 500);
    },
    draw: function() {
        game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        let contentY = game.properties.canvasHeight * 0.25;
        console.log(textHeader.opacity);
        game.properties.textContext.fillStyle = "rgba(38, 38, 38," + textHeader.opacity + ")";
        game.properties.textContext.fillRect(0, contentY, game.properties.canvasWidth, 200);
        game.properties.textContext.fillStyle = "rgba(255,255,255" + textHeader.opacity + ")";
        game.properties.textContext.font = "35px Times New Roman";
        game.properties.textContext.fillText("Location", game.properties.canvasWidth * 0.50 - 20, contentY + 100);
        if(textHeader.opacity <= 0.5) {
            textHeader.opacity += 0.1;
            console.log(this.opacity);
            setTimeout(textHeader.draw, 16);
        }
        else {
            setTimeout(function() {
                game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
                textHeader.opacity = 0;
                       }, 1500);
        }
    }
};