window.addEventListener('load', function() {
    backgroundImageSlider();
});
function backgroundImageSlider() {
    let direction = -1;
    let request = requestAnimationFrame(animate);
    let duration = 0;
    document.getElementById("background_image_container").style.height = screen.availHeight + "px";
    console.log(screen.availHeight);
    function animate() {
        // If 2 frames have gone by, animate
        if(duration % 2 === 0) {
            // Image is 3200 x 3200
            let imageElement = document.getElementById("background_image");
            let leftStyle = imageElement.offsetLeft;
            // If leftStyle is at the right edge change direction back
            if(direction == -1 && leftStyle < - (3100 - screen.width)) {
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