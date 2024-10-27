export interface GameLog {
    message: string;
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
