    /*jshint sub:true*/
    
    /* function hello () {
    var car = {type:"Fiat", model:500, color:"white"};
    var comb = [car, "new york", "legend"];
    var city = {name:"Oslo", population:300000};
    var cities = [city, 500];
    var x = 300 + car.model;
    document.getElementById("demo").innerHTML = model;
    } */
    window.addEventListener("Window loaded", window.load, displaycookie());
    window.addEventListener("Window loaded", window.load, date());
    var type1Entry = document.forms["troop_add"].elements["type_1"].value;
    /* function troop () {
        console.log("Training initiated function troop");
        troop_1();
        troop_2();
        troop_3();
        troop_4();
        troop_5();
    } */
    function troop_1 () {  
        var type1Count = document.getElementsByTagName("TD")[1].textContent;
        var type1Entry = document.forms["troop_add"].elements["type_1"].value;
        if ( type1Entry != 0) {
            
            console.log("troop_1");
            document.forms["troop_add"].reset();
            date (type1Entry);
            
            /* var type1CountNew = parseInt(type1Entry) + parseInt(type1Count);
            document.cookie = "swordman=" + type1CountNew + ";expires=Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            console.log(type1Entry + " "  + "swordman, added to army, the army is now on" + " " + type1CountNew);
            document.forms["troop_add"].reset();
            displaycookie();*/
        }

        else if (type1Entry === 0) {
            alert("No swordman detected"); 
        } 
    }
    
    function date () {
        console.log("date");
        var type1Entry = document.forms["troop_add"].elements["type_1"].value;
        document.forms["troop_add"].reset();
        var nameValueArray = document.cookie.split(/\W+/);
        var time_search = nameValueArray.indexOf("trainingtime1");
        if (type1Entry > 0)  {
            checktable(type1Entry);
        }
        else {     
        console.log("No input or cookie");
        }
        }
    
    function checktable (type1Entry) {  
        var now = new Date().getTime();
        var type_1training = 30000;
        var date_3 = new Date(now).getTime() + (type1Entry * type_1training);
        var date_4 = new Date(now).getTime() + (type_1training);
        hello (date_3, type1Entry, date_4);
        hello_2 (date_3, type1Entry, date_4);
    }

    function hello (date_3, type1Entry, date_4) {
        var x = setInterval(function (){training(x, date_3, type1Entry, date_4); },1000);
    }
    function hello_2 (date_3, type1Entry, date_4) {
        var y = setInterval(function (){training_2(y, type1Entry, date_4); },1000);
    }
    function training (x, date_3, type1Entry) {
            console.log("TIMER");
            var date_2 = new Date().getTime();
            var countdown = date_3 - date_2;
            document.cookie = "trainingtime1=" + countdown + ";expires=Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            document.getElementById("cell1").innerHTML= date_2;
            document.getElementById("cell2").innerHTML = date_3;
            var hours = Math.floor((date_2 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((date_2 % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((date_2 % (1000 * 60)) / 1000);
            var hours_1 = Math.floor((date_3 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes_1 = Math.floor((date_3 % (1000 * 60 * 60)) / (1000 * 60));
            var seconds_1 = Math.floor((date_3 % (1000 * 60)) / 1000); 
            var hours_2 = Math.floor((countdown % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes_2 = Math.floor((countdown % (1000 * 60 * 60)) / (1000 * 60));
            var seconds_2 = Math.floor((countdown % (1000 * 60)) / 1000);
            hours_2 = hours_2 < 10 ? "0" + hours_2 : hours_2;
            minutes_2 = minutes_2 < 10 ? "0" + minutes_2 : minutes_2;
            seconds_2 = seconds_2 < 10 ? "0" + seconds_2 : seconds_2;
            document.getElementById("cell2_1").innerHTML = hours + ":" + minutes + ":" + seconds;
            document.getElementById("cell2_2").innerHTML = hours_1 + ":" + minutes_1 + ":" + seconds_1; 
            document.getElementById("cell3").innerHTML = type1Entry + "x swordman =" + " " + hours_2 + ":" + minutes_2 + ":" + seconds_2;
            if (countdown<0) {
               cleanup(x);
            }
    }
    function training_2 (y, type1Entry, date_4, countdown) {
        var date2 = new Date().getTime();
        var countdown2 = date_4 - date2;
        var hours_3 = Math.floor((countdown2 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes_3 = Math.floor((countdown2 % (1000 * 60 * 60)) / (1000 * 60));
        var seconds_3 = Math.floor((countdown2 % (1000 * 60)) / 1000);
        hours_3 = hours_3 < 10 ? "0" + hours_3 : hours_3;
        minutes_3 = minutes_3 < 10 ? "0" + minutes_3 : minutes_3;
        seconds_3 = seconds_3 < 10 ? "0" + seconds_3 : seconds_3;    
        document.getElementById("cell3_2").innerHTML = "Next soldier will be finished in:" + " " + hours_3 + ":" + minutes_3 + ":" + seconds_3;
        if (countdown2 < 0 && countdown > 0) {
            training_2();
        }
        else if (countdown2 < 0 && countdown < 0) {
            cleanup(y);
        }
    }
    function cleanup (x) {
        clearInterval(x);
        document.getElementById("cell3").innerHTML = "";
        
    }
    function cleanup (y) {
        clearInterval(y);
        document.getElementById("cell3_2").innerHTML = "";
        
    }

    function troop_1 () {  
        /* var type1Count = document.getElementsByTagName("TD")[1].textContent;*/
        /* var type_1Training = 60 */
        
        /* var trainingTime = parseInt(type1Entry) * parseInt(type_1Training); */
        if ( type1Entry != 0) {
            
            console.log("troop_1");
            document.forms["troop_add"].reset();
            date ();
            
            /* var type1CountNew = parseInt(type1Entry) + parseInt(type1Count);
            document.cookie = "swordman=" + type1CountNew + ";expires=Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            console.log(type1Entry + " "  + "swordman, added to army, the army is now on" + " " + type1CountNew);
            document.forms["troop_add"].reset();
            displaycookie(); */
        }

        else if (type1Entry === 0) {
            alert("No swordman detected"); 
        } 
    }
    function troop_2 () {
        var type2Entry = document.forms["troop_add"].elements["type_2"].value;
        var type2Count = document.getElementsByTagName("TD")[3].textContent;
        if (type2Entry != 0) {
            var type2CountNew = parseInt (type2Entry) + parseInt (type2Count);
            /* var type2CountNew = 10; */
            document.cookie = "spearman=" + type2CountNew + ";expires= Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            console.log (type2Entry + " " + "spearman, added to army, the army is now on" + " " + type2CountNew);
            document.forms["troop_add"].reset();
            displaycookie();
        }
        else  if (type2Entry === 0) {
            console.log("No spearmens detected");
        }
    }
    function troop_3 () {
        var type3Entry = document.forms["troop_add"].elements["type_3"].value;
        var type3Count = document.getElementsByTagName("TD")[5].textContent;
        if (type3Entry != 0) {
            var type3CountNew = parseInt (type3Entry) + parseInt (type3Count);
            document.cookie = "archer=" + type3CountNew + ";expires = Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            console.log (type3Entry + " " + "archer, added to army, the army is now on" + " " + type3CountNew);
            document.forms["troop_add"].reset();
            displaycookie();
        }
        else if (type3Entry === 0) {
            console.log("No archer detected");
        }
    }
    function troop_4 () {
        var type4Entry = document.forms["troop_add"].elements["type_4"].value;
        var type4Count = document.getElementsByTagName("TD")[7].textContent;
        if (type4Entry != 0) {
            var type4CountNew = parseInt (type4Entry) + parseInt (type4Count);
            document.cookie = "horseWarrior=" + type4CountNew + ";expires = Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            console.log (type4Entry + " " + "horse warrior, added to army, the army is now on" + " " + type4CountNew);
            document.forms["troop_add"].reset();
            displaycookie();
        }
        else if (type4Entry === 0) {
            console.log("No archer detected");
        }
    }
    function troop_5 () {
        var type5Entry = document.forms["troop_add"].elements["type_5"].value;
        var type5Count = document.getElementsByTagName("TD")[9].textContent;
        if (type5Entry != 0) {
            var type5CountNew = parseInt (type5Entry) + parseInt (type5Count);
            document.cookie = "horseArcher=" + type5CountNew + ";expires = Fri, 5 Aug 2020 01:00:00 UTC; path=/;";
            console.log (type5Entry + " " + "horse archer, added to army, the army is now on" + " " + type5CountNew);
            document.forms["troop_add"].reset();
            displaycookie();
        }
        else if (type5Entry === 0) {
            console.log("No archer detected");
        }
    }
    function displaycookie () {
        var nameValueArray = document.cookie.split(/\W+/);
        var a = nameValueArray.indexOf("swordman");
        var b = nameValueArray.indexOf("spearman");
        var c = nameValueArray.indexOf("archer");
        var d = nameValueArray.indexOf("horseWarrior");
        var e = nameValueArray.indexOf("horseArcher");
        if ( a != -1) {
            var a_increase = a + 1;
            var a_pick = nameValueArray[a_increase];
            console.log("Cookie detected," + " " + a_pick);
            document.getElementsByTagName("TD")[1].textContent = a_pick;
            }
        if (b != -1) {
            var b_increase = b + 1;
            var b_pick = nameValueArray[b_increase];
            console.log("Cookie detected," + " " + b_pick);
            document.getElementsByTagName("TD")[3].textContent = b_pick;
        }
        if (c != -1) {
            var c_increase = c + 1;
            var c_pick = nameValueArray[c_increase];
            console.log("Cookie detected," + " " + c_pick);
            document.getElementsByTagName("TD")[5].textContent = c_pick;
        }
        if (d != -1) {
            var d_increase = d + 1;
            var d_pick = nameValueArray[d_increase];
            console.log("Cookie detected," + " " + d_pick);
            document.getElementsByTagName("TD")[7].textContent = d_pick;
        }
        if (e != -1) {
            var e_increase = e + 1;
            var e_pick = nameValueArray[e_increase];
            console.log("Cookie detected," + " " + e_pick);
            document.getElementsByTagName("TD")[9].textContent = e_pick;
        }
        
        else if (a === -1 && b === -1 && c === -1 && d === -1 && e === -1) {
                console.log("No cookie detected");
            }    
        }