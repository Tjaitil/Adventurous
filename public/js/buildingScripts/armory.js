const armoryModule = {
    init() {
        // selectitem.js
        selectItemEvent.addSelectEvent();
        document.getElementById("news_content_main_content").querySelectorAll("button")[0].addEventListener("click", function() {
           inputHandler.fetchBuilding('armycamp'); 
        });
        this.addClickEvents();
        document.getElementById("put_on_button").addEventListener("click", () => this.wearArmor());
    },
    addClickEvents(childIndex = false) {
        let elements;
        if(!childIndex) {
            elements = [...document.getElementsByClassName("armory_view_part")];
        } else {
            // Find armory_view_part inside warriorDiv that has childIndex
            let warriorDiv = document.getElementById("warrior_container").getElementsByClassName("armory_view")[childIndex];
            elements = [...warriorDiv.getElementsByClassName("armory_view_part")];
        }
        
        elements.forEach(element => 
            element.addEventListener("click", event => this.removeArmor(event))
        );
    },
    toggleOption() {
        let element = document.getElementById("selected").children[0].children[1].innerHTML;
        if(element.search("Sword") != -1 || element.search("Dagger") != -1) {
            document.getElementById("type").style.visibility = "visible";
        }
        else if(element.search("Arrow") != -1 || element.search("Knives") != -1) {
            document.getElementById("ranged_alt").style.visibility = "visible";
        }
        else {
            document.getElementById("type").style.visibility = "hidden";
        }
    },
    wearArmor() {
        let warrior_id = document.getElementById("select_warrior").selectedIndex;
        let element = document.getElementById("selected");
        let item = element.children[0].children[1].innerHTML;
        item = item.trim();
        let result = false;
        let minerals = ["Iron", "Steel", "Gargonite", "Adron", "Yeqdon", "Frajrite", "Oak", "Beech", "Yew"];
        let items = ["Sword", "Spear", "Dagger", "Shield", "Platebody", "Platelegs", "Helm", "Arrows", "Bow" , "Throwing", "Boots"];
        // Check out if the $item matches $mineral and $item
        let item_array = item.split(" ");
        if(minerals.indexOf(item_array[0]) == -1) {
            result = true;
        }
        if(items.indexOf(item_array[1]) == -1) {
            result = true;
        }
        if(result === true) {
            gameLogger.addMessage("ERROR: Select a valid item to wear!");
            gameLogger.logMessages();
            return false;
        }
        let select = document.getElementById("type");
        let hand;
        if(select.style.visibility == "visible") {
            hand = select.options[select.selectedIndex].value;
        }
        else {
            hand = false;
        }
        let rangedAmount = document.getElementById("ranged_alt");
        let amount;
        if(rangedAmount.style.visibility == "visible") {
            amount = rangedAmount.querySelectorAll("input")[0].value;
        }
        else {
            amount = false;
        }
        let data = "model=Armory" + "&method=wearArmor" + "&warrior_id=" + warrior_id + "&item=" + item  + "&hand=" +
                    hand + "&amount=" + amount;
        ajaxP(data, response => {
            if(response[0] != false) {
                let responseText = response[1];
                document.getElementById("selected").innerHTML = "";
                this.replaceWarriorContainer(responseText.html, warrior_id - 1);
                updateInventory('armory', true);
            }
        }, false);
    },
    removeArmor(event) {
        let partElement = event.currentTarget;
        if(!partElement) return false;
        let parent = partElement.closest(".armory_view");
        if(!parent.querySelectorAll(".armory_view_warrior_id")[0]) return false;
        let warrior_id = parent.querySelectorAll(".armory_view_warrior_id")[0].innerHTML.trim();
        let part = partElement.classList[1];
        let item = partElement.title;
        if(item === 'none') return false;
        let data = "model=Armory" + "&method=removeArmor" + "&warrior_id=" + warrior_id + "&part=" + part;
        ajaxP(data, response => {
            if(response[0] != false) {
                let responseText = response[1];
                this.replaceWarriorContainer(responseText.html, warrior_id - 1);
                updateInventory('armory', true);
            }
        });
    },
    replaceWarriorContainer(newContainer, replaceIndex) {
        let parentContainer = document.getElementById("warrior_container");
        let warriorContainer = document.getElementsByClassName("armory_view");
        // Convert newContainer string into object
        let div = document.createElement("div");
        div.innerHTML = newContainer;
        if(!warriorContainer[replaceIndex]) return false;
        parentContainer.replaceChild(div.getElementsByClassName("armory_view")[0], warriorContainer[replaceIndex]);
        this.addClickEvents(replaceIndex);
    },
    updatePage() {
        let data = "model=Armory" + "&method=getData";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                document.getElementById("warriors").innerHTML = response[1];
            }
        });
    },
    testCombatSkills(warriors) {
        // For testing purposes
        let data = "model=test" + "&method=loadCombat" + "&route=calculator" + "&warriors=" + JSON.stringify(warriors);
        ajaxP(data, function(response) {
            console.log(response);
        });
    }
};
export default armoryModule;