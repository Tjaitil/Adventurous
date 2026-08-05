/**
 * Only the last row of a row-major, left-to-right/top-to-bottom grid can be
 * ragged (fewer items than columns), so column count is derived from how
 * many leading elements share the first element's offsetTop. A plain
 * vertical list resolves to 1 column, which keeps single-column consumers
 * (e.g. a dropdown menu) working the same way.
 */
const getColumnCount = (elements: HTMLElement[]): number => {
  if (elements.length <= 1) {
    return elements.length || 1;
  }

  const firstRowTop = elements[0].offsetTop;
  const columns = elements.findIndex(
    element => element.offsetTop !== firstRowTop,
  );

  return columns === -1 ? elements.length : columns;
};

const getCurrentIndex = (elements: HTMLElement[]): number =>
  elements.findIndex(element => element === document.activeElement);

const focusHorizontal = (elements: HTMLElement[], direction: 1 | -1): void => {
  const currentIndex = getCurrentIndex(elements);
  if (currentIndex === -1) {
    return;
  }

  const columns = getColumnCount(elements);
  const column = currentIndex % columns;
  const nextColumn = column + direction;
  const nextIndex = currentIndex + direction;

  if (
    nextColumn < 0 ||
    nextColumn >= columns ||
    nextIndex < 0 ||
    nextIndex >= elements.length
  ) {
    return;
  }

  elements[nextIndex].focus();
};

const focusVertical = (elements: HTMLElement[], direction: 1 | -1): void => {
  const currentIndex = getCurrentIndex(elements);
  if (currentIndex === -1) {
    return;
  }

  const columns = getColumnCount(elements);
  const totalRows = Math.ceil(elements.length / columns);
  const currentRow = Math.floor(currentIndex / columns);
  const targetRow = currentRow + direction;

  if (targetRow < 0 || targetRow >= totalRows) {
    return;
  }

  const column = currentIndex % columns;
  const targetIndex = Math.min(
    targetRow * columns + column,
    elements.length - 1,
  );

  elements[targetIndex].focus();
};

export type ArrowKeyDirection = 'horizontal' | 'vertical' | 'both';

/**
 * Moves focus between `elements` in response to arrow keys, treating them as
 * a row-major grid (see `getColumnCount`). `direction` restricts which axis
 * of arrow keys is handled. Returns whether the key was handled so callers
 * can decide whether to prevent default / stop propagation.
 */
export const handleArrowKeys = (
  event: KeyboardEvent,
  elements: HTMLElement[] | null | undefined,
  direction: ArrowKeyDirection = 'both',
): boolean => {
  if (!elements || elements.length === 0) {
    return false;
  }

  const allowHorizontal = direction === 'horizontal' || direction === 'both';
  const allowVertical = direction === 'vertical' || direction === 'both';

  switch (event.key) {
    case 'ArrowRight':
      if (!allowHorizontal) {
        return false;
      }
      focusHorizontal(elements, 1);
      return true;
    case 'ArrowLeft':
      if (!allowHorizontal) {
        return false;
      }
      focusHorizontal(elements, -1);
      return true;
    case 'ArrowDown':
      if (!allowVertical) {
        return false;
      }
      focusVertical(elements, 1);
      return true;
    case 'ArrowUp':
      if (!allowVertical) {
        return false;
      }
      focusVertical(elements, -1);
      return true;
    default:
      return false;
  }
};
