    var c = 0;
    
    var timer = 0;

    function chat() {
        console.log("chat");
        var input = document.getElementById("text");
        var text = input.value;
        input.value = "";
        if(text.length == 0) {
            return false;
        }
        timer = 1;
        var data = "model=Main" + "&method=Chat" + "&message=" + text;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateScroll(response[1]);
            }       
        });
        timer = 0;
    }
    function getChat() {
        var chat = document.getElementById("chat");
        chat.scrollTop = chat.scrollHeight - chat.clientHeight;
        if(timer != 0) {
            return false;
        }
        chat = document.getElementById("chat").children[0].lastElementChild.innerHTML.match(/\[(.*)\]/).pop();
        var element = chat[0];
        var data = "model=Main" + "&method=getChat" + "&clock=" + element;
        ajaxG(data, function(response) {
           updateScroll(response[1]); 
        });
    }
    
    function updateScroll(messages) {
        var chat = document.getElementById("chat");
        var isScrolledToBottom = chat.scrollHeight - chat.clientHeight <= chat.scrollTop + 1;
        document.getElementById("chat").children[0].innerHTML += messages;
        // scroll to bottom if isScrolledToBotto
        if(isScrolledToBottom) {
          chat.scrollTop = chat.scrollHeight - chat.clientHeight;
        }
    }   
    /*setInterval(getChat, 2000);*/
    window.onload = function () {
        getChat();
        getXP();
        // Check for browser, and return error message for not optimized browser
        if(/Safari|Chrome|Firefox/i.test(navigator.userAgent) == false) {
            alert("WARNING! \n This game is not tested and optimized for the browser you are using." +
                  " Recommended browsers are safari, chrome or firefox");
        }
    };
    function getXP() {
        var data = "&model=gamedata" + "&method=getXP";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var x_value1 = Number(data[0]);
                var x_value2 = Number(data[1]);
                var width = x_value1 / x_value2 * 100;
                document.getElementById("skill_bar2").style.width = width + "%"; 
            }
        }); 
    }
    var data = [];
    
    function add() {
      console.log('ADD');
      data.push(number1);
      data.push(number2);
      console.log(data);
    }
    
    
    function callFunction() {
        for(let i = 0; i < data.length; i++) {
            console.log(data[i]);
            data[i]();
        }
    }
    function zoom() {
        let images = document.getElementById("map").querySelectorAll(".map_img");
        console.log(images);
        for(var i = 0; i < images.length; i++) {
            console.log(images[i].style.width);
            if(images[i].style.width === "") {
                images[i].style.width = "400px";
                images[i].style.height = "400px";
            }
            else {
                images[i].style.width = 400;
                images[i].style.height = 400; 
            }
            console.log(images[i].style.width);
        }
        document.getElementById("map").style.width = 9 * 400 + "px";
    }
    
    
    function number1() {
      console.log(1);
    }
    function number2() {
      console.log(2);
    }
    function number3() {
      console.log(3);
    }