import { throttle } from '@/utilities/throttle';
import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest';

describe('throttle', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  test('calls immediately on first call', () => {
    const fn = vi.fn();
    const throttled = throttle(fn, 100);

    throttled(1);

    expect(fn).toHaveBeenCalledTimes(1);
    expect(fn).toHaveBeenCalledWith(1);
  });

  test('suppresses calls within wait window and fires trailing call with latest args', () => {
    const fn = vi.fn();
    const throttled = throttle(fn, 100);

    throttled(1);
    throttled(2);
    throttled(3);
    expect(fn).toHaveBeenCalledTimes(1);

    vi.advanceTimersByTime(100);

    expect(fn).toHaveBeenCalledTimes(2);
    expect(fn).toHaveBeenLastCalledWith(3);
  });

  test('allows a call after wait has elapsed', () => {
    const fn = vi.fn();
    const throttled = throttle(fn, 100);

    throttled(1);
    vi.advanceTimersByTime(100);
    throttled(2);

    expect(fn).toHaveBeenCalledTimes(2);
    expect(fn).toHaveBeenLastCalledWith(2);
  });

  test('no trailing call when only one call was made', () => {
    const fn = vi.fn();
    const throttled = throttle(fn, 100);

    throttled(1);
    vi.advanceTimersByTime(500);

    expect(fn).toHaveBeenCalledTimes(1);
  });
});
