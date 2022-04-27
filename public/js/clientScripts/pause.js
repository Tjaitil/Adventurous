const pauseManager = {
    togglePause() {
        if(game.properties.gameState === 'playing')  {
            this.pauseGame();
        }   
        else {
            this.resumeGame();
        } 
    },
    pauseGame(status = false, id) {
        if (document.getElementById("conversation_container").style.visibility == "visible" || game.properties.inBuilding == true) {
            resetTimer();
            return false;
        }
        if ((controls.playerUp !== false || controls.playerRight !== false ||
            controls.playerDown !== false || controls.playerLeft !== false) && game.properties.device == "pc" &&
            game.properties.gameState === 'playing') {
            resetTimer();
            return false;
        }
        else if ((document.getElementById("control_button").style.top !== "25%" &&
            document.getElementById("control_button").style.display != "none") && game.properties.device == "mobile" &&
            game.properties.gameState === 'playing') {
            resetTimer();
            return false;
        }
        // updateHunger();
        if (game.properties.gameState !== 'loading') {
            viewport.drawText("72px Bold Serif", "#EB7A09", "Game Paused", viewport.width / 2, viewport.height / 2, true);
            viewport.drawText("72px Bold Serif", "#EB7A09", "Press P to continue", viewport.width / 2, viewport.height / 2 + 95, true);
        }
        window.cancelAnimationFrame(game.properties.requestId);
        game.properties.requestId = null;
        game.setGameState('pause');
    },
    resumeGame(first = false) {
        if(["pause"].includes(game.properties.gameState)) {
            viewport.resetTextLayer();
        }
        game.setGameState('playing');
        game.properties.requestId = window.requestAnimationFrame(game.update);
    }
};
