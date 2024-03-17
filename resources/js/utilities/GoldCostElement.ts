export class GoldCostElement {
    private element: HTMLElement;
    private goldAmountElement: HTMLElement;
    private goldIconElement: HTMLElement;
    private goldCost: number;

    constructor(element: HTMLElement) {
        this.element = element;
        this.goldAmountElement = <HTMLElement>(
            element.querySelectorAll('.gold-cost-amount')[0]
        );
        this.goldIconElement = <HTMLElement>(
            element.querySelectorAll('.gold-icon')[0]
        );
        this.goldCost = parseInt(this.goldAmountElement.innerText);
    }

    public setClass(className: string) {
        this.element.classList.add(className);
        return this;
    }

    public removeClass(className: string) {
        this.element.classList.remove(className);
        return this;
    }

    public setGoldCost(goldCost: number) {
        this.goldCost = goldCost;
        this.goldAmountElement.innerText = goldCost.toString();
    }

    public getGoldCost(): number {
        return this.goldCost;
    }

    public getHTMLElement(): HTMLElement {
        return this.element;
    }
}
