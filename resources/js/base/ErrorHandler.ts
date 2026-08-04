import { ErrorHandler } from '@/lib/ErrorHandler';

export type ErrorPayload = {
  text: string;
  stack?: string;
};

export const initErrorHandler = () => {
  const handler = new ErrorHandler<ErrorPayload>('/log/error');

  handler.registerErrorListener((e: ErrorEvent | PromiseRejectionEvent) => {
    let text = '';
    let stack: string | undefined;
    if (e instanceof ErrorEvent) {
      text = `Frontend error: ${e.message}`;
      stack = e.error?.stack;
    } else if (e instanceof PromiseRejectionEvent) {
      text = `Unhandled promise rejection: ${e.reason?.message}`;
      stack = e.reason?.stack;
    }
    handler.logError({ text, stack });
  });

  return handler;
};

export const reportCatchError = (e: unknown) => {
  const handler = initErrorHandler();
  if (e instanceof Error) {
    handler.logError({ text: `Frontend error: ${e.message}`, stack: e.stack });
  } else {
    handler.logError({ text: `Frontend error: ${e}` });
  }
};
