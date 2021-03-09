    window.addEventListener("load", function() {
        var buttons = document.getElementById("actions").querySelectorAll("BUTTON");
        buttons.forEach(function(element) {
            // Add eventListener to each node
            element.addEventListener('click', function() {
                toggle();
            });
        });
        document.getElementById("message").children[1].addEventListener("click", toggle);
        
        var prev = document.querySelectorAll(".previous");
        prev.forEach(function(element) {
            // Add functions to each node
            element.disabled = true;
            element.addEventListener('click', function() {
                getmMessages();
            });
        });
        var next = document.querySelectorAll(".next");
        next.forEach(function(element) {
            // Add functions to each node
            element.addEventListener('click', function() {
                getmMessages();
            });
        });
        document.getElementById("receiver").addEventListener("keyup", chk_me);
        readCheck();
    });
    
    var timer;
    function chk_me(){
        clearTimeout(timer);
        timer = setTimeout(userCheck, 1000);
    }
    function readCheck() {
        var tbodyRows = document.getElementById("inbox").children[1].children;
        var x;
        console.log(tbodyRows);
        var src;
        for(var i = 0; i < tbodyRows.length; i++) {
            // Check the src of image in last tr, if it is 0 the message has not been read
            src = tbodyRows[i].children[4].children[0].src.split("/").pop();
            if(src.indexOf(0) != -1) {
                for(x = 0; x < tbodyRows[i].children.length; x++) {
                    tbodyRows[i].children[x].style.fontWeight = "bold";
                }
            }
        }
    }
    function toggle() {
        var element = event.target.textContent.trim();
        if(element === "Back to messages") {
            element = "Inbox";
        }
        var divs = ["Sent", "Inbox", "Write Message", "Message"];
        
        for(var i = 0; i < divs.length; i++) {
            var div = divs[i].replace(" ", "_").toLowerCase();
            if(divs[i] == element) {
                document.getElementById(div).style.display = "inline-block";
            }
            else {
                document.getElementById(div).style.display = "none";
            }
        }
    }
    
    
    function showWriteMessage(title = false, receiver = false, message = false) {
        document.getElementById("write_message").style.display = "block";
        document.getElementById("inbox").style.display = "none";
        document.getElementById("message").style.display = "none";
        document.getElementById("sent").style.display = "inline";
        if(title && receiver && message != false) {
            console.log(document.getElementById("message_form").getElementsByTagName("INPUT"));
            var info = document.getElementById("message_form").getElementsByTagName("INPUT");
            info[0].value = title;
            info[1].value = receiver;
            document.getElementById("the_message").innerHTML = "\n\n\n" + "-------------------------" + "\n" + receiver +
            " " + "wrote:" + "\n\n" + message; 
        }
    }
    
    function answer() {
        var info = document.getElementById("message_info").getElementsByTagName("TD");
        var title = info[1].innerHTML;
        var message = document.getElementById("message_content").innerHTML;
        console.log(title.search("RE:"));
        if(title.search("RE") != -1) {
            var r = /\d+/;
            var match = title.match(r);
            if(match == null) {
                var part1 = title.slice(0,2);
                var part2 = title.slice(2);
                part1 += "^1 ";
                title = part1 + part2;
            }
            if(match > 0) {
                var part3 = title.slice(match.index, match.index +1);
                part3 = Number(part3);
                title = title.replace(part3, part3+1);
            }
        }
        if(title.search("RE") == -1) {
            title = "RE: " + title;
        }
        var receiver = info[3].innerHTML;
        showWriteMessage(title, receiver, message);
    }
    
    function showMessage(message_id, element) {
        var td = event.target.closest("td");
        if(td.style.fontWeight !== "normal") {
            tr = td.parentNode;
            for(x = 0; x < tr.children.length; x++) {
                tr.children[x].style.fontWeight = "initial";
            }
        }
        
        document.getElementById("inbox").style.display = "none";
        document.getElementById("message").style.display = "block";
        document.getElementById("write_message").style.display = "none";
        document.getElementById("sent").style.display = "none";
        /*document.getElementsByTagName("SECTION")[0].appendChild(div);*/
    
        
        var data = "model=Messages" + "&method=showMessage" + "&message_id=" + message_id;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var responseText = response[1].split("|");
                document.getElementById("message_info").children[0].children[0].children[1].innerHTML = responseText [1];
                document.getElementById("message_info").children[0].children[1].children[1].innerHTML = responseText [2];
                document.getElementById("message_info").children[0].children[2].children[1].innerHTML = responseText [3];
                document.getElementById("message").children[2].innerHTML = responseText[4];
                console.log(responseText[4]);
                if(data[0] == 0) {
                    var parent = element.parentNode.parentNode;
                    var img = parent.children[4].children[0];
                    img.setAttribute("src", "/1.png");
                }
                checkMessages();
            }
        });
    }
    
    function userCheck() {
        let input = document.getElementById("receiver");
        let data = "model=Messages" + "&method=userCheck" + "&input=" + input.value;
        ajaxG(data, function(response) {
            console.log(response);
            console.log(parseInt(response[1]));
            if(parseInt(response[1]) === 0) {
                console.log('hello');
                input.setCustomValidity("The user you are trying to send message to does not exists!");    
            }
            else {
                
            }
        });
    }
    var pages = {
        
    };
    var indexes = {
        i: 0,
        x: 0
    };
    
    function getmMessages() {
        var button = event.target;
        var table = button.closest("table");
        // tb = tbody
        var tb = table.children[1];
        var date = table.children[1].lastElementChild.children[3].innerHTML;
        var type = button.innerHTML;
        var increment;
        // Increment decides if you go next or prev page
        if(type.indexOf("Next") != -1) {
            increment = 1;
        }
        else {
            increment = -1;
        }
        // index is the index for the current table and letter is for deciding which table index to use
        var index;
        var letter;
        if(table.id == "inbox") {
            table = "inbox";
            index =  indexes.i;
            letter = "i";
        }
        else {
            table = "sent";
            index = indexes.x;
            letter = "x";
        }
        // If the user return to front page disable the button
        if(index + increment == 0) {
            button.disabled = true;
            button.parentElement.children[1].disabled = false;
        }
        else if(index === 0) {
            button.parentElement.children[0].disabled = false;
        }
        var page = pages[table + (index + increment)];
        if(page == undefined) {
            var data = "model=Messages" + "&method=getmMessages" + "&table=" + table + "&type=" + type + "&date=" + date;
            ajaxG(data, function(response) {
                if(response[0] != false) {
                    var responseText = response[1].split("#");
                    if(responseText[0].length > 0) {
                        button.disabled = true;
                    }
                    else {
                        button.disabled = false;
                    }
                    pages[table + index] = tb.innerHTML;
                    // If the index is 0 and the page is not saved, save it
                    if(indexes[letter] == 0 && pages[table + index] == undefined) {
                        pages[table + 0] = tb.innerHTML;
                    }
                    indexes[letter] += increment;
                    pages[table + indexes[letter]] = responseText[1];
                    tb.innerHTML = responseText[1];
                }
            });   
        }
        else {
            indexes[letter] += increment;
            tb.innerHTML = pages[table + (index + increment)];
            // If the instances of class is less than 8 there is no more messages
            if(pages[table + (index + increment)].split("class").length < 9) {
                button.disabled = true;
            }
        }
    }