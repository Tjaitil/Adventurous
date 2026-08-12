import { describe, expect, test, beforeEach } from 'vitest';
import { SpatialGrid, CELL_SIZE, type SpatialObject } from '@/clientScripts/SpatialGrid';

function makeObject(
  diameterLeft: number,
  diameterUp: number,
  diameterRight: number,
  diameterDown: number,
  noCollision = false,
): SpatialObject {
  return { diameterLeft, diameterUp, diameterRight, diameterDown, noCollision };
}

describe('SpatialGrid', () => {
  let grid: SpatialGrid<SpatialObject>;

  beforeEach(() => {
    grid = new SpatialGrid<SpatialObject>();
  });

  test('query returns an inserted object that overlaps the query box', () => {
    const obj = makeObject(0, 0, 10, 10);
    grid.insert(obj);

    expect(grid.query(0, 0, 10, 10)).toEqual([obj]);
  });

  test('query does not return objects outside the query box', () => {
    const obj = makeObject(0, 0, 10, 10);
    grid.insert(obj);

    expect(grid.query(1000, 1000, 1010, 1010)).toEqual([]);
  });

  test('query returns each object only once even if it spans multiple cells', () => {
    // Spans several CELL_SIZE-wide cells both horizontally and vertically.
    const obj = makeObject(0, 0, CELL_SIZE * 3, CELL_SIZE * 3);
    grid.insert(obj);

    const result = grid.query(0, 0, CELL_SIZE * 3, CELL_SIZE * 3);

    expect(result).toEqual([obj]);
  });

  test('query returns multiple distinct objects overlapping the box', () => {
    const a = makeObject(0, 0, 10, 10);
    const b = makeObject(5, 5, 15, 15);
    grid.insert(a);
    grid.insert(b);

    const result = grid.query(0, 0, 20, 20);

    expect(result).toHaveLength(2);
    expect(result).toEqual(expect.arrayContaining([a, b]));
  });

  test('remove takes an object out of the grid so it is no longer queryable', () => {
    const obj = makeObject(0, 0, 10, 10);
    grid.insert(obj);
    grid.remove(obj);

    expect(grid.query(0, 0, 10, 10)).toEqual([]);
  });

  test('remove is a no-op for an object that was never inserted', () => {
    const obj = makeObject(0, 0, 10, 10);

    expect(() => {
      grid.remove(obj);
    }).not.toThrow();
  });

  test('reset clears all inserted objects', () => {
    const a = makeObject(0, 0, 10, 10);
    const b = makeObject(CELL_SIZE * 5, CELL_SIZE * 5, CELL_SIZE * 5 + 10, CELL_SIZE * 5 + 10);
    grid.insert(a);
    grid.insert(b);

    grid.reset();

    expect(grid.query(-1000, -1000, 1000, 1000)).toEqual([]);
  });

  test('an object moved via remove+insert is found at its new position and not its old one', () => {
    const obj = makeObject(0, 0, 10, 10);
    grid.insert(obj);

    grid.remove(obj);
    obj.diameterLeft = 500;
    obj.diameterUp = 500;
    obj.diameterRight = 510;
    obj.diameterDown = 510;
    grid.insert(obj);

    expect(grid.query(0, 0, 10, 10)).toEqual([]);
    expect(grid.query(500, 500, 510, 510)).toEqual([obj]);
  });
});
