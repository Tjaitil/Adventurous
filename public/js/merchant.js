    
    window.onload = function () {
        var trades = document.getElementById("trades").querySelectorAll(".store_trade");
        trades.forEach(function(element) {
            // Add eventListener to each node
            element.addEventListener('click', function() {
                selectTrade();
            });
        });
        var button = document.getElementById("do_trade").querySelectorAll("button")[0];
        button.addEventListener("click", buyItem);
        button.disabled = true;
    };
    
    
    function selectTrade() {
        console.log(event.target.tagName);
        if(event.target.tagName == 'IMG') {
            return false;
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].disabled = false;
        var trade = event.target.closest(".store_trade");
        document.getElementById("selected_trade").innerHTML = trade.innerHTML;
    }
    
    function buyItem() {
        if(document.getElementById("selected_trade").children[0] == undefined) {
            gameLog("ERROR: Select a trade!");
            return false;
        }
        var item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        var amount = document.getElementById("amount").value;
        var bond = document.getElementById("bond").checked;
        if(amount.length == 0) {
            gameLog("ERROR: Select your amount!");
            return false;
        }
        var data = "model=Merchant" + "&method=buyItem" + "&item=" + item + "&amount=" + amount + "&bond=" + bond;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                updateStock();
            }
        });
    }
    
    function updateStock() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.open("GET", "handlers/handler_js.php?model=Merchant" + "&method=getData");
        ajaxRequest.send();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                document.getElementById("trades").children[0].innerHTML = this.responseText;
                var trades = document.getElementById("trades").querySelectorAll(".store_trade");
                trades.forEach(function(element) {
                    // Add eventListener to each node
                    element.addEventListener('click', function() {
                        selectTrade();
                    });
                });
            }
        };
    }
    
    function newAssignment(id) {
        var data = "model=setassignment" + "&method=newAssignment" + "&assignment_id=" + id;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    gameLog(this.responseText);
                }
                
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    function pickUp(favor = false) {
        var data;
        if(favor === true) {
            data = "model=trader" + "&method=pickUp" + "&favor=true";
        }
        data = "model=trader" + "&method=pickUp";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                var responseText = response[1].split("|");
                gameLog(responseText[0]);
                var substrings = document.getElementById("assignment").children[0].innerHTML.split(" ");
                substrings[2] = responseText[1];
                document.getElementById("assignment").children[0].innerHTML = substrings.join(" ");
            }
        });
    }
    function deliver(favor = false) {
        var data;
        if(favor == true) {
            data = "model=trader" + "&method=deliver" + "&favor=true";
        }
        data = "model=trader" + "&method=deliver";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                var responseText = response[1].split("|");
                console.log(responseText);
                var assignmentDiv = document.getElementById("assignment");
                var substrings;
                if(response[1].indexOf("finished") == -1) {
                    gameLog(responseText[0]);
                    show_xp('trader', responseText[1]);
                    // Change the paragraphs in assignment div
                    substrings = assignmentDiv.children[0].innerHTML.split(" ");
                    substrings[2] = "0" + substrings[2].slice(substrings[2].indexOf("/"));
                    document.getElementById("assignment").children[0].innerHTML = substrings.join(" ");
                    var str = assignmentDiv.children[1].innerHTML;
                    assignmentDiv.children[1].innerHTML = str.slice(0, str.indexOf("delivered") + 9) + " " + responseText[3];
                }
                else {
                    gameLog(responseText[0]);
                    gameLog(responseText[4]);
                    show_xp('trader', parseInt(responseText[1]) + parseInt(responseText[6]));
                    // Change the paragraphs in assignment div
                    substrings = assignmentDiv.children[0].innerHTML.split(" ");
                    substrings[2] = "0" + substrings[2].slice(substrings[2].indexOf("/"));
                    document.getElementById("assignment").children[0].innerHTML = substrings.join(" ");
                    assignmentDiv.children[1].innerHTML = "Current Assignment: none";
                }
            }
        });
    }
    function getData(assignment = false) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(assignment == false) {
                    document.getElementById("assignment_status").innerHTML = this.responseText;
                }
                else {
                    document.getElementById("assignment").innerHTML = this.responseText;
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=" + "&method=" + "&assignment=" + assignment);
        ajaxRequest.send();
    }