export interface GameObject {
    diameterUp: number;
    diameterLeft: number;
    diameterRight: number;
    diameterDown: number;
    x: number;
    y: number;
    width: number;
    height: number;
    noCollision: boolean;
    type: string;
}
