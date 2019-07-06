    window.onload = function() {
        var count = 20;
        /*for(i=0; i<count; i++) {
            var element = document.createElement("DIV");
            element.setAttribute("id", [i]);
            document.getElementById("img").appendChild(element);
            document.getElementById(img+[i]).addEventListener("click", moveDiv(img+[i]));
            element.onclick = moveDiv;
        }*/
    };
    
    var pos = {
        '1.0' : true,
        '1.1' : true,
        '1.2' : true,
        '1.3' : true,
        '1.4' : true,
        '1.5' : true,
        '1.6' : true,
        '2.0' : true,
        '2.1' : true,
        '2.2' : true,
        '2.3' : true,
        '2.4' : true,
        '2.5' : true,
        '2.6' : true,
        '3.0' : true,
        '3.1' : true,
        '3.2' : true,
        '3.3' : true,
        '3.4' : true,
        '3.5' : true,
        '3.6' : false,
    };
    
    function moveDiv(id) {
        var el = document.getElementById(id);
        var rect = el.getBoundingClientRect();
        x = rect.left;
        y = rect.top;
        w = rect.width;
        h = rect.height;
        el.style.left = x + 1;
    }
    