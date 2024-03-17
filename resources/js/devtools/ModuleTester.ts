export class ModuleTester {
    public defaultExport: boolean;
    private module = null;
    private moduleName: string;

    constructor(module: any, moduleName: string, options: ModuleTesterOptions) {
        this.defaultExport = options.defaultExport ?? false;
        this.moduleName = moduleName;

        if (this.defaultExport) {
            this.module = module.default;
        } else {
            this.module = module;
        }
        this.toWindow();
        this.listMethods();
        this.checkHTMLElements();
    }

    public toWindow() {
        if (!(<any>window)['modules']) {
            (<any>window)['modules'] = {};
        }
        (<any>window)['modules'][this.moduleName] = this.module;
        console.info('ModuleTester: ' + this.moduleName + ' added to window');
    }

    public listMethods() {
        console.info('ModuleTester: ' + this.moduleName + ' methods:');
        console.info(Object.getOwnPropertyNames(this.module));
    }

    public checkHTMLElements() {
        // Check if objects that is typed as htmlelement is actually an htmlelement
        for (const key in this.module) {
            // Skip if function
            if (typeof this.module[key] === 'function') {
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

            if (!(this.module[key] instanceof HTMLElement)) {
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
export function addModuleTester(
    classInstance: any,
    name: string,
    options: ModuleTesterOptions = {},
) {
    if (import.meta.env.DEV) {
        new ModuleTester(classInstance, name, options);
    }
}
