game.inactivityTime = function (pause = false) {
    console.log('inactivityTime');
    console.log('pause');
    let time;
    document.addEventListener("keydown", resetTimer);
    document.addEventListener("keyup", resetTimer);
    document.addEventListener("touchmove", resetTimer);
    if(pause == 'resume') {
        console.log('reset');
        resetTimer();
    }
    else if (pause == true) {
        console.log('pause true');
        clearTimeout(time);
        pauseGame(true);
    }
    function pauseGame(status = false, id) {
        if (id !== game.properties.pauseID && game.properties.pauseID !== null && status === false) return false;
        if (document.getElementById("conversation_container").style.visibility == "visible" || inBuilding == true) {
            resetTimer();
            return false;
        }
        if ((game.controls.up !== false || game.controls.right !== false ||
            game.controls.down !== false || game.controls.left !== false) && game.properties.device == "pc" &&
            game.properties.loading !== true) {
            resetTimer();
            return false;
        }
        else if ((document.getElementById("control_button").style.top !== "25%" &&
            document.getElementById("control_button").style.display != "none") && game.properties.device == "mobile" &&
            game.properties.loading !== true) {
            resetTimer();
            return false;
        }
        updateHunger();
        console.log(status);
        console.log(time);
        if (status !== 'loading') {
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
        game.properties.gamePause = true;
    }
    function resetTimer() {
        clearTimeout(time);
        game.properties.pauseID = time = setTimeout(() => pauseGame(false, time), 10000);
        // 1000 milliseconds = 1 second
    }
};
game.resumeGame = function (first = false) {
    if (first == true) {
        game.inactivityTime('resume');
    }
    else {
        game.properties.textContext.clearRect(0, 0, 700, 700);
        game.inactivityTime();
    }
    game.properties.gamePause = false;
    if (game.properties.requestId === null) {
        game.properties.requestId = window.requestAnimationFrame(game.update);
    }
    game.properties.startTime = new Date().getTime();
};