window.addEventListener("load", () => {
    document.getElementById("toggle_world_image").addEventListener("click", () => map.toggleMapType());
    document.getElementById("map_type_toggle_overlay").addEventListener("click", () => map.toggleMapType());
    document.getElementById("toggle_icon_list_image").addEventListener("click", () => map.toggleIconList());
    // Add events to both toggle map button on canvas and the close button in map container
    document.getElementById("toggle_map_icon").addEventListener("click", () => map.toggle());
    document.getElementById("close_map_button").addEventListener("click", map.toggle);
});

const map = {
    mapElement: document.getElementById("map_world_img_container"),
    fontProperties: {
        city: 32,
        placeName: 24
    },
    IconListVisible: false,
    player: {"tagType": "dot", "visible": false, x: 0, y: 0},
    localMapTags: [
        {"tagType": "img", "src": "pesr", "icon": "travel icon", "visible": false, x: 0, y: 0},
        {"tagType": "img", "src": "sailor", "icon": "boat travel icon", "visible": false, x: 0, y: 0}
    ],
    mapTag: [
        {"id": 1, "x": 50, "y": 30, "text": "Towhar", "type": "city", "mapParent": "5.7", "tagType": "text"},
        {"id": 2, "x": 50, "y": 50, "text": "Golbak", "type": "city", "mapParent": "3.5", "tagType": "text"},
        {"id": 3, "x": 50, "y": 20, "text": "Fansal Plains", "type": "city", "mapParent": "4.3", "tagType": "text"},
        {"id": 4, "x": 50, "y": 50, "text": "Snerpiir", "type": "city", "mapParent": "5.5", "tagType": "text"},
        {"id": 4, "x": 50, "y": 50, "text": "Ter", "type": "city", "mapParent": "6.3", "tagType": "text"},
        {"id": 4, "x": 50, "y": 50, "text": "Khanz", "type": "city", "mapParent": "8.2", "tagType": "text"},
        {"id": 4, "x": 50, "y": 40, "text": "Hirtam", "type": "city", "mapParent": "4.9", "tagType": "text"},
        {"id": 4, "x": 50, "y": 40, "text": "Pvitul", "type": "city", "mapParent": "2.9", "tagType": "text"},
        {"id": 4, "x": 50, "y": 40, "text": "Cruendo", "type": "city", "mapParent": "6.6", "tagType": "text"},
        {"id": 4, "x": 50, "y": 40, "text": "Fagna", "type": "city", "mapParent": "7.5", "tagType": "text"},
        {"id": 4, "x": 50, "y": 40, "text": "Krasnur", "type": "city", "mapParent": "3.6", "tagType": "text"},
        {"id": 4, "x": 50, "y": 40, "text": "Tasnobil", "type": "city", "mapParent": "2.6", "tagType": "text"},
        {"id": 4, "x": 100, "y": 90, "text": "Wilsna´s point", "type": "placeName", "mapParent": "8.4", "tagType": "text"},
        {"id": 4, "x": 150, "y": 100, "text": "Heskils mountains", "type": "placeName", "mapParent": "4.4", "tagType": "text"},
        {"id": 4, "x": 100, "y": 90, "text": "Tibs pass", "type": "placeName", "mapParent": "7.3", "tagType": "text"},
        {"id": 4, "x": 120, "y": 140, "src": "combat icon", "type": "icon", "mapParent": "4.2", "tagType": "img"},
        {"id": 4, "x": 50, "y": 40, "src": "combat icon", "type": "icon", "mapParent": "3.10", "tagType": "img"},
        {"id": 4, "x": 50, "y": 80, "src": "combat icon", "type": "icon", "mapParent": "8.3", "tagType": "img"},
        {"id": 4, "x": 50, "y": 80, "src": "combat icon", "type": "icon", "mapParent": "6.2", "tagType": "img"}
    ],
    mapType: "local",
    load(currentMap) {
        if(document.getElementById("local_img")) {
            document.getElementById("local_img").src = "public/images/" + currentMap + "m.png";
        }
        this.drawTags();
        this.checkImages();
        this.loadLocalMapTags();
    },
    loadLocalMapTags() {
        this.localMapTags.forEach((element) => {
            let pos = this.findLocalMapTags(element.src, 'character');
            if(pos[0] !== undefined) {
                element.visible = true;
                element.x = pos[0];
                element.y = pos[1];
            }
            else {
                element.visible = false;
                element.x = 0;
                element.y = 0;
            }
        });
        this.drawLocalTags();
    },
    locatePlayerMarker() {
        let playerMarker = document.getElementById("map_player_marker");
        let playerX = gamePieces.player.xpos;
        let playerY = gamePieces.player.ypos;
        if(this.mapType === "local") {
            if(playerMarker.parentElement.id !== "map_local_img_container") {
                document.getElementById("map_world_img_container").removeChild(playerMarker);
                document.getElementById("map_local_img_container").appendChild(playerMarker);
            } else if(!playerMarker.parentElement.id) {
                document.getElementById("map_local_img_container").appendChild(playerMarker);
            }
            playerX /= 2;
            playerY /= 2;
        }
        else if(this.mapType === "world") {
            if(playerMarker.parentElement.id !== "map_world_img_container") {
                // document.getElementById("map_local_img_container").removeChild(playerMarker);
                document.getElementById("map_world_img_container").appendChild(playerMarker);
            }
            let map = game.properties.currentMap.split(".");
            playerX = (playerX / 16) + (parseInt(map[0]) - 1) * 200 - 12.5;
            playerY = (playerY / 16) + (parseInt(map[1]) - 1) * 200 - 12.5;
        }
        playerMarker.style.top = playerY + "px";
        playerMarker.style.left = playerX + "px";
    },
    findLocalMapTags(variable, type) {
        if(type === "character") {
            let object = gamePieces.objects.filter((object) => object.type === "character" && object.src.indexOf(variable) !== -1);
            if(object.length > 0) object = object[0];
            return [object.x, object.y];
        }
        else {
        }
   },
    checkImages() {
        let images = document.getElementsByClassName("world_img");
        for(let i = 0; i < images.length; i++) {
            let image = new Image();
            var current = images[i];
            let childIndex = [...current.parentNode.children].indexOf(current);
            image.addEventListener("error", () => {
                image.src = "public/images/1.10m.png";
                document.getElementById("map_world_img_container").children[childIndex].src = "public/images/1.10m.png";
            });
            image.src = images[i].src;
        }
    },
    drawTags() {
        for(const i of this.mapTag) {
            let mapParentNumbers = i.mapParent.split(".");
            let tag;
            if(i.tagType === "text") {
                tag = document.createElement("span");
                tag.className = "mapTextTag ";
                tag.style.fontSize = this.fontProperties[i.type].size + "px";
                tag.innerText = i.text;
            }
            else {
                tag = document.createElement("img");
                tag.src = "public/images/" + i.src + ".png";
            }
            tag.className += "mapTag";
            tag.style.left = i.x + ((parseInt(mapParentNumbers[0]) - 1) * 200) + "px";
            tag.style.top = i.y + ((parseInt(mapParentNumbers[1]) - 1) * 200) + "px";
            this.mapElement.appendChild(tag);
        }
   },
   drawLocalTags() {
        let tags = document.getElementById("map_local_img_container").querySelectorAll(".localTag");
        if(tags.length > 0) {
            for(let i = 0; i < tags.length; i++) {
                document.getElementById("map_local_img_container").removeChild(tags[i]);
            }
        }
        for(const i of this.localMapTags) {
            if(i.visible === false) {
                continue;
            }
            let tag;
            tag = document.createElement("img");
            tag.src = "public/images/" + i.icon + ".png";
            tag.className = "mapTag localTag";
            // Divide by 2 because the image is styled 1600px by 1600px. map.css -> #local_img
            tag.style.left = (i.x / 2) + "px";
            tag.style.top = (i.y / 2) + "px";
            document.getElementById("map_local_img_container").appendChild(tag);
        }
   },
   toggle () {
        let mapContainer = document.getElementById("map_container");
        if(mapContainer.style.visibility != "visible") {
            mapContainer.style.visibility = "visible";
            mapContainer.style.left = "0px";
        } else {
            document.getElementById("map_icon_list").style.visibility = "hidden";
            mapContainer.style.visibility = "hidden";
            map_container.style.left = map_container.offsetWidth + "px";
        }
    },
    toggleIconList() {
        // Show sidebar
        if(this.IconListVisible) {
            document.getElementById("map_icon_list").style.width = "0%";
            setTimeout(() => document.getElementById("map_icon_list").style.visibility = "hidden", 200);

        } else {
            document.getElementById("map_icon_list").style.visibility = "visible";
            document.getElementById("map_icon_list").style.width = "30%";
        }
        this.IconListVisible = !this.IconListVisible;
    },
    toggleMapType() {
        if(this.mapType === "local") {
            document.getElementById("map_local_img_container").style.display = "none";
            document.getElementById("map_world_img_container").style.display = "grid";
            document.getElementById("map_type_toggle_overlay").style.visibility = "visible";
            this.mapType = "world";
            document.getElementById("map_container_header").querySelectorAll("h2")[0].innerText = jsUcfirst(this.mapType) + " map";
        }
        else {
            document.getElementById("map_local_img_container").style.display = "block";
            document.getElementById("map_world_img_container").style.display = "none";
            document.getElementById("map_type_toggle_overlay").style.visibility = "hidden";
            this.mapType = "local";
            document.getElementById("map_container_header").querySelectorAll("h2")[0].innerText = jsUcfirst(this.mapType) + " map";
        }
        this.locatePlayerMarker();
    }
}