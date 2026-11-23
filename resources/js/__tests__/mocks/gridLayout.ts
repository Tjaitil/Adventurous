/**
 * jsdom doesn't compute real CSS layout, so offsetTop is always 0 for every
 * element, which makes grid arrow-key navigation untestable as-is. Call
 * this with the rendered elements and the column count they should behave
 * as if they were laid out in, to stub offsetTop into rows.
 */
export function mockGridLayout(elements: HTMLElement[], columns: number): void {
  elements.forEach((element, index) => {
    const row = Math.floor(index / columns);
    Object.defineProperty(element, 'offsetTop', {
      configurable: true,
      value: row * 100,
    });
  });
}
