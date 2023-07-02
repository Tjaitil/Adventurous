
// class ItemWrapper {

//     constructor() {
//         let div = document.createElement("div");
//         div.classList.add("item");

//         let figure = document.createElement("figure");
//         figure.appendChild(document.createElement("img"));

//         let figcaption = document.createElement("figcaption");
//         figcaption.classList.add("tooltip");
//         figure.appendChild(figcaption);
//         div.appendChild(figure);

//         let span = document.createElement("span");
//         span.classList.add("item_amount");
//         div.appendChild(span);
//         div.querySelectorAll("figcaption")[0].innerHTML = jsUcWords(name);
//         div.querySelectorAll("img")[0].src = "public/images/" + imgSrc + ".png";
//         div.querySelectorAll(".item_amount")[0].innerHTML = "" + amount;

//         // Add itemtitle events
//         div.addEventListener("mouseenter", (event) => itemTitle.show(event));
//         div.addEventListener("mouseleave", () => itemTitle.hide());
//         this.requirementsWrapper.appendChild(div);
//     }
// }