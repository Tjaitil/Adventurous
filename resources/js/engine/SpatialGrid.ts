export const CELL_SIZE = 32;

export interface SpatialObject {
  diameterLeft: number;
  diameterUp: number;
  diameterRight: number;
  diameterDown: number;
  noCollision?: boolean;
}

export class SpatialGrid<T extends SpatialObject> {
  private cells = new Map<string, T[]>();
  private objectCells = new Map<T, string[]>();

  private key(cx: number, cy: number): string {
    return `${cx},${cy}`;
  }

  private toCell(n: number): number {
    return Math.floor(n / CELL_SIZE);
  }

  insert(obj: T): void {
    const cellLeft = this.toCell(obj.diameterLeft);
    const cellTop = this.toCell(obj.diameterUp);
    const cellRight = this.toCell(obj.diameterRight);
    const cellBottom = this.toCell(obj.diameterDown);
    const keys: string[] = [];
    for (let cx = cellLeft; cx <= cellRight; cx++) {
      for (let cy = cellTop; cy <= cellBottom; cy++) {
        const k = this.key(cx, cy);
        const cell = this.cells.get(k) ?? [];
        cell.push(obj);
        this.cells.set(k, cell);
        keys.push(k);
      }
    }
    this.objectCells.set(obj, keys);
  }

  remove(obj: T): void {
    const keys = this.objectCells.get(obj);
    if (!keys) return;
    for (const k of keys) {
      const cell = this.cells.get(k);
      if (cell) {
        const idx = cell.indexOf(obj);
        if (idx !== -1) cell.splice(idx, 1);
      }
    }
    this.objectCells.delete(obj);
  }

  query(minX: number, minY: number, maxX: number, maxY: number): T[] {
    const cellLeft = this.toCell(minX);
    const cellTop = this.toCell(minY);
    const cellRight = this.toCell(maxX);
    const cellBottom = this.toCell(maxY);
    const result = new Set<T>();
    for (let cx = cellLeft; cx <= cellRight; cx++) {
      for (let cy = cellTop; cy <= cellBottom; cy++) {
        this.cells.get(this.key(cx, cy))?.forEach(o => result.add(o));
      }
    }
    return Array.from(result);
  }

  reset(): void {
    this.cells.clear();
    this.objectCells.clear();
  }
}
