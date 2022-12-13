function clock() {
    var today = new Date();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();
    // minutes = checkZero(minutes);
    // seconds = checkZero(seconds);
    let container = document.getElementById("clock");
    if (container) {
        container.innerHTML = hours + ":" + minutes + ":" + seconds;
    }
    setTimeout(clock, 1000);
}
function checkZero(i: number) {
    if (i < 10) {
        i = 0 + i;
    } //
    return i;
}
clock();
