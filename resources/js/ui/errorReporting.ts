let errorHandler: ((error: unknown) => void) | null = null;

export const reportError = (error: unknown) => {
  if (errorHandler) {
    errorHandler(error);
  }
};

export const registerErrorHandler = (callback: (error: unknown) => void) => {
  errorHandler = callback;
};
