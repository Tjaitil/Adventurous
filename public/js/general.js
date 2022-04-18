scriptLoader.loadScript(['itemTitle', 'uppercase'], 'utility');

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
    itemTitle.addTitleEvent();
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
                span[i].style.left = "-20%";
                span[i].style.display = "block";
            }
        }
    }
    if (location.href.indexOf("advclient") != -1) {
        let linksDiv = document.querySelectorAll(".top_bar");
        linksDiv.forEach((element, index) => {
            // If the device is mobile the first a is not displayed
            if(linksDiv.length - 1 === index) return;
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
function displayNav() {
    var visibility = document.getElementById("nav_2").children[1].style.visibility;
    if (visibility == 'visible') {
        document.getElementById("nav_2").children[1].style.visibility = "hidden";
    }
    else {
        document.getElementById("nav_2").children[1].style.visibility = "visible";
    }
}
function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
function getgMessage() {
    ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            gameLog(this.responseText);
        }
    };
    ajaxRequest.open('GET', "handlers/handler_ses.php?variable=game_message");
    ajaxRequest.send();
}
/*window.addEventListener("load", getgMessage, false);*/
function openNews(content, sidebar = false) {
    document.getElementById("news_content_main_content").innerHTML = "";
    document.getElementById("news_content").style.top = game.properties.rootCanvas.offsetTop + "px";

    document.getElementById("news").style.visibility = "visible";
    document.getElementById("news_content").style.visibility = "visible";
    if (typeof content == 'object') {
        if (content.innerText.indexOf("Loading") != -1) {
            document.getElementById("news_content_main_content").appendChild(content);
        }
        else if (Object.keys(content).length > 1) {
            for (var i = 0; i < Object.keys(content).length; i++) {
                document.getElementById("news_content_main_content").appendChild(content[i]);
            }
        }
    }
    else {
        document.getElementById("news_content_main_content").innerHTML = content;
    }
    if (sidebar == true) {
        newsContentSidebar.adjustSidebar();
    }
}
function closeNews() {
    var newsDiv = document.getElementById("news_content_main_content");
    if(document.getElementsByClassName("page_title")[0] !== undefined) {
        if (document.getElementsByClassName("page_title")[0].innerText == 'merchant') {
            updateStockCountdown('end');
        }
        // Remove stck_menu after stockpile has been exited
        if (document.getElementsByClassName("page_title")[0].innerText == 'Stockpile' &&
            document.getElementById("stck_menu").style.visibility === "visible") {
            document.getElementById("news_content").removeChild(document.getElementById("stck_menu"));
        }
        // Remove event on inventory items after exiting tavern
        if (document.getElementsByClassName("page_title")[0].innerText == 'tavern') {
            let figures = document.getElementById("inventory").querySelectorAll('figure');
            figures.forEach(function (element) {
                element.removeEventListener('click', getHealingAmount);
            });
        }
    }
    newsDiv.innerHTML = "";
    news.style = "visibility: hidden;";
    document.getElementById("news_content").style.visibility = "hidden";
    document.getElementById("news_content").style.top = "200px";
    if (typeof game.properties.inBuilding !== 'undefined') {
        // Render the player outside building
        /*renderPlayer(0, 40);*/
        game.properties.inBuilding = false;
    }
    if (selectItemEvent.selectItemStatus === true) {
        selectItemEvent.removeSelectEvent();
    }
    if (typeof (menubarToggle) !== 'undefined') {
        menubarToggle.removeEvent();
    }
    if (itemTitle.status === false) {
        (() =>itemTitle.addTitleEvent())();
    }
}
const mainContentHelpContainer = {
    helpElement: null,
    toggled: false,
    toggle() {
        if(this.toggled === false) {
            this.helpElement.style.height = "250px";
            this.toggled = true;
        }
        else {
            this.helpElement.style.height = "0px";
            this.toggled = false;
        }
    }
}
const inventorySidebarMob = {
    toggleInventory() {
        let inventory = document.getElementById("inventory");
        if (inventory.style.visibility === "hidden") {
            console.log('hlelo');
            inventory.style.width = "50%";
            inventory.style.visibility = "visible";
        }
        else {
            if (inventory.querySelectorAll("#stck_menu").length > 0) {
                inventory.querySelectorAll("#stck_menu")[0].style.visibility = "hidden";
            }
            inventory.style.width = "10%";
            inventory.style.visibility = "hidden";
        }
    }
};
const newsContentSidebar = {
    activeButton: null,
    adjustSidebar() {
        let news_content_main = document.getElementById("news_content_main_content");
        if (news_content_main.style.width === "100%") {
            news_content_main.style.width = "75%";
            document.getElementById("news_content_side_panel").style.width = "25%";
            document.getElementById("news_content_side_panel").style.display = "";
        }
        document.getElementById("news_content_side_panel").innerHTML = "";
        let divChildren = news_content_main.children;
        let exceptions = ["put_on", "mission_enabled", "current_mission", "persons", "stck_menu", "battle-result"];
        let buttonCount = 0;
        let divFirst = false;
        for (let i = 0; i < divChildren.length; i++) {
            if (divChildren[i].tagName === 'DIV' && exceptions.indexOf(divChildren[i].id) == -1) {
                console.log(divChildren[i]);
                divChildren[i].style.visibility = "hidden";
                divChildren[i].style.display = "none";
                let button = document.createElement("button");
                // Get text from div id and remove underscore and Uppercase character of each word;
                let text = document.createTextNode(jsUcWords(underscoreTreatment(divChildren[i].id, false)));
                button.appendChild(text);
                button.classList.add("building-tab");
                document.getElementById("news_content_side_panel").appendChild(button);
                if(divFirst === false) {
                    divChildren[i].style.visibility = "visible";
                    divFirst = true;
                    this.activeButton = button.innerText;
                    this.showContent(true);
                        window.setTimeout(() => {this.adjustMainContentHeight()}, 500);
                }
                divChildren[i].style.width = "100%";
                buttonCount++;
            }
        }
        if(buttonCount == 1) {
            // Remove padding
            document.getElementById("news_content_main_content").style.paddingRight = "0px";
            // If first div is persons then show second div ([2] in the child tree, [0] is title, [1] is persons div)
            if (news_content_main.querySelectorAll("div")[0].id == "persons") {
                news_content_main.children[2].style.visibility = "visible";
                news_content_main.children[2].style.position = "";
                document.getElementById("news_content_main_content").style.height =
                    document.getElementById("news_content_main_content").querySelectorAll("div")[0].offsetHeight + 110 +
                    news_content_main.children[2].offsetHeight + "px";
            }
            else {
                // document.getElementById("news_content_main_content").querySelectorAll("div")[0].getBoundingClientRect();
                // document.getElementById("news_content_main_content").style.height =
                //     document.getElementById("news_content_main_content").querySelectorAll("div")[0].offsetHeight + 40 + "px";
                console.log(document.getElementById("news_content_main_content").querySelectorAll("div")[0].offsetHeight);
                console.log(document.getElementById("news_content_main_content").querySelectorAll("div")[0]);
            }
            news_content_main.style.width = "100%";
            document.getElementById("news_content_side_panel").style.display = "none";
        }
        else {
            this.addEvent();
            if (document.getElementById("news_content_main_content").style.paddingRight == 0) {
                document.getElementById("news_content_main_content").style.paddingRight = "8px";
            }
        }
    },
    showContent(eventButton = false) {
        if(eventButton === false) this.activeButton = event.target.innerText;
        let buttons = document.getElementsByClassName("building-tab");
        for (var i = 0; i < buttons.length; i++) {
            if (buttons[i].innerText == this.activeButton) {
                buttons[i].style.backgroundColor = "#474700";
                // document.getElementById(underscoreTreatment(buttons[i].innerText, true).toLowerCase()).style.position = "";
                document.getElementById(underscoreTreatment(buttons[i].innerText, true).toLowerCase()).style.visibility = "visible";
                document.getElementById(underscoreTreatment(buttons[i].innerText, true).toLowerCase()).style.display = "block";
            }
            else {
                buttons[i].style.backgroundColor = "";
                document.getElementById(underscoreTreatment(buttons[i].innerText, true).toLowerCase()).style.visibility = "hidden";
                document.getElementById(underscoreTreatment(buttons[i].innerText, true).toLowerCase()).style.display = "none";

            }
        }
        if (document.getElementById("form_cont") !== null && document.getElementById("form_cont").style.display == "block") {
            document.getElementById("form_cont").style.display = "none";
        }
        newsContentSidebar.adjustMainContentHeight();
    },
    adjustMainContentHeight(first = false) {
        document.getElementById("news_content_main_content").getBoundingClientRect();
        if(this.activeButton === 'Overview') {
            let number = document.querySelectorAll(".warrior").length;
            document.getElementById("news_content_main_content").style.height = (Math.ceil(number / 3) * 390) + 130 + "px";
        }
        else {
            let div = underscoreTreatment(this.activeButton, true).toLowerCase();
            let test = document.getElementById(div).getBoundingClientRect();
            let visibleChildren = [...document.getElementById("news_content_main_content").children].filter(element => {
                return(element.style.visibility !== "hidden" && element.style.display !== "none");
            });
            let totalHeight = 0;
            visibleChildren.forEach(element => totalHeight += element.offsetHeight);
            console.log(totalHeight);
            document.getElementById("news_content_main_content").style.height = totalHeight + "px";
        }
    },
    addEvent() {
        let buttons = [...document.getElementsByClassName("building-tab")];
        buttons.forEach(function (element) {
            // Add eventListener to each node
            element.addEventListener('click', () => newsContentSidebar.showContent());
        });
    }
};
function checkGameMessages(echoData) {
    if(echoData['gameMessages'] !== "undefined") {
        echoData['gameMessages'].forEach((message) => {
            gameLog(gameInfo.gameMessages[message]);
        });
    }
}
function underscoreTreatment(string, addUnderscore) {
    let result = string.search("_");
    let editedString;
    if (result != -1 && addUnderscore === false) {
        editedString = string.replace("_", " ");
    }
    else if (result == -1 && addUnderscore === true) {
        editedString = string.replace(" ", "_");
    }
    else {
        return string;
    }
    return editedString;
}
function alertMessage(page, pIdentifier = false) {
    // pIdentifer is used to identify which content to append if this function is used multiple times on the same page
    var subButton = document.createElement("button");
    subButton.appendChild(document.createTextNode("Submit"));
    subButton.setAttribute("id", "alert_submit");
    var cancButton = document.createElement("button");
    cancButton.appendChild(document.createTextNode("Cancel"));
    cancButton.setAttribute("id", "alert_cancel");

    var content = {};
    switch (page) {
        case 'citycentre':
            var link = document.createElement("a");
            link.appendChild(document.createTextNode("here"));
            link.setAttribute("href", "/gameguide/profiency");
            console.log(link);
            var p = document.createElement("p");
            var message =
                'Beware that changing profiency may result in lowering levels </br> and no access to profiency specific activites';
            var message2 = '</br> Read more on </br>';
            var message3 = "Are you sure you want to continue?";
            p.innerHTML = message + message2;
            p.appendChild(link);
            p.innerHTML += '</br>' + message3;
            content[0] = p;
            document.getElementById("alert_submit").addEventListener("click", function () {
                changeProfiency();
                closeNews();
            });
            break;
    }
    content[1] = subButton;
    content[2] = cancButton;
    openNews(content);

    document.getElementById("alert_cancel").addEventListener("click", closeNews);
    document.getElementById("cont_exit").addEventListener("click", closeNews);
}
function get_xp(skill) {
    let divs = document.getElementById("skills").querySelectorAll("div");
    // Used when player are closing sidebar and one of the skill tooltip is visible
    if (skill == false) {
        for (let x = 0; x < divs.length; x++) {
            divs[x].children[1].style.visibility = "hidden";
        }
        return false;
    }
    const skillDivs = {
        "adventurer": 0,
        "farmer": 1,
        "miner": 2,
        "trader": 3,
        "warrior": 4
    }

    let element = event.target.closest("div");

    for (let i = 0; i < divs.length; i++) {
        if (skillDivs[skill] != i) {
            divs[i].children[1].style.visibility = "hidden";
            console.log(divs[i].children[1]);
        }
    }
    console.log(skill);
    let tooltip = element.querySelectorAll(".skill_tooltip")[0];
    tooltip.style.right = "-30%";
    if (tooltip.style.visibility == "visible") {
        tooltip.style.visibility = "hidden";
        return false;
    }
    else if (skill == 'adventurer') {
        tooltip.innerHTML = skill.charAt(0).toUpperCase() + skill.slice(1);
        tooltip.style.visibility = "visible";
    }
    else {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                data.shift();
                let skillName = jsUcfirst(skill);
                /*element.title = skillName + '\n' + "Current xp: " + data[0] + '\n' + "Next level: " + data[1];*/
                tooltip.innerHTML = skillName + '</br>' + "Current xp: " + data[0] + '</br>' + "Next level: " + data[1];
                tooltip.style.visibility = "visible";
            }
        };
        ajaxRequest.open('GET', "handlers/handler_ses.php?variable=" + skill);
        ajaxRequest.send();
    }
}
function show_xp(skill, xp) {
    var elements = {
        adventurer: 0,
        farmer: 1,
        miner: 2,
        trader: 3,
        warriors: 4
    };
    var element = document.getElementById("skills").children[elements[skill]].children[2];
    element.innerHTML = "+" + xp;
    setTimeout(hide_xp, 2000, element);
}
function hide_xp(element) {
    element.innerHTML = "";
}
var timeID = [];
function show_title() {
    let element = event.target.closest("div");
    let item = element.getElementsByTagName("figcaption")[0].innerHTML;
    let menu = document.getElementById("item_tooltip");
    if(menu.children[0].children[0].innerHTML === item) {
        return false;
    }
    // Insert item name at the first li
    menu.children[0].children[0].innerHTML = item;
    menu.style.visibility = "visible";
    // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
    let menuTop;
    document.getElementById("tooltip_item_price").innerHTML = itemPrices.findItem(item);
    if (element.className == "inventory_item") {
        document.getElementById("inventory").insertBefore(menu,
            document.getElementById("inventory").querySelectorAll(".inventory_item")[0]);
        // menuTop = element.offsetTop + 30;
        menuTop = element.offsetTop + 15;
        console.log(menuTop);
        menu.children[0].style.top = menuTop + "px";
        menu.children[0].children[0].style.textAlign = "center";
        if (item.length < 8) {
            menu.children[0].style.left = element.offsetLeft + 20 + "px";
        }
        else {
            menu.children[0].style.left = element.offsetLeft + 10 + "px";
        }
    }
    else {
        let elementParent = element.closest("div");
        let firstChild = elementParent.children[0];
        elementParent.appendChild(menu);
        menu.children[0].style.left = 10 + "px";
        menu.children[0].style.top = 55 + "px";
    }
    // If element is inside inventory or stockpile
    /*if(["stockpile", "inventory"].indexOf(element.parentNode.id) != -1) {
        //menuTop = element.offsetTop + element.parentNode.scrollTop + 50;
        menuTop = element.offsetTop + 30;
        console.log(menuTop);
        menu.children[0].style.top = menuTop + "px";
        if(item.length < 8) {
            menu.children[0].style.left = element.offsetLeft +  20 + "px";
            menu.children[0].children[0].style.width = "50px";
            menu.children[0].children[0].style.textAlign = "center";
        }
        else {
            menu.children[0].style.left = element.offsetLeft + 10 + "px";
            menu.children[0].children[0].style.width = "auto";
        }
    }
    else {
        var elementPos = element.getBoundingClientRect();
        menuTop = document.documentElement.scrollTop + elementPos.y - 164;
        console.log("element.offsetTop: " + element.offsetTop);
        console.log("element.parentNode.scrollTop" + element.parentNode.scrollTop);
        console.log("html srollTop:" + document.documentElement.scrollTop);
        console.log(menuTop);
        console.log(document.documentElement);
        console.log(element.getBoundingClientRect());
        menu.children[0].style.top = menuTop + "px";
        menu.children[0].style.left = element.offsetLeft + 10 + "px";
        console.log(menu);
    }*/

    /*setTimeout(hide_title, 4000, element);*/
}
function hide_title(element) {
    /*element.style.visibility = "hidden";*/
    if (data[1] == true) {
        var div_button = data[2].parentElement.children[0];
        div_button.style = "visibility: hidden";
    }
}
var sidebar = {
    sidebarElement: document.getElementById("sidebar"),
    sidebarToggled: false,
    addClickEvent() {
        // Get every button and add event;
        let sidebarTabs = document.getElementById("sidebar").querySelectorAll("button");
        sidebarTabs.forEach(function (element) {
            // Find first button and not append event to it
            if (element.previousElementSibling == null) {
                return;
            }
            element.addEventListener('click', function () {
                sidebar.showTab(sidebarCheck = true);
            });
        });
        this.sidebarElement = document.getElementById("sidebar");
    },
    toggleSidebar() {
        if (this.sidebarToggled === false) {
            this.sidebarElement.style.visibility = "visible";
            this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.40 + "px";
            this.sidebarToggled = true;
            document.getElementById("sidebar_button_toggle").style.visibility = "visible";
            if (window.screen.width < 830) {
                document.getElementById("sidebar_button_toggle").style.cssFloat = "right";
                console.log(document.getElementById("sidebar_button_toggle").style.cssFloat);
            }
        }
        else {
            // Hide all bars
            sidebar.showTab(sidebarCheck = false);
            get_xp(false);
            if (window.screen.width < 830) {
                this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.12 + "px";
            }
            else {
                this.sidebarElement.style.width = document.getElementsByTagName("aside")[0].clientWidth + "px";
            }
            this.sidebarToggled = false;
            if (window.screen.width > 830) {
                document.getElementById("sidebar_button_toggle").style.visibility = "hidden";
            }
            else {
                setTimeout(function () {
                    document.getElementById("sidebar").style.visibility = "hidden";
                    document.getElementById("sidebar_button_toggle").style.cssFloat = "left";
                }, 200);
            }
        }
    },
    showTab(sidebarCheck) {
        console.log(sidebarCheck);
        if (this.sidebarToggled === false && sidebarCheck === true) {
            this.toggleSidebar();
        }
        let newActiveTab = event.target.innerText;
        console.log(newActiveTab);
        let tabs = document.getElementById("sidebar").querySelectorAll(".sidebar_tab");
        let tabNames = ["Adventure", "Countdowns", "Diplomacy", "Skills"];
        for (var i = 0; i < tabNames.length; i++) {
            if (newActiveTab.includes(tabNames[i])) {
                if(newActiveTab !== 'Skills') {
                    get_xp(false);
                }
                tabs[i].style.visibility = "visible";
            }
            else {
                tabs[i].style.visibility = "hidden";
            }
        }
    }
};
function getAdventure() {
    let building = "Adventures";
    ajaxRequest = new XMLHttpRequest();
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
    console.log('started');
    let data = "model=combatTest" + "&method=test";
    ajaxP(data, function(response) {
        if(response[0] !== false) {
            console.log(response[1]);
            let responseText = response[1];
            openNews(responseText.html, true);
        }
    });
}