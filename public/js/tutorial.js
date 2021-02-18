tutorial = {
    step: "0,0",
    lastStep: 8,
    clickEvent: function() {
        
    },
    handler: function() {
     
    },
    setTutorialTopic: function(topic) {
        document.getElementById("tutorial_progressContainer").querySelectorAll("p")[1].innerText = topic;
        progressBar.calculateProgress(document.getElementById("tutorial_progressBar"),
                                      parseInt(this.step.split(",")[0]) + 1, this.lastStep, false);
    },
    progressBar: function() {
        let div_container = document.createElement("div");
        div_container.style.height = "50px";
        div_container.setAttribute("id", "tutorial_progressContainer");
        div_container.style.color = "white";
        div_container.style.position = "absolute";
        div_container.style.top = document.getElementById("game_canvas").offsetTop + 20 + "px";
        div_container.style.width = document.getElementById("game_canvas").width + "px";
        let title = document.createElement("p");
        title.innerText = "Tutorial progress";
        div_container.appendChild(title);
        let p = document.createElement("p");
        p.innerText = "1. Introduction";
        let div = document.createElement("div");
        div.setAttribute("class", "progressBarContainer");
        div.setAttribute("id", "tutorial_progressBar");
        div.style.width = "100%";
        div_container.appendChild(div);
        div_container.appendChild(p);
        let div2 = document.createElement("div");
        div2.setAttribute("class", "progressBarOverlay");
        div.appendChild(div2);
        let div3 = document.createElement("div");
        div3.setAttribute("class", "progressBar");
        div3.style.position = "absolute";
        div.appendChild(div3);
        let span1 = document.createElement("span");
        span1.setAttribute("class", "progressBar_currentValue");
        let span2 = document.createElement("span");
        let space = document.createTextNode("\u00A0");
        let slash = document.createTextNode(" /");
        span2.setAttribute("class", "progressBar_maxValue");
        div3.appendChild(span1);
        div3.appendChild(space);
        div3.appendChild(space);
        div3.appendChild(slash);
        div3.appendChild(space);
        div3.appendChild(space);
        div3.appendChild(span2);
        document.getElementsByTagName("section")[0].appendChild(div_container);
        progressBar.calculateProgress(document.getElementById("tutorial_progressBar"), 1, this.lastStep, false);
    },
    makeButton: function() {
        let button = document.createElement("button");
        button.appendChild(document.createTextNode("Skip tutorial"));
        button.addEventListener('click', tutorial.skipTutorial);
        button.style.position = "absolute";
        button.style.top = document.getElementById("game_canvas").offsetTop + "px";
        button.style.left = document.getElementById("game_canvas").offsetLeft + "px";
        button.style.zIndex = 2;
        document.getElementsByTagName("section")[0].appendChild(button);
    },
    conversationCount: 0,
    startTutorial: function() {
      this.makeButton();
      this.progressBar();
      this.tutorialSteps();
    },
    setNextStep: function(nextStep) {
        this.conversationCount = 0;
        this.step = nextStep;
        document.getElementById("conversation").querySelectorAll("button")[0].removeEventListener("click", tutorial.handler);
    },
    tutorialSteps: function() {
        let stepParts = this.step.split(",");
        console.log(stepParts);
        switch(stepParts[0]) {
            case "0":
                switch(stepParts[1]) {
                    case "0":
                        // Intoduction
                        game.inactivityTime('pause');
                        game.properties.pause = false;
                        game.properties.requestId = window.requestAnimationFrame(game.update);
                        // Set handler function for this step, triggered by conversation button
                        this.handler = function() {
                            console.log('first handler');
                            tutorial.conversationCount++;
                            console.log(tutorial.conversationCount);
                            
                            if(tutorial.conversationCount == 6) {
                                // Remove tutorial.handler for this step
                                document.getElementById("conversation").querySelectorAll("button")[0].removeEventListener("click",
                                                                                                                   tutorial.handler);
                                
                                tutorial.setNextStep("1,0");
                                tutorial.tutorialSteps();
                            }
                        };
                        conversation.loadConversation('hassen', false, function() {
                            document.getElementById("conversation").querySelectorAll("button")[0].addEventListener("click",
                                                                                                               tutorial.handler);
                        });
                        break;
                    case "1":
                        
                        break;
                    case "2":
                        
                        break;
                }
                break;
            case "1":
                switch(stepParts[1]) {
                    case "0":
                        this.setTutorialTopic("2. Controls");
                        // Set handler function for this step, triggered by conversation button
                        this.handler = function() {
                            tutorial.conversationCount++;
                            console.log(tutorial.conversationCount);
                            
                            if(tutorial.conversationCount == 2) {
                                let img = new Image(32, 32);
                                img.src = "public/images/kapys.png";
                                let img2 = new Image(128, 128);
                                img.onload = function() {
                                    game.properties.context3.drawImage(img, 128, 0, 32, 32);
                                };
                                img2.onload = function() {
                                    game.properties.context3.drawImage(img2, 0, 0, 128, 128);
                                };
                                img2.src = "public/images/stockpile.png";
                            }
                            if(tutorial.conversationCount == 3) {
                                // Remove tutorial.handler for this step
                                document.getElementById("conversation").querySelectorAll("button")[0].removeEventListener("click",
                                                                                                                   tutorial.handler);
                                tutorial.setNextStep("1.1");
                            }
                        };
                        conversation.loadConversation('hassen', 'hssn1', function() {
                            document.getElementById("conversation").querySelectorAll("button")[0].addEventListener("click",
                                                                                                               tutorial.handler);
                        });
                        break;
                    case "1":
                        // Set handler function for this step, triggered by conversation button
                        this.handler = function() {
                            if(this.conversationCount == 2) {
                                setTimeout(function() {
                                    document.getElementById("conversation_container").style.visibility = "hidden";
                                    conversation.index = null;
                                }, 4000);
                                function checkKey() {
                                    console.log('checkKey');
                                    console.log(event.keyCode);
                                    if(event.keyCode == 69) {
                                        window.removeEventListener("keydown", checkKey);
                                        conversation.getNextLine();
                                    }
                                }
                                window.addEventListener("keydown", checkKey);
                            }
                        };
                        break;
                }
                break;
            case "2":
                this.setTutorialTopic("3. HUD");
                break;
            case "3":
                this.setTutorialTopic("4. Profiencies");
                break;
            case "4":
                this.setTutorialTopic("5. Profiencies");
                break;
            case "5":
                this.setTutorialTopic("6. Adventures");
                break;
            case "6":
                this.setTutorialTopic("7. Important characters");
                break;
            case "7":
                this.setTutorialTopic("8. End");
                conversation.loadConversation('hassen', 'hssnEx');
                break;
        }
    },
    skipTutorial: function() {
        tutorial.setNextStep("7,0");
        tutorial.tutorialSteps();
    }
    
};