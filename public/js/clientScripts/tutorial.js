const tutorial = {
    step: 1,
    lastStep: 8,
    onGoing: false,
    steps: [
        {
            id: 0,
            index: "hssn0",
                  
        }
    ],
    clickEvent: function () {

    },
    handler: function () {

    },
    checkStep() {
        let index; 
        switch (this.step) {
            case 1:
                index = "hssn0";
                break;
            case 2:
                console.log(conversation.index);
                if(conversation.index === "hssn1rrrrrr") {
                    index = "hssnns";
                }
                else {
                    index = "hssn1";
                }
            default:
                break;
        }
        conversation.loadConversation('hassen', index);
    },
    relocateHassen([xNewPos, yNewPos]) {
        gamePieces.objects.forEach(element => {
            if(element.type === 'figure') return;
            else if(element.src.indexOf("hassen") !== -1) {
                let xAdd = xNewPos - element.x;
                let yAdd = yNewPos - element.y; 
                element.x += xAdd;
                element.drawX += xAdd;
                element.diameterUp += yAdd;
                element.diameterDown += yAdd;
                element.y += yAdd;
                element.drawY += yAdd;
                element.diameterLeft += xAdd;
                element.diameterRight += xAdd;
            }
        });
        viewport.draw();
        gamePieces.drawStaticPieces();
        viewport.checkViewportGamePieces(true);
    },
    locateHassen() {
        gamePieces.objects.forEach(element => {
            if(element.type === 'figure') return;
            else if(element.src.indexOf("hassen") !== -1) {
                console.log(element);
            }
        });
    },
    showBuilding(building) {
        gamePieces.objects.forEach(element => {
            if(element.type === 'figure') return;
            else if(element.src.indexOf(building) !== -1) {
                element.visible = true;
            }
            else if(element.type === 'building') {
                element.visible = false;
            }
        });
        gamePieces.drawStaticPieces();
    },
    setTutorialTopic(topic) {
        document.getElementById("tutorial_progressContainer").querySelectorAll("p")[1].innerText = this.step + ". " + topic;
        progressBar.calculateProgress(document.getElementById("tutorial_progressBar"),
            this.step, this.lastStep, false);
    },
    makeHUD() {
        let tutorial_progressContainer = document.createElement("div");
        tutorial_progressContainer.setAttribute("id", "tutorial_progressContainer");
        tutorial_progressContainer.style.top = 60 + "px";
        tutorial_progressContainer.style.width = document.getElementById("game_canvas").width * 0.75 + "px";
        let title = document.createElement("p");
        title.innerText = "Tutorial progress";
        let under_title = document.createElement("p");
        under_title.innerText = "0. Introduction";

        // Create new progressBar and append in to progressContainer
        let createdProgressBar = progressBar.createProgressBar("tutorial_progressBar");
        tutorial_progressContainer.appendChild(title);
        tutorial_progressContainer.appendChild(createdProgressBar);
        tutorial_progressContainer.appendChild(under_title);
        document.getElementById("game_hud").appendChild(tutorial_progressContainer);
        progressBar.calculateProgress(createdProgressBar, 1, this.lastStep, false);

        // Make exit button
        let button = document.createElement("button");
        button.appendChild(document.createTextNode("Skip tutorial"));
        button.addEventListener('click', tutorial.skipTutorial);
        button.style.position = "absolute";
        button.style.top = 130 + "px";
        button.style.left = document.getElementById("game_canvas").offsetLeft + "px";
        button.style.zIndex = 4;
        document.getElementById("game_hud").appendChild(button);
    },
    hideHUD() {
        document.getElementById("game_hud").removeChild(document.getElementById("tutorial_progressContainer"));
    },
    conversationCount: 0,
    startTutorial() {
        if(this.onGoing === true) return;
        this.onGoing = !this.onGoing;
        this.makeHUD();
        conversation.loadConversation('hassen');
        //   this.tutorialSteps();
    },
    setNextStep() {
        console.log('nextStep');
        this.step++;
        this.tutorialSteps();
        // document.getElementById("conversation").querySelectorAll("button")[0].removeEventListener("click", tutorial.handler);
    },
    tutorialSteps() {
        // Step 0 doesn't exist
        switch (this.step) {
            case 1:
                this.setTutorialTopic("Introduction");
                break;
            case 2:
                this.setTutorialTopic("Controls");
                conversation.loadConversation('hassen', "hssn1");
                break;
            case 3:
                // HUD
                this.setTutorialTopic("HUD");
                conversation.loadConversation('hassen', "hssn2");
                break;
            case 4:
                // Profiencies
                this.setTutorialTopic("Profiencies");
                conversation.loadConversation('hassen', "hssn3");
                break;
            case 5:
                // Buildings
                this.setTutorialTopic("Buildings");
                conversation.loadConversation('hassen', "hssn4");
                break;
            case 6:
                // Buildings
                this.setTutorialTopic("Characters");
                conversation.loadConversation('hassen', "hssn5");
                break
            case 7:
                // Daqloons
                this.setTutorialTopic("Daqloons");
                break;
            case 8:
                // Adventures
                this.setTutorialTopic("Adventures");
                break;
            case 9:
                this.setTutorialTopic("8. End");
                conversation.loadConversation('hassen', 'hssnEx');
                break;
        }
    },
    skipTutorial() {
        tutorial.setNextStep("7,0");
        tutorial.tutorialSteps();
    },
    exitTutorial() {
        this.onGoing = false;
        this.step = 1;
        this.hideHUD();
    }
};