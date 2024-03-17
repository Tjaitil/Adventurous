import { addModuleTester } from '@/devtools/ModuleTester';
import { CustomFetchApi } from '../CustomFetchApi';

export enum commonMessages {
    'inventoryFull' = 'Remove some items from inventory before doing this action',
}

export class GameLogger {
    private static messages: GameLog[] = [];
    private static currentlyLogging = false;
    private static currentIndex = 0;

    public static addErrorMessage(
        message: string,
        instantLog = false,
        shouldLogToApi = false,
    ) {
        this.addMessage(
            { text: message, type: GameLogTypes.ERROR },
            instantLog,
            shouldLogToApi,
        );
    }

    public static addSuccessMessage(
        message: string,
        instantLog = false,
        shouldLogToApi = false,
    ) {
        this.addMessage(
            { text: message, type: GameLogTypes.SUCCESS },
            instantLog,
            shouldLogToApi,
        );
    }

    public static addWarningMessage(
        message: string,
        instantLog = false,
        shouldLogToApi = false,
    ) {
        this.addMessage(
            { text: message, type: GameLogTypes.WARNING },
            instantLog,
            shouldLogToApi,
        );
    }

    public static addInfoMessage(
        message: string,
        instantLog = false,
        shouldLogToApi = false,
    ) {
        this.addMessage(
            { text: message, type: GameLogTypes.INFO },
            instantLog,
            shouldLogToApi,
        );
    }

    /**
     * @param message string|GameLog
     * This function accepts a string for legacy reasons until all calls to this function are updated
     */
    public static addMessage(
        message: string | GameLog,
        instantLog = false,
        shouldLogToApi = false,
    ) {
        if (typeof message !== 'string') {
            this.messages.push(message);
        } else {
            this.messages.push({
                text: message,
                type: GameLogTypes.INFO,
            });
        }

        if (shouldLogToApi === true) {
            this.logMessageToApi(message);
        }

        // Use to start this.logMessages instead of having to call it directly in another file
        if (instantLog) this.logMessages();
    }

    public static addMessages(
        messages: GameLog[],
        instantLog = false,
        shouldLogToApi = false,
    ) {
        messages.forEach(message =>
            this.addMessage(message, false, shouldLogToApi),
        );
        if (instantLog) this.logMessages();
    }

    public static logMessages() {
        if (this.messages.length === 0) return false;
        // Start new loop only if none is set

        if (!this.currentlyLogging) {
            this.clientLog();
        }
        this.currentlyLogging = true;
    }

    private static mainLog() {
        function addZero(num: number): string {
            let str = num + '';
            if (num < 10) {
                str = '0' + num;
            }
            return str;
        }

        const message = this.messages[this.currentIndex];
        const td = <HTMLTableCellElement>(
            document
                .getElementById('game_messages')
                .querySelectorAll('td')[0]
                .cloneNode(true)
        );

        td.classList.add(this.getColorFromType(message.type));

        if (message.timestamp === undefined) {
            const d = new Date();
            const time =
                '[' +
                addZero(d.getHours()) +
                ':' +
                addZero(d.getMinutes()) +
                ':' +
                addZero(d.getSeconds()) +
                '] ';
            message.timestamp = time;
        }

        td.innerHTML = message.timestamp + message.text;
        const tr = document.createElement('TR');
        tr.appendChild(td);

        const logElement = document.getElementById('log');
        const isScrolledToBottom =
            logElement.scrollHeight - logElement.clientHeight <=
            logElement.scrollTop + 1;
        document
            .getElementById('game_messages')
            .querySelectorAll('tbody')[0]
            .appendChild(tr);
        this.removeLogElementIfOverLength();
        // scroll to bottom if isScrolledToBottom
        if (isScrolledToBottom) {
            logElement.scrollTop =
                logElement.scrollHeight - logElement.clientHeight;
        }
    }
    /**
     * @param message string|GameLog
     * Typing for legacy support see GameLogger.addMessage()
     */
    private static logMessageToApi(
        message: GameLog | string,
        instantLog = false,
    ) {
        let messageToLog;
        if (typeof message === 'string') {
            messageToLog = {
                text: message,
            };
        } else {
            messageToLog = message;
        }

        CustomFetchApi.post('/log', messageToLog)
            .then(() => false)
            .catch(() => false);
    }

    private static clientLog() {
        const message = this.messages[this.currentIndex];
        const div = document.getElementById('log-modal');
        div.querySelectorAll('p')[0].innerHTML = message.text;
        div.style.opacity = '1';
        div.style.height = '50px';
        div.style.top = window.scrollY + 5 + 'px';

        div.classList.add(this.getColorFromType(message.type));
        this.mainLog();
        setTimeout(() => {
            div.style.height = '4px';
        }, 3700);
        setTimeout(() => {
            if (this.currentIndex !== this.messages.length - 1) {
                this.currentIndex++;
                this.clientLog();
            } else {
                this.closeClientLog();
            }
        }, 4000);
    }

    private static getColorFromType(type: GameLogType) {
        switch (type) {
            case GameLogTypes.ERROR:
                return 'text-red-600';
            case GameLogTypes.WARNING:
                return 'text-yellow-600';
            case GameLogTypes.SUCCESS:
                return 'text-green-600';
            default:
                return 'text-black-600';
        }
    }

    private static closeClientLog() {
        const div = document.getElementById('log-modal');
        div.style.height = '4px';
        div.style.top = '0px';
        div.style.opacity = '0';

        this.messages = [];
        this.currentIndex = 0;
        this.currentlyLogging = false;
    }

    private static removeLogElementIfOverLength() {
        if (this.messages.length > 100) {
            document
                .getElementById('game_messages')
                .querySelectorAll('td')[0]
                .remove();
        }
    }
}

interface GameLog {
    text: string;
    type: GameLogType;
    timestamp?: string;
}

enum GameLogTypes {
    INFO = 'info',
    ERROR = 'error',
    WARNING = 'warning',
    SUCCESS = 'success',
}

type GameLogType = `${GameLogTypes}`;
addModuleTester(GameLogger, 'GameLogger');
