import { AdvApi } from './../AdvApi.js';
import { ClientOverlayInterface } from "../clientScripts/clientOverlayInterface.js";
import { Inventory } from "../clientScripts/inventory.js";
import { itemTitle } from "../utilities/itemTitle.js";

const menubarToggle = {
	toggled: false,
	addEvent() {
		menubarToggle.toggled = true;
		let figures = document
			.getElementById("inventory")
			.querySelectorAll("figure");
		figures.forEach((element) => element.addEventListener("click", show_menu));
		itemTitle.removeTitleEvent();
	},
	removeEvent() {
		menubarToggle.toggled = false;
		let figures = document
			.getElementById("inventory")
			.querySelectorAll("figure");
		figures.forEach((element) =>
			element.removeEventListener("click", show_menu)
		);
	},
};
function addStockpileActions() {
	let listElements = document
		.getElementById("stck_menu")
		.querySelectorAll("LI");
	listElements.forEach((element, index) => {
		// First element is the item name, third is the input
		if ([0, 3].includes(index)) {
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
}
function addShowMenuEvent() {
	let figures = document.getElementById("stockpile").querySelectorAll("figure");
	figures.forEach(function (element) {
		element.addEventListener("click", show_menu);
	});
	if (/Safari|Chrome/i.test(navigator.userAgent)) {
		let spans = document.getElementsByClassName("item_amount");
		for (let i = 0; i < spans.length; i++) {
			let span = spans[i] as HTMLSpanElement;
			span[i].style.left = "-20%";
			span[i].style.display = "block";
		}
	}
}

function show_menu(event) {

	// Show menu above the item;
	let element = event.target.closest("div");
	let menu = document.getElementById("stck_menu");
	let list = menu.children[0] as HTMLElement;

	if (element.className == "inventory_item") {
		document.getElementById("inventory").appendChild(menu);
	} else {
		document.getElementById("news_content").appendChild(menu);
	}
	let item = element.getElementsByTagName("figcaption")[0].innerHTML;
	// Insert item name at the first li
	menu.children[0].children[0].innerHTML = item;
	menu.style.visibility = "visible";
	// Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
	let menuTop;
	let lis = list.children;
	let elementPos;
	let inputElement = document.getElementById("stck_menu_custom_amount") as HTMLInputElement;

	if (element.className == "inventory_item") {
		for (var i = 1; i < lis.length - 1; i++) {
			if (i === 3) {
				inputElement.placeholder = "Insert x";
			} else {
				lis[i].innerHTML = "Insert " + lis[i].innerHTML.split(" ")[1];
			}
		}
		lis[lis.length - 1].innerHTML = "Insert all";
		elementPos = element.getBoundingClientRect();
		if (
			element.offsetTop + 150 >
			document.getElementById("stockpile").offsetHeight
		) {
			menuTop = element.offsetTop - 70;
		} else {
			menuTop = element.offsetTop - 25;
		}
		list.style.left = element.offsetLeft + "px";
	} else {
		for (var x = 1; x < lis.length - 1; x++) {
			if (x === 3) {
				inputElement.placeholder = "Insert x";
			} else {
				lis[x].innerHTML = "Withdraw " + lis[x].innerHTML.split(" ")[1];
			}
		}
		lis[lis.length - 1].innerHTML = "Widthdraw all";
		elementPos = element.getBoundingClientRect();
		if (
			element.offsetTop + 150 >
			document.getElementById("stockpile").offsetHeight
		) {
			menuTop = element.offsetTop - 70;
		} else {
			menuTop = element.offsetTop + 85;
		}
		list.style.left = element.offsetLeft + "px";
	}
	list.style.top = menuTop + "px";
}


function hideMenu() {
	let menu = document.getElementById("stck_menu");
	menu.style.visibility = "hidden";
	document.getElementById("news_content").appendChild(menu);
}

const stockpileModule = {
	init() {
		document.getElementById("item_tooltip").style.visibility = "hidden";
		menubarToggle.addEvent();
		addShowMenuEvent();
		addStockpileActions();
	},
	stockpileAction(amountSet = false, event) {
		let element = event.target.closest("div").parentNode;
		let itemName = document
			.getElementById("stck_menu")
			.querySelectorAll("li")[0].innerHTML;
		let item =
			itemName
				.toLowerCase()
				.trim();
		let amount;
		let insert;

		if (document.getElementById("stck_menu").closest("#inventory")) {
			insert = true;
		} else {
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
			amount = event.target.innerHTML.split(" ")[1];
		}

		hideMenu();

		item = item.split("<br>")[0];

		let data = {
			insert,
			amount,
			item,
		}

		AdvApi.post("/stockpile/" + item, data).then((res) => {
			document.getElementById("stockpile").innerHTML = res.html["stockpile"];
			// ShowMenuEvent is added in updateInventory
			addShowMenuEvent();
			document.getElementById("stck_menu").style.visibility = "hidden";

			stckMenuInput.value = "";
			Inventory.update();
			ClientOverlayInterface.adjustWrapperHeight();
		}).catch((error) => {
			return;
		})
	},
	onClose() {
		menubarToggle.removeEvent();
		let menu = document.getElementById("stck_menu");
		// If menu is visible remove it
		if (menu)
			menu.parentElement.removeChild(document.getElementById("stck_menu"));
	},
};
export { stockpileModule as default, show_menu };
