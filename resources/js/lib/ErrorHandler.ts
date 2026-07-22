import axios from 'axios';

type ErrorCallback = (error: ErrorEvent | PromiseRejectionEvent) => void;
export class ErrorHandler<T> {
  private static isListening = false;
  private static seenErrors = new Set<string>();

  endpoint: string;

  constructor(endpoint: string) {
    this.endpoint = endpoint;
  }

  public registerErrorListener(callback: ErrorCallback) {
    if (ErrorHandler.isListening) return;
    ErrorHandler.isListening = true;
    window.addEventListener('error', e => {
      callback(e);
    });
    window.addEventListener('unhandledrejection', function (e) {
      callback(e);
    });
  }

  public logError(format: T) {
    const key = JSON.stringify(format);
    if (ErrorHandler.seenErrors.has(key)) return;
    ErrorHandler.seenErrors.add(key);
    axios.post(this.endpoint, format);
  }
}
