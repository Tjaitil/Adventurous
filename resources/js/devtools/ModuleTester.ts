/**
 * Any object, class or function (classes and functions are objects)
 */
export type TestableModule = object;

interface WindowWithModules extends Window {
  modules?: Record<string, TestableModule>;
}

export class ModuleTester<T extends TestableModule = TestableModule> {
  public defaultExport: boolean;
  private module: T;
  private moduleName: string;

  constructor(
    module: T | { default: T },
    moduleName: string,
    options: ModuleTesterOptions,
  ) {
    this.defaultExport = options.defaultExport ?? false;
    this.moduleName = moduleName;

    if (this.defaultExport) {
      this.module = (module as { default: T }).default;
    } else {
      this.module = module as T;
    }
    this.toWindow();
    this.listMethods();
    this.checkHTMLElements();
  }

  public toWindow(): void {
    const moduleWindow: WindowWithModules = window;
    moduleWindow.modules ??= {};
    moduleWindow.modules[this.moduleName] = this.module;
    console.info('ModuleTester: ' + this.moduleName + ' added to window');
  }

  public listMethods(): void {
    console.info('ModuleTester: ' + this.moduleName + ' methods:');
    console.info(Object.getOwnPropertyNames(this.module));
  }

  public checkHTMLElements(): void {
    // Check if objects that is typed as htmlelement is actually an htmlelement
    const properties = this.module as Record<string, unknown>;
    for (const key in properties) {
      // Skip if function
      if (typeof properties[key] === 'function') {
        continue;
      }

      if (
        !key.includes('Wrapper') &&
        !key.includes('Element') &&
        !key.includes('Button') &&
        !key.includes('Container') &&
        !key.includes('Input')
      ) {
        continue;
      }

      if (!(properties[key] instanceof HTMLElement)) {
        console.warn('Potentially missing htmlelement: ', key);
      }
    }
  }
}

export interface ModuleTesterOptions {
  defaultExport?: boolean;
  instantiate?: boolean;
}

/**
 * Add module to window object in dev mode for easier testing
 */
export function addModuleTester<T extends TestableModule>(
  module: T | { default: T },
  name: string,
  options: ModuleTesterOptions = {},
): void {
  if (import.meta.env.DEV && import.meta.env.MODE !== 'test') {
    new ModuleTester(module, name, options);
  }
}
