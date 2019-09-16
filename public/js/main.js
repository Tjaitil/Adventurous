
                /*function hover() {
                    document.getElementsByClassName("a").style.boxShadow = "3px 3px 10px black";
                    document.getElementsByClassName("but").style.boxShadow = "3px 3px 10px black";
                }
                function unHover() {
                    document.getElementsByClassName("a").style.boxShadow = "3px 3px 5px black";
                    document.getElementsByClassName("but").style.boxShadow = "3px 3px 5px black";
                }
                /*document.getElementsByClassName("a").addEventListener("onmouseover", hover());
                document.getElementsByClassName("but").addEventListener("onmouseout", unhover());
                
                function showtroops () {
                    xmlhttp = new XMLHttpRequest();
                    xmlhttp.open("GET","/adventurous/gettroop.php?q=");
                    xmlhttp.send();
                    xmlhttp.onload = function (){
                        if(this.readyState == 4 && this.status == 200) {
                         document.getElementById("troop").innerHTML = this.responseText;   
                        }
                        };
                    
                }*/
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
            ajaxRequest = new XMLHttpRequest();
            var data = "model=Main" + "&method=Chat" + "&message=" + text;
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    updateScroll(this.responseText);
                }
            };
            ajaxRequest.open('POST', "handlers/handler_p.php");
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);
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
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    updateScroll(this.responseText);
                }
            };
            ajaxRequest.open('GET', "handlers/handler_g.php?model=Main" + "&method=getChat" + "&clock=" + element);
            ajaxRequest.send();
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
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    var data = this.responseText.split("|");
                    var x_value1 = Number(data[0]);
                    var x_value2 = Number(data[1]);
                    var width = x_value1 / x_value2 * 100;
                    document.getElementById("skill_bar2").style.width = width + "%"; 
                }
            };
            ajaxRequest.open("GET", "handlers/handler_js.php?&model=gamedata" + "&method=getXP");
            ajaxRequest.send();
        }
        

var user = {
                name : "John Snow",
                level : 13
                }; 
            function startGame() {
                document.getElementById("demo").innerHTML = user.name;
                myGameArea();
                myGamePiece = new component(30,30,"red",10,120);
                if (user.level < 15) {
                   myGamepiec2 = new component2();
                }
            }

            
            function myGameArea() {
                    var canvas = document.getElementById("myCanvas"); 
                    context = canvas.getContext("2d");
                    var img = document.getElementById("world_map");
                    img.style.width = 300;
                    img.style.height = 200;
                    context.drawImage(img, 0, 0, canvas.width, canvas.height);
                    var para = document.getElementById("demo");
                    para.insertBefore(canvas, para.childNodes[0]);
            }
            function component(width, height, color, x, y) {
                this.width = width;
                this.height = height;
                this.x = x;
                this.y = y;    
                context = myGameArea.context;
                context.fillStyle = color;
                context.fillRect(this.x, this.y, this.width, this.height);
            }
            function component2() {
                context = myGameArea.context;
                context.fillStyle = "red";
                context.beginPath();
                context.moveTo(10,200);
                context.lineTo(100,50);
                context.lineTo(50, 100);
                context.lineTo(0, 90);
                context.closePath();
                context.fill();
            }