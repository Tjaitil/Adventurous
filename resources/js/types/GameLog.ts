export interface GameLog {
  message: string;
  type: GameLogType;
  timestamp?: string;
}

export type ParsedGameLog = Pick<GameLog, 'timestamp' | 'type'> & {
  message: string | string[];
};

export enum GameLogTypes {
  INFO = 'info',
  ERROR = 'error',
  WARNING = 'warning',
  SUCCESS = 'success',
}

export type GameLogType = `${GameLogTypes}`;
