CookieTicket = {
    checkCookieTicket(cookieNoob = "getOut") {
        var today = new Date();
        var cookieTicket;
        if(CookieTicket.sweetCookie === null) {
            cookieTicket = today.getMonth() + today.getDate() + "|";
            for(var i = 0; i < 10; i++) {
                cookieTicket += Math.floor(Math.random() * (10 - 1) + 1);
            }
            CookieTicket.sweetCookie = cookieTicket;
        }
        else {
            cookieTicket = CookieTicket.sweetCookie;
        }
        let data = "model=cookieMaker" + "&method=yummyCookies" + "&cookieTicket=" + cookieTicket + "&cookieNoob=" + cookieNoob;
        ajaxP(data, function(response) {
            console.log(response);
            console.log(response[1]);
            if(response[1] === "false") {
                // Session check return false, go to logout
                location.href = "/logout";
            }
            else {
                return;
            }
        });
    },
    disposeGarbage() {
        let ego = event.target.innerText;
        if(ego === "") {
            checkCookieTicket();
        }
        else {
            burntCookie();
        }
    },
    burntCookie() {
        let data = "model=cookieMaker" + "&method=delicCookies" + "&cookieTicket=" + CookieTicket.sweetCookie;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[1] == false) {
                window.cancelAnimationFrame(game.properties.requestId);
                game.loadWorld(false);
            }
            else {
                return;
            }
        });    
    },
    sweetCookie: null,
};