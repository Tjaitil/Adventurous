window.addEventListener('load', () => backgroundImageSlider());
function backgroundImageSlider() {
    let direction = -1;
    requestAnimationFrame(animate);
    let duration = 0;
    function animate() {
        // If 2 frames have gone by, animate
        if(duration % 2 === 0) {
            // Fits best with a square image. Preferrably a map image 3200 x 3200
            let imageElement = document.getElementById("background_image");
            let leftStyle = imageElement.offsetLeft;
            // If leftStyle is at the right edge change direction back
            if(direction == -1 && leftStyle < - (imageElement.offsetWidth - screen.width)) {
                direction = + 1;
            }
            else if(direction == 1 && leftStyle > - 100) {
                direction = - 1;
            }
            imageElement.style.left = (leftStyle + direction) + "px";
        }
        requestAnimationFrame(animate);
    }
}