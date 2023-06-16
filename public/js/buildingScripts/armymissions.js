// import countdown from "../utilities/countdown.js";
// import warriorSelect from "../utilities/warriorSelect.js";
// import createHTMLNode from "../utilities/createHTMLNode.js";
// import { inputHandler } from "../clientScripts/inputHandler.js";
// import { gameLogger } from "../utilities/gameLogger.js";
// import { ajaxP } from "../ajax.js";

// const armyMissionModule = {
//     init() {
//         document.getElementById("navigate-army-camp").addEventListener("click", function () {
//             inputHandler.fetchBuilding("armycamp");
//         });

//         document
//             .getElementById("new_missions")
//             .querySelectorAll(".mission-info")
//             .forEach((element) => element.addEventListener("click", (event) => this.prepareMission(event)));

//         [...document.getElementsByClassName("mission-tab-toggle")].forEach((element) =>
//             element.addEventListener("click", (event) => this.toggleTab(event.currentTarget))
//         );

//         document.getElementById("mission-enabled-do").addEventListener("click", () => this.doMission());
//         this.prepareMissionWrapper = document.getElementById("new-mission-selected-container");
//         this.createInitMissionData();
//         this.missions = [];
//         warriorSelect.addWarriorEvents();
//     },
//     prepareMissionWrapper: undefined,
//     toggleNewMissionSelected(toggleVisible) {
//         if (toggleVisible) {
//             this.prepareMissionWrapper.style.visibility = "visible";
//             this.prepareMissionWrapper.style.display = "block";
//         } else {
//             this.prepareMissionWrapper.style.visibility = "hidden";
//             this.prepareMissionWrapper.style.display = "none";
//         }
//     },
//     selectedTab: 0,
//     toggleTab(button) {
//         if (!button.dataset.missionTabToggle) return false;

//         let number = button.dataset.missionTabToggle;
//         // Toggle tab with number in data attribute
//         [...document.getElementsByClassName("mission-tab")].forEach((element: HTMLElement) => {
//             if (element.dataset.missionTab === number) {
//                 element.classList.add("mission-tab-visible");
//             } else {
//                 element.classList.remove("mission-tab-visible");
//             }
//         });
//         newsContentSidebar.adjustMainContentHeight();
//     },
//     missions: [],
//     createInitMissionData() {
//         const locateTabElement = (missionID) =>
//             [...document.getElementsByClassName("mission-tab")].find(
//                 (element: HTMLElement) => element.dataset.missionId === missionID
//             ) ?? undefined;

//         let data = "model=ArmyMissions" + "&method=getCountdowns";
//         ajaxG(data, (response) => {
//             if (response[0] != false) {
//                 let responseText = response[1];
//                 if (Array.isArray(responseText.countdowns)) {
//                     responseText.countdowns.forEach((element, index) => {
//                         // Create mission object
//                         this.initNewMissionObject({
//                             missionID: element.mission_id,
//                             endtime: element.datetime,
//                             tabElement: locateTabElement(element.mission_id),
//                         });
//                     });
//                 }
//                 updateCountdownTab();
//             }
//         });
//     },
//     prepareMission(event) {
//         let missionContainer = event.currentTarget.cloneNode(true);

//         this.prepareMissionWrapper.replaceChild(
//             missionContainer,
//             this.prepareMissionWrapper.querySelectorAll("div")[0]
//         );
//         this.toggleNewMissionSelected(true);
//     },
//     initNewMissionObject($init_data: InitArmyMissionData) {
//         // Create mission object
//         let missionObject = new ArmyMission({
//             missionID: $init_data.missionID,
//             endTime: $init_data.endTime ? $init_data.endTime : false,
//             tabElement: $init_data.tabElement,
//         });
//         missionObject.addEvents();
//         this.missions.push(missionObject);
//         missionObject.calculateCountdown();
//     },
//     doMission() {
//         let warriors = warriorSelect.warriorsCheck();
//         // Send array with warriors id and mission id to model
//         if (!warriors || warriors.length === 0) {
//             gameLogger.addMessage("Please select warriors!", true);
//             return false;
//         }

//         let mission_id = document.getElementById("new-mission-selected-container").querySelectorAll(".mission-info")[0]
//             .dataset.missionId;
//         if (!mission_id) return false;
//         let data =
//             "model=ArmyMissionsRessource" +
//             "&method=set" +
//             "&mission_id=" +
//             mission_id +
//             "&warriors=" +
//             JSON.stringify(warriors);
//         ajaxP(data, (response) => {
//             console.log(response);
//             if (response[0] != false) {
//                 let responseText = response[1];
//                 updateHunger(response.newHunger);
//                 updateCountdownTab();

//                 // Update warriors
//                 warriorSelect.getAvailableWarriors();

//                 // Insert HTML templates
//                 let missionTabIndex;
//                 let outer_container;
//                 let tabElement;
//                 if (document.getElementById("mission-tabs-outer-container")) {
//                     outer_container = document.getElementById("mission-tabs-outer-container");
//                     let containerChildren = outer_container.querySelectorAll("mission-tab-toggle").length;
//                     missionTabIndex = containerChildren - 1;
//                 }

//                 // Add html and calculate mission tab data
//                 if (responseText.html[0] !== undefined) {
//                     const node = createHTMLNode(responseText.html[0]);

//                     tabElement = document.getElementById("mission-tabs-outer-container").appendChild(node);
//                     tabElement.setAttribute("data-mission-tab", missionTabIndex);
//                 }
//                 if (
//                     responseText.html[1] !== undefined &&
//                     document.getElementById("mission-tab-toggle-outer-container")
//                 ) {
//                     // Create node
//                     const node = createHTMLNode(responseText.html[1]);

//                     node.setAttribute("data-mission-tab-toggle", missionTabIndex);

//                     document.getElementById("mission-tab-toggle-outer-container").appendChild(node);
//                 }
//                 console.log(tabElement);
//                 // Init  new Mission
//                 this.initNewMissionObject({
//                     missionID: mission_id,
//                     tabElement,
//                 });

//                 this.toggleNewMissionSelected(false);
//             }
//         });
//     },
// };

// interface InitArmyMissionData {
//     tabElement?: HTMLButtonElement;
//     missionID?: number;
//     endTime?: boolean | number;
// }

// class ArmyMission {
//     // tabElement = null;
//     // missionID = undefined;
//     // getReportButton = undefined;
//     // cancelMissionReportButton = undefined;
//     // timeContainer = undefined;
//     // intervalID = null;
//     // endTime = undefined;
//     private tabElement: InitArmyMissionData["tabElement"];
//     public missionID: InitArmyMissionData["missionID"];
//     public getReportButton?: HTMLButtonElement;
//     public cancelMissionReportButton?: HTMLElement;
//     public timeContainer: HTMLElement;
//     private intervalID: null | number;
//     private endTime: InitArmyMissionData["endTime"];

//     constructor($init_data: InitArmyMissionData) {
//         this.tabElement = $init_data.tabElement;
//         this.missionID = $init_data.missionID;
//         this.endTime = $init_data.endTime;
//         if (this.tabElement) {
//             this.timeContainer = <HTMLElement>$init_data.tabElement.querySelectorAll(".mission-countdown")[0];
//             this.cancelMissionReportButton = <HTMLElement>(
//                 $init_data.tabElement.querySelectorAll(".current-mission-cancel")[0]
//             );
//             this.cancelMissionReportButton = <HTMLElement>(
//                 $init_data.tabElement.querySelectorAll(".current-mission-get-report")[0]
//             );
//         }
//     }

//     addEvents() {
//         this.getReportButton.addEventListener("click", () => this.updateMission());
//         this.cancelMissionReportButton.addEventListener("click", () => this.cancelMission());
//     }

//     cancelMission() {
//         let data = "model=ArmyMissionsRessource" + "&method=cancel" + "&mission_id=" + this.missionID;
//         ajaxP(data, (response) => {
//             if (response[0] != false) {
//                 this.destroy();

//                 // Update selected warriors
//                 warriorSelect.getAvailableWarriors();
//             }
//         });
//     }
//     updateMission() {
//         let data = "model=ArmyMissionsRessource" + "&method=update" + "&mission_id=" + this.missionID;
//         ajaxP(data, (response) => {
//             if (response[0] !== false) {
//                 let responseText = response[1];

//                 // Combat is currently disabled

//                 // if(responseText.mission_combat) {
//                 //     document.getElementById("battle_result").innerHTML = responseText[0];
//                 //     document.getElementById("battle_result").style.display = "";
//                 // }

//                 // Update selected warrors
//                 warriorSelect.getAvailableWarriors();

//                 updateInventory();
//                 this.destroy();
//             }
//         });
//     }

//     calculateCountdown() {
//         // Calculate mission countdown
//         this.endTime = this.endTime * 1000;
//         this.intervalID = setInterval(() => {
//             let { remainder, hours, minutes, seconds } = countdown.calculate(this.endTime);
//             if (!document.getElementById("new_missions")) {
//                 clearInterval(this.intervalID);
//                 return false;
//             }
//             console.log(this.cancelMissionReportButton);
//             if (remainder < 0 && this.missionID != 0) {
//                 clearInterval(this.intervalID);
//                 this.cancelMissionReportButton.style.display = "none";
//                 this.getReportButton.style.display = "inline-block";
//                 this.timeContainer.innerHTML = "Finished";
//             } else if (remainder < 0) {
//                 clearInterval(this.intervalID);
//                 this.timeContainer.innerHTML = "None";
//                 this.getReportButton.style.display = "none";
//                 this.cancelMissionReportButton.style.display = "none";
//             } else {
//                 this.cancelMissionReportButton.style.display = "inline-block";
//                 this.getReportButton.style.display = "none";
//                 this.timeContainer.innerHTML = "Time left " + hours + "h " + minutes + "m " + seconds + "s ";
//             }
//         }, 1000);
//     }

//     getCountdown() {
//         if (!this.missionID) return;
//         // Fetch a missions countdown
//         let data = "model=ArmyMissions" + "&method=getCountdowns" + "&mission_id=" + this.missionID;
//         ajaxG(data, (response) => {
//             if (response[0] != false) {
//                 let responseText = response[1];
//                 this.endTime = responseText.countdowns[0].datetime * 1000;
//                 this.calculateCountdown(responseText);
//             }
//         });
//     }

//     destroy() {
//         // Destroy
//         clearInterval(this.intervalID);
//         this.tabElement.innerHTML = "<p>Mission finished</p>";
//     }
// }

// export default armyMissionModule;
