import { describe, test, expect, beforeEach, vi } from 'vitest';
import axios from 'axios';
import { ErrorHandler } from '@/lib/ErrorHandler';
import type { ErrorPayload } from '@/base/ErrorHandler';

vi.mock('axios', () => ({
  default: { post: vi.fn() },
}));

describe('ErrorHandler', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  test('does not post the same error payload twice', () => {
    const handler = new ErrorHandler<{ text: string }>('/log/error');
    const payload = { text: 'Frontend error: dedupe-me' };

    handler.logError(payload);
    handler.logError(payload);

    expect(axios.post).toHaveBeenCalledTimes(1);
    expect(axios.post).toHaveBeenCalledWith('/log/error', payload);
  });

  test('still posts a payload that differs from a previously seen one', () => {
    const handler = new ErrorHandler<{ text: string }>('/log/error');

    handler.logError({ text: 'Frontend error: unique-a' });
    handler.logError({ text: 'Frontend error: unique-b' });

    expect(axios.post).toHaveBeenCalledTimes(2);
  });

  test('dedupes a payload with a stack field, not just text', () => {
    const handler = new ErrorHandler<ErrorPayload>('/log/error');
    const payload = {
      text: 'Frontend error: with-stack',
      stack: 'Error: boom\n  at foo (foo.ts:1:1)',
    };

    handler.logError(payload);
    handler.logError(payload);
    handler.logError({ ...payload, stack: 'Error: different stack' });

    expect(axios.post).toHaveBeenCalledTimes(2);
  });
});
