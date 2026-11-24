import { describe, expect, beforeEach, afterEach, vi, test } from 'vitest';
import { canvasTextHeader } from '@/clientScripts/canvasText';

const textLayer = vi.hoisted(() => ({
  clearRect: vi.fn(),
  fillRect: vi.fn(),
  fillText: vi.fn(),
  fillStyle: '',
  font: '',
  textAlign: '',
}));

vi.mock('@/clientScripts/viewport', () => ({
  default: {
    width: 800,
    height: 600,
    layer: { text: textLayer },
  },
}));

describe('canvasTextHeader', () => {
  beforeEach(() => {
    vi.useFakeTimers();
    vi.clearAllMocks();
    canvasTextHeader.opacity = 0;
    canvasTextHeader.text = null;
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  test('setDraw ignores empty text', () => {
    expect(canvasTextHeader.setDraw('', 2)).toBe(false);
    vi.advanceTimersByTime(500);

    expect(canvasTextHeader.text).toBeNull();
    expect(textLayer.fillText).not.toHaveBeenCalled();
  });

  test('setDraw draws non-empty text with capitalized first letter', () => {
    canvasTextHeader.setDraw('you died!', 2);
    vi.advanceTimersByTime(75);

    expect(canvasTextHeader.text).toBe('you died!');
    expect(textLayer.fillText).toHaveBeenCalledWith(
      'You died!',
      expect.any(Number),
      expect.any(Number),
    );
  });

  test('draw uses a valid rgba text color', () => {
    canvasTextHeader.text = 'hello';
    canvasTextHeader.opacity = 0.3;
    canvasTextHeader.draw();

    expect(textLayer.fillStyle).toBe('rgba(255,255,255,0.3)');
  });

  test('text is cleared after timer elapses', () => {
    canvasTextHeader.setDraw('hello', 2);
    vi.advanceTimersByTime(2000);

    expect(textLayer.clearRect).toHaveBeenCalled();
    expect(canvasTextHeader.opacity).toBe(0);
  });
});
