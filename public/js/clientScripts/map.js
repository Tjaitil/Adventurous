window.addEventListener("load", function () {
    // Add events to both toggle map button on canvas and the close button in map container
    document.getElementById("toggle_map_button").addEventListener("click", map.toggle);
    document.getElementById("close_map_button").addEventListener("click", map.toggle);
});
map = {
    toggle () {
        let mapContainer = document.getElementById("map_container");
        if(mapContainer.style.display != "block") {
            mapContainer.style.display = "block";
        }
        else {
            mapContainer.style.display = "none";
        }
    }
};