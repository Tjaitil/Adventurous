/**
 * jsdom does not implement the native Popover API (showPopover/hidePopover/
 * togglePopover), so any component relying on it throws in tests. Call this
 * in a `beforeEach`/`beforeAll` for suites that render such components.
 */
export function mockPopoverApi(): void {
  HTMLElement.prototype.showPopover = function (this: HTMLElement): void {
    this.setAttribute('popover-open', '');
  };
  HTMLElement.prototype.hidePopover = function (this: HTMLElement): void {
    this.removeAttribute('popover-open');
  };
  HTMLElement.prototype.togglePopover = function (
    this: HTMLElement,
    options?: boolean | { force?: boolean; source?: HTMLElement },
  ): boolean {
    const force =
      typeof options === 'object' ? options.force : (options as boolean);
    const shouldOpen = force ?? !this.hasAttribute('popover-open');
    if (shouldOpen) {
      this.setAttribute('popover-open', '');
    } else {
      this.removeAttribute('popover-open');
    }
    return shouldOpen;
  };
}
