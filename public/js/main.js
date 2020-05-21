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
    };
    function getXP() {
        var data = "&model=gamedata" + "&method=getXP";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                console.log(response[1]);
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
    
    function number1() {
      console.log(1);
    }
    function number2() {
      console.log(2);
    }
    function number3() {
      console.log(3);
    }