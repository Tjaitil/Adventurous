export function throttle<Args extends unknown[]>(
  fn: (...args: Args) => void,
  waitMs: number,
): (...args: Args) => void {
  let lastCall = -Infinity;
  let timeout: ReturnType<typeof setTimeout> | null = null;
  let pendingArgs: Args | null = null;

  const invoke = (args: Args) => {
    lastCall = Date.now();
    fn(...args);
  };

  return (...args: Args) => {
    const remaining = waitMs - (Date.now() - lastCall);

    if (remaining <= 0) {
      if (timeout !== null) {
        clearTimeout(timeout);
        timeout = null;
      }
      pendingArgs = null;
      invoke(args);
      return;
    }

    pendingArgs = args;
    if (timeout === null) {
      timeout = setTimeout(() => {
        timeout = null;
        if (pendingArgs !== null) {
          const trailingArgs = pendingArgs;
          pendingArgs = null;
          invoke(trailingArgs);
        }
      }, remaining);
    }
  };
}
