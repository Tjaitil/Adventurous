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
        console.log('pause');
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
            game.properties.textContext.font = "30px Comic Sans MS";
            game.properties.textContext.fillStyle = "pink";
            game.properties.textContext.textAlign = "center";
            game.properties.textContext.fillText("Game Paused", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
            game.properties.textContext.font = "20px Comic Sans MS";
            game.properties.textContext.fillText("Press P to continue", game.properties.canvasWidth / 2,
                game.properties.canvasHeight / 2 + 35);
        }
        window.cancelAnimationFrame(game.properties.requestId);
        game.properties.requestId = null;
        game.setGameState('pause');
    },
    inactivityTime(pause = false) {
        let time;
        document.addEventListener("keydown", resetTimer);
        document.addEventListener("keyup", resetTimer);
        document.addEventListener("touchmove", resetTimer);
        if(pause == 'resume') {
            resetTimer();
        }
        else if (pause == true) {
            clearTimeout(time);
            pauseGame(true);
        }
        function pauseGame(status = false, id) {
            if (id !== game.properties.pauseID && game.properties.pauseID !== null && status === false) return false;
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
            if (game.properties.gamestatus !== 'loading') {
                game.properties.textContext.font = "30px Comic Sans MS";
                game.properties.textContext.fillStyle = "pink";
                game.properties.textContext.textAlign = "center";
                game.properties.textContext.fillText("Game Paused", game.properties.canvasWidth / 2, game.properties.canvasHeight / 2);
                game.properties.textContext.font = "20px Comic Sans MS";
                game.properties.textContext.fillText("Press any key to continue", game.properties.canvasWidth / 2,
                    game.properties.canvasHeight / 2 + 35);
            }
            window.cancelAnimationFrame(game.properties.requestId);
            game.properties.requestId = null;
            game.setGameState('pause');
        }
        function resetTimer() {
            clearTimeout(time);
            game.properties.pauseID = time = setTimeout(() => pauseGame(false, time), 10000);
            // 1000 milliseconds = 1 second
        }
    },
    resumeGame(first = false) {
        // if (first == true) {
        //     pauseManager.inactivityTime('resume');
        // }
        // else {
        //     if(game.properties.gameState === 'pause') {
        //         game.properties.textContext.clearRect(0, 0, 700, 700);
        //     }
        //     pauseManager.inactivityTime();
        // }
        // console.log('resumeGame: ' + first);
        // console.log(game.properties.requestId);
        // if(game.properties.requestId === null ) {
            if(["loading", "pause"].includes(game.properties.gameState)) {
                game.properties.textContext.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
            }
            game.setGameState('playing');
            game.properties.requestId = window.requestAnimationFrame(game.update);
        // }
        // game.properties.startTime = new Date().getTime();
    }
};
