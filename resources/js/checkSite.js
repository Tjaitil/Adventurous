     window.addEventListener("DOMContentLoaded", function() {
        // Make section cover whole width of page by setting the gridColumn to 1 / 3 from 2 / 3. See layout.css
        if(document.getElementsByTagName("aside").length == 0) {
            document.getElementsByTagName("body")[0].id = "wrapperOneColumn";
            document.getElementsByTagName("section")[0].style.gridColumn = "1 / span 2";
            document.getElementsByTagName("footer")[0].style.gridColumn = "1 / span 2";
        }
        else {
            return false;
        }
    });