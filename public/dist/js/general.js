import { sidebar } from './clientScripts/sidebar';
import { itemTitle } from './utilities/itemTitle.js';
import { inventorySidebarMob } from './utilities/inventoryToggle.js';
import { ajaxP } from './ajax.js';
import { clientOverlayInterface } from './clientScripts/clientOverlayInterface.js';
// scriptLoader.loadScript(['itemTitle', 'uppercase', 'inventoryToggle'], 'utility');
const generalProperties = {
    computerDevice: true,
    setDeviceType() {
        if (/Mobi|Android/i.test(navigator.userAgent)) {
            this.computerDevice = false;
        }
    }
};
window.addEventListener("load", () => generalInit());
function generalInit() {
    generalProperties.setDeviceType();
    itemTitle.init(generalProperties.computerDevice);
    // document.getElementById("help_button").addEventListener("click", () => helpContainer.toggle());
    // helpContainer.helpElement = document.getElementById("help");
    var log = document.getElementById("log");
    if (log != null) {
        log.scrollTop = log.scrollHeight - log.clientHeight;
    }
    if (document.getElementById("sidebar") != null) {
        document.getElementById("sidebar").style.width = document.getElementsByTagName("aside")[0].clientWidth + "px";
        sidebar.addClickEvent();
    }
    if (document.getElementById("inventory") != null) {
        if (window.location.href.indexOf("stockpile") == -1) {
        }
        if (/Safari|Chrome/i.test(navigator.userAgent)) {
            let span = document.getElementsByClassName("item_amount");
            for (var i = 0; i < span.length; i++) {
                // TODO: Fix
                // span[i].style.left = "-20%";
                // span[i].style.display = "block";
            }
        }
    }
    if (location.href.indexOf("advclient") != -1) {
        let linksDiv = document.querySelectorAll(".top_bar");
        linksDiv.forEach((element, index) => {
            // If the device is mobile the first a is not displayed
            if (linksDiv.length - 1 === index)
                return;
            if (element.querySelectorAll("a")[0].style.display != "none") {
                element.querySelectorAll("a")[0].setAttribute("target", "_blank");
            }
            element.querySelectorAll("a")[1].setAttribute("target", "_blank");
        });
    }
    // Check screen width
    if (window.screen.width < 830) {
        let inventory = document.getElementById("inventory");
        inventory.style.visibility = "hidden";
        inventory.style.transition = "width 0.5s";
        inventory.style.transitionTimingFunction = "ease-out";
        document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
    }
    if (location.href.indexOf("gameguide") == -1) {
        // checkInboxMessages();
    }
}
/*window.addEventListener("scroll", function(e) {
    let element = e.target;
    console.log(element.scrollHeight - element.scrollTop - element.clientHeight);
    console.log(element);
    let inv = document.getElementById("inventory");
    if(inv.style.visibility === "visible") {
        e.preventDefault();
    }
}, true);*/
// function checkInboxMessages() {
//     var data = "model=Messages" + "&method=checkMessages";
//     ajaxG(data, function (response) {
//         console.log(response[1]);
//         if (response[1] > 0) {
//             document.getElementById("nav").querySelectorAll(".top_but")[4].children[0].innerHTML += ' ' + '(' + response[1] + ')';
//         }
//     });
// }
// function displayNav() {
//     var visibility = document.getElementById("nav_2").children[1].style.visibility;
//     if (visibility == 'visible') {
//         document.getElementById("nav_2").children[1].style.visibility = "hidden";
//     }
//     else {
//         document.getElementById("nav_2").children[1].style.visibility = "visible";
//     }
// }
function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
// function getgMessage() {
//     let ajaxRequest = new XMLHttpRequest();
//     ajaxRequest.onload = function () {
//         if (this.readyState == 4 && this.status == 200) {
//             console.log(this.responseText);
//             gameLog(this.responseText);
//         }
//     };
//     ajaxRequest.open('GET', "handlers/handler_ses.php?variable=game_message");
//     ajaxRequest.send();
// }
/*window.addEventListener("load", getgMessage, false);*/
const mainContentHelpContainer = {
    helpElement: null,
    toggled: false,
    toggle() {
        if (this.toggled === false) {
            this.helpElement.style.height = "250px";
            this.toggled = true;
        }
        else {
            this.helpElement.style.height = "0px";
            this.toggled = false;
        }
    }
};
// function alertMessage(page, pIdentifier = false) {
//     // pIdentifer is used to identify which content to append if this function is used multiple times on the same page
//     var subButton = document.createElement("button");
//     subButton.appendChild(document.createTextNode("Submit"));
//     subButton.setAttribute("id", "alert_submit");
//     var cancButton = document.createElement("button");
//     cancButton.appendChild(document.createTextNode("Cancel"));
//     cancButton.setAttribute("id", "alert_cancel");
//     var content = {};
//     switch (page) {
//         case 'citycentre':
//             var link = document.createElement("a");
//             link.appendChild(document.createTextNode("here"));
//             link.setAttribute("href", "/gameguide/profiency");
//             console.log(link);
//             var p = document.createElement("p");
//             var message =
//                 'Beware that changing profiency may result in lowering levels </br> and no access to profiency specific activites';
//             var message2 = '</br> Read more on </br>';
//             var message3 = "Are you sure you want to continue?";
//             p.innerHTML = message + message2;
//             p.appendChild(link);
//             p.innerHTML += '</br>' + message3;
//             content[0] = p;
//             document.getElementById("alert_submit").addEventListener("click", function () {
//                 changeProfiency();
//                 closeNews();
//             });
//             break;
//     }
//     content[1] = subButton;
//     content[2] = cancButton;
//     openNews(content);
//     document.getElementById("alert_cancel").addEventListener("click", closeNews);
//     document.getElementById("cont_exit").addEventListener("click", closeNews);
// }
var timeID = [];
function getAdventure() {
    let building = "Adventures";
    let ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            let responseText = this.responseText;
            console.log(responseText);
            document.getElementById("sidebar").getElementsByTagName("div")[0].innerHTML = responseText;
        }
    };
    ajaxRequest.open('GET', "handlers/handler_v.php?" + "&building=" + building);
    ajaxRequest.send();
}
function checkCombatCalculator() {
    let data = "model=combatTest" + "&method=test";
    ajaxP(data, function (response) {
        if (response[0] !== false) {
            console.log(response[1]);
            let responseText = response[1];
            clientOverlayInterface.show(responseText.html);
        }
    });
}
