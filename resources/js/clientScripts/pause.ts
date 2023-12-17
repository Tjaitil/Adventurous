import { GamePieces } from './gamePieces';
import { controls } from './controls';
import { Game } from '../advclient';
import viewport from './viewport';

export const pauseManager = {
    togglePause() {
        if (Game.properties.gameState === 'playing') {
            this.pauseGame();
        }
        else {
            this.resumeGame();
        }
    },
    pauseGame() {
        if (document.getElementById("conversation-container").style.visibility == "visible" || Game.properties.inBuilding == true) {
            return false;
        }
        if ((controls.playerUp !== false || controls.playerRight !== false ||
            controls.playerDown !== false || controls.playerLeft !== false) && Game.properties.device == "pc" &&
            Game.properties.gameState === 'playing') {
            return false;
        }
        else if ((document.getElementById("control_button").style.top !== "25%" &&
            document.getElementById("control_button").style.display != "none") && Game.properties.device == "mobile" &&
            Game.properties.gameState === 'playing') {
            return false;
        }
        // updateHunger();
        if (Game.properties.gameState !== 'loading') {
            viewport.drawText("72px Bold Serif", "#EB7A09", "Game Paused", viewport.width / 2, viewport.height / 2, true);
            viewport.drawText("72px Bold Serif", "#EB7A09", "Press P to continue", viewport.width / 2, viewport.height / 2 + 95, true);
        }
        window.cancelAnimationFrame(Game.properties.requestId);
        Game.properties.requestId = null;
        Game.setGameState('pause');
    },
    resumeGame(first = false) {
        if (["pause"].includes(Game.properties.gameState)) {
            viewport.resetTextLayer();
        }
        Game.setGameState('playing');
        Game.properties.requestId = window.requestAnimationFrame(Game.update);
    }
};
