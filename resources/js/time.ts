function clock() {
    const today = new Date();
    const hours = today.getHours();
    const minutes = today.getMinutes();
    const seconds = today.getSeconds();
    // minutes = checkZero(minutes);
    // seconds = checkZero(seconds);
    const container = document.getElementById('clock');
    if (container) {
        container.innerHTML = hours + ':' + minutes + ':' + seconds;
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
