import { ErrorHandler } from '@/lib/ErrorHandler';

export const initErrorHandler = () => {
  const handler = new ErrorHandler<{
    text: string;
  }>('/log/error');

  handler.registerErrorListener((e: ErrorEvent | PromiseRejectionEvent) => {
    let text = '';
    if (e instanceof ErrorEvent) {
      text = text = `Frontend error: ${e.message} ${e.error}`;
    } else if (e instanceof PromiseRejectionEvent) {
      text = `Unhandled promise rejection: ${e.reason.message} ${e.reason.stack}`;
    }
    handler.logError({
      text,
    });
  });

  return handler;
};

export const reportCatchError = (e: unknown) => {
  const handler = initErrorHandler();
  let text = '';
  if (e instanceof Error) {
    text = `Frontend error: ${e.message} ${e.stack}`;
    handler.logError({ text });
  } else {
    text = `Frontend error: ${e}`;
  }
  handler.logError({ text });
};
