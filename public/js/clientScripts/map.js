window.addEventListener("load", () => {
    map.load();
    document.getElementById("toggle_world_image").addEventListener("click", () => map.toggleMapType());
    document.getElementById("map_image_overlay").addEventListener("click", () => map.toggleMapType());
    // Add events to both toggle map button on canvas and the close button in map container
    document.getElementById("toggle_map_button").addEventListener("click", map.toggle);
    document.getElementById("close_map_button").addEventListener("click", map.toggle);
});

const map = {
    mapElement: document.getElementById("map_world_img_container"),
    fontProperties: {
        city: 32,
        placeName: 24
    },
    load() {
        this.drawTags();
        this.checkImages();
    },
    checkImages() {
        let images = document.getElementsByClassName("world_img");
        console.log(images);
        for(let i = 0; i < images.length; i++) {
            let image = new Image();
            var current = images[i];
            let childIndex = [...current.parentNode.children].indexOf(current);
            image.addEventListener("error", function() {
                console.log(current);
                let div = document.createElement("div");
                div.className = "world_img_placeholder";
                document.getElementById("map_world_img_container").replaceChild(div,
                                        document.getElementById("map_world_img_container").children[childIndex]);
            });
            image.src = images[i].src;
        }
    },
    textTag: [
       {"id": 1, "x": 50, "y": 30, "text": "Towhar", "type": "city", "mapParent": "5.7"},
       {"id": 2, "x": 50, "y": 30, "text": "Golbak", "type": "city", "mapParent": "3.5"},
       {"id": 3, "x": 50, "y": 20, "text": "Fansal Plains", "type": "city", "mapParent": "3.3"},
       {"id": 4, "x": 50, "y": 20, "text": "Snerpiir", "type": "city", "mapParent": "5.5"},
       {"id": 4, "x": 50, "y": 40, "text": "Hirtam", "type": "city", "mapParent": "4.9"},
       {"id": 4, "x": 50, "y": 40, "text": "Pvitul", "type": "city", "mapParent": "2.9"},
       {"id": 4, "x": 50, "y": 40, "text": "Cruendo", "type": "city", "mapParent": "6.6"},
       {"id": 4, "x": 50, "y": 40, "text": "Wilsnas point", "type": "placeName", "mapParent": "6.7"},

    ],
    mapType: "local",
    drawTags() {
        for(const i of this.textTag) {
            let mapParentNumbers = i.mapParent.split(".");
            let textTag = document.createElement("span");
            textTag.className = "mapTextTag";
            textTag.style.left = i.x + ((parseInt(mapParentNumbers[0]) - 1) * 200) + "px";
            textTag.style.top = i.y + ((parseInt(mapParentNumbers[1]) - 1) * 200) + "px";
            textTag.innerText = i.text;
            textTag.style.fontSize = this.fontProperties[i.type].size + "px";
            this.mapElement.appendChild(textTag);
        }
   },
   toggle () {
        let mapContainer = document.getElementById("map_container");
        if(mapContainer.style.display != "block") {
            mapContainer.style.display = "block";
        }
        else {
            mapContainer.style.display = "none";
        }
    },
    toggleMapType() {
        if(this.mapType === "local") {
            document.getElementById("map_local_img_container").style.display = "none";
            document.getElementById("map_world_img_container").style.display = "grid";
            this.mapType = "world";
            document.getElementById("map_image_overlay").style.visibility = "visible";
        }
        else {
            document.getElementById("map_local_img_container").style.display = "block";
            document.getElementById("map_world_img_container").style.display = "none";
            this.mapType = "local";
            document.getElementById("map_image_overlay").style.visibility = "hidden";
        }
    }
}