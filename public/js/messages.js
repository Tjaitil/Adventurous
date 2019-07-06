
    function showWriteMessage(title = false, receiver = false, message = false) {
        document.getElementById("write_message").style = "display: block;";
        document.getElementById("inbox").style = "display: none;";
        document.getElementById("message").style = "display: none;";
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
            console.log("ready");
            title = "RE: " + title;
        }
        var receiver = info[3].innerHTML;
        showWriteMessage(title, receiver, message);
    }
    
    function showMessage(message_id, element) {
        console.log(document.getElementById("inbox"));
        document.getElementById("inbox").style = "display: none;";
        document.getElementById("message").style = "display: block;";
        document.getElementById("write_message").style = "display: none";
        /*document.getElementsByTagName("SECTION")[0].appendChild(div);*/
        
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                console.log(data);
                console.log(document.getElementById("message_info"));
                document.getElementById("message_info").children[0].children[0].children[1].innerHTML = data[1];
                document.getElementById("message_info").children[0].children[1].children[1].innerHTML = data[2];
                document.getElementById("message").children[2].innerHTML = data[3];
                if(data[0] == 0) {
                    var parent = element.parentNode.parentNode;
                    var img = parent.children[3].children[0];
                    console.log(img);
                    img.setAttribute("src", "/message1.jpg");
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=messages" + "&method=showMessage" + "&message_id=" + message_id);
        ajaxRequest.send(); 
    }
    
    function userCheck() {
        var input = document.getElementById("receiver").value;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText == 0) {
                    console.log("The user you are trying to send message to does not exists!");
                    console.log(this.responseText);
                }
                else if(this.responseText > 0) {
                    console.log("User Exists!");
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=messages" + "&method=userCheck" + "&input=" + input);
        ajaxRequest.send();
    }
    
    function toggle(element) {
        document.getElementById("write_message").style = "display: none;";
        document.getElementById("message").style = "display: none;";
        if(element == 'sent') {
            document.getElementById("sent").style = "display: inline;";
            document.getElementById("inbox").style = "display: none;";
        }
        else if (element == 'inbox') {
            document.getElementById("inbox").style = "display: inline;";
            document.getElementById("sent").style = "display: none;";
        }
    }
    
    