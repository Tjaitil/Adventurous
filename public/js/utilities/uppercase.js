function jsUcfirst(string) {
    // Return uppercase first letter
    return string.charAt(0).toUpperCase() + string.slice(1);
}
function jsUcWords(str) {
    // Return uppercase first letter each word
    return str.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}