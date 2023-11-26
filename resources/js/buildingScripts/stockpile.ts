import { AdvApi } from './../AdvApi.js';
import { ClientOverlayInterface } from "../clientScripts/clientOverlayInterface.js";
import { Inventory } from "../clientScripts/inventory.js";
import { itemTitle } from "../utilities/itemTitle.js";

const stockpileModule = {
	toggled: false,
	addEvent() {
		this.toggled = true;

		Inventory.items.forEach((element) => element.addEventListener("click", (e) => this.show_menu(e)));
		itemTitle.removeTitleEvent();
	},
	removeEvent() {
		this.toggled = false;

		Inventory.items.forEach((element) => element.removeEventListener("click", (e) => this.show_menu(e)));
		itemTitle.addTitleEvent();
	},
	menuListItemInputIndex: 2,
	init() {
		document.getElementById("item_tooltip").style.visibility = "hidden";
		this.addEvent();
		this.addShowMenuEvent();
		this.addStockpileActions();
	},
	addShowMenuEvent() {
		let figures = document.getElementById("stockpile").querySelectorAll("figure");
		figures.forEach((element) =>
			element.addEventListener("click", (e) => this.show_menu(e))
		);
		if (/Safari|Chrome/i.test(navigator.userAgent)) {
			let spans = document.getElementsByClassName("item_amount");
			for (let i = 0; i < spans.length; i++) {
				let span = spans[i] as HTMLSpanElement;
				span[i].style.left = "-20%";
				span[i].style.display = "block";
			}
		}
	},
	addStockpileActions() {
		let listElements = document
			.getElementById("stck_menu")
			.querySelectorAll("LI");
		listElements.forEach((element, index) => {
			// First element is the item name, third is the input
			if ([this.menuListItemInputIndex].includes(index)) {
				return;
			}
			element.addEventListener("click", (event) =>
				stockpileModule.stockpileAction(false, event)
			);
		});
		document
			.getElementById("stck_menu_custom_amount")
			.addEventListener("keyup", (event) => {
				event.preventDefault();
				if (event.key === "Enter") {
					stockpileModule.stockpileAction(true, event);
				}
			});
	},
	stockpileAction(amountSet = false, event: Event) {
		let listItem = (event.target as HTMLElement);
		let itemName = document.getElementById("stck-current-item").innerHTML;
		let item =
			itemName
				.toLowerCase()
				.trim();
		let amount;
		let insert;
		let method;

		if (document.getElementById("stck_menu").closest("#inventory")) {
			method = "insert";
			insert = true;
		} else {
			method = "withdraw";
			insert = false;
		}

		let stckMenuInput = <HTMLInputElement>document.getElementById("stck_menu_custom_amount");

		if (amountSet) {
			amount = stckMenuInput.value;
		} else if (
			event.currentTarget === document.getElementById("stck_menu_all")
		) {
			let array: HTMLElement[] = [];
			if (insert === true) {
				array = [...document.getElementById("inventory").querySelectorAll(".inventory_item")] as HTMLElement[];
			} else {
				array = [...document.getElementById("stockpile").querySelectorAll(".stockpile_item")] as HTMLElement[];
			}
			let itemElement =
				array.find((element) =>
					element.querySelectorAll("figcaption")[0].innerHTML === itemName
				);
			amount = parseInt(itemElement.querySelectorAll(".item_amount")[0].innerHTML);
		} else {
			amount = parseInt(listItem.dataset.actionAmount);
		}

		this.hideMenu();

		item = item.split("<br>")[0];

		let data = {
			insert,
			amount,
			item,
		}

		AdvApi.post<UpdateStockpileRequest>("/stockpile/update", data).then((res) => {
			document.getElementById("stockpile-list").innerHTML = res.html.stockpile;
			document.getElementById("stck_menu").style.visibility = "hidden";

			stckMenuInput.value = "";
			Inventory.update().then(() => {
				itemTitle.removeTitleEvent();
				this.addShowMenuEvent();
				this.addEvent();
			});

			ClientOverlayInterface.adjustWrapperHeight();
		}).catch((error) => {
			return;
		})
	},
	show_menu(event: Event) {
		let element = (event.target as HTMLElement).closest("div");
		let menu = document.getElementById("stck_menu");
		let list = document.getElementById("stck-menu-option-list");

		if (element.className == "inventory_item") {
			document.getElementById("inventory").appendChild(menu);
		} else {
			document.getElementById("news_content").appendChild(menu);
		}
		let item = element.getElementsByTagName("figcaption")[0].innerHTML;
		// Insert item name at the first li
		document.getElementById("stck-current-item").innerHTML = item;
		menu.style.visibility = "visible";
		// Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
		let menuTop;
		let listItems = list.querySelectorAll("li");
		let elementPos;
		let inputElement = document.getElementById("stck_menu_custom_amount") as HTMLInputElement;

		let stockpileMenuItemsLabel = {
			insert: [
				"Insert 1",
				"Insert 5",
				"Insert x",
				"Insert all"
			],
			withdraw: [
				"Withdraw 1",
				"Withdraw 5",
				"Withdraw x",
				"Withdraw all"
			]
		};

		let currentStockpileMenuItemLabels;

		if (element.className == "inventory_item") {
			currentStockpileMenuItemLabels = stockpileMenuItemsLabel.insert;
			elementPos = element.getBoundingClientRect();
			if (
				element.offsetTop + 150 >
				document.getElementById("stockpile").offsetHeight
			) {
				menuTop = element.offsetTop - 70;
			} else {
				menuTop = element.offsetTop - 25;
			}
		} else {
			currentStockpileMenuItemLabels = stockpileMenuItemsLabel.withdraw;
			elementPos = element.getBoundingClientRect();
			if (
				element.offsetTop + 150 >
				document.getElementById("stockpile").offsetHeight
			) {
				menuTop = element.offsetTop - 70;
			} else {
				menuTop = element.offsetTop + 85;
			}
		}

		for (let i = 0; i < listItems.length; i++) {
			if (i === this.menuListItemInputIndex) {
				inputElement.placeholder = currentStockpileMenuItemLabels[i];
			} else {
				listItems[i].innerHTML = currentStockpileMenuItemLabels[i];
			}
		}

		menu.style.left = element.offsetLeft + "px";
		menu.style.top = menuTop + "px";
	},
	hideMenu() {
		let menu = document.getElementById("stck_menu");
		menu.style.visibility = "hidden";
		document.getElementById("news_content").appendChild(menu);
	},
	onClose() {
		this.removeEvent();
		let menu = document.getElementById("stck_menu");
		// If menu is visible remove it
		if (menu) {
			menu.parentElement.removeChild(document.getElementById("stck_menu"));
		}
	},
};
export { stockpileModule as default };

export interface UpdateStockpileRequest {
	html: {
		stockpile: string;
	}
}