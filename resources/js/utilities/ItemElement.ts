import { AssetPaths } from '../clientScripts/ImagePath';
import { itemTitle } from './itemTitle';
import { jsUcWords } from './uppercase';

export class ItemElement {
    private element: HTMLElement = null;
    private imageElement: HTMLImageElement = null;
    private amountElement: HTMLElement = null;
    private nameElement: HTMLElement = null;

    constructor(
        element?: HTMLElement | Element | Node,
        initalItem?: Item,
        options?: ItemOptions,
    ) {
        const showTooltip = options?.showTooltip || false;

        if (initalItem) {
            this.element = document.createElement('div');
            this.element.classList.add('item');

            const figure = document.createElement('figure');
            figure.appendChild(document.createElement('img'));

            this.nameElement = document.createElement('figcaption');
            this.nameElement.classList.add('tooltip');
            figure.appendChild(this.nameElement);
            this.element.appendChild(figure);
            this.imageElement = figure.querySelector('img')[0];

            const span = document.createElement('span');
            span.classList.add('item_amount');
            this.element.appendChild(span);
            this.element.querySelectorAll(
                'figcaption .tooltip_item',
            )[0].innerHTML = jsUcWords(initalItem.name);
            this.element.querySelectorAll('img')[0].src =
                'images/' + initalItem.name + '.png';
            this.element.querySelectorAll('.item_amount')[0].innerHTML =
                '' + initalItem.amount;
            this.element.classList.add(initalItem.className);

            this.element.addEventListener('mouseenter', event =>
                itemTitle.show(event),
            );
            this.element.addEventListener('mouseleave', () => itemTitle.hide());
        } else {
            this.element = <HTMLElement>element;
            if (!this.element) throw new Error(`Element not found`);

            this.imageElement = this.element.querySelectorAll('img')[0];
            this.amountElement = <HTMLElement>(
                this.element.querySelectorAll('.item_amount')[0]
            );
            this.nameElement = <HTMLElement>(
                this.element.querySelectorAll('figcaption .tooltip_item')[0]
            );
        }

        if (!showTooltip) {
            this.element.classList.add('no-tooltip');
        }
    }

    public setClass(className: string) {
        this.element.classList.add(className);
        return this;
    }

    public removeClass(className: string) {
        this.element.classList.remove(className);
        return this;
    }

    public replaceItem(item: string, amount: number) {
        this.imageElement.src = AssetPaths.getImagePath(item + '.png');
        this.amountElement.innerText = amount.toString();
        this.nameElement.innerText = jsUcWords(item);
    }

    get item(): string {
        return this.element
            .querySelectorAll('figcaption .tooltip_item')[0]
            .innerText.trim()
            .toLowerCase();
    }

    get amount(): number {
        return parseInt(this.amountElement.innerText);
    }

    get HTMLElement(): HTMLElement {
        return this.element;
    }
}

export function getItemFromID(id: string) {}

interface Item {
    name: string;
    amount: number;
    className?: string;
    id?: string;
}

interface ItemOptions {
    showTooltip: boolean;
}
