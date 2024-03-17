import { Game } from '../advclient';
import { tutorial } from './tutorial';
import { canvasTextHeader } from './canvasText';

interface IgameTravel {
    seconds: number;
    intervalID: number | null;
    travel: (destination: string, characterName: string) => boolean;
}

export const gameTravel: IgameTravel = {
    seconds: 14, // Countdown for the countdown function
    intervalID: null, // intervalID to clear interval when countdown is finished;
    travel(destination: string, characterName: string) {
        canvasTextHeader.setDraw('Travelling in 15', 15);
        if (tutorial.onGoing) tutorial.exitTutorial();

        let startPointType = false;

        // If character is sailor then draw player at dock
        if (characterName.indexOf('sailor')) {
            startPointType = true;
        } else {
            startPointType = false;
        }

        setTimeout(
            () =>
                Game.setWorld({
                    method: 'changeMap',
                    newDestination: destination,
                    startPointType,
                }),
            16000,
        );

        this.intervalID = window.setInterval(() => {
            if (gameTravel.seconds <= 0) {
                clearInterval(gameTravel.intervalID);
            } else {
                canvasTextHeader.text = 'Travelling in ' + gameTravel.seconds;
                canvasTextHeader.draw();
                gameTravel.seconds--;
            }
        }, 1000);
        return true;
    },
};
