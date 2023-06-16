import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface.js';
import { Inventory, checkInventoryStatus } from './../clientScripts/inventory.js';
import { ProgressBar } from './../progressBar.js';
import { AssignmentCountdownResponse, AssignmentDeliverResponse, AssignmentPickupResponse } from '../types/responses/TraderResponses.js';
import { AdvApi } from './../AdvApi.js';
import countdown from "../utilities/countdown.js";
import { commonMessages, gameLogger } from '../utilities/gameLogger.js';

const traderModule = {
    selected: null,
    progressBar: null as ProgressBar,
    init() {
        document
            .getElementById("start_trader_assignment")
            .addEventListener("click", () => this.newAssignment());
        [...document.getElementsByClassName("trader_assignment")].forEach(
            (element) =>
                element.addEventListener("click", (event) =>
                    traderModule.selectTrade(event)
                )
        );

        this.getTraderAssignmentCountdown();
        // Check if active trader assignment
        if (document.getElementById("traderAssignment_progressBar")) {
            // Calculate progress
            this.progressBar = new ProgressBar(document.getElementById("traderAssignment_progressBar"),
                { currentValue: 0, maxValue: 0, }, false)
            document
                .getElementById("traderAssignment-pick-up")
                .addEventListener("click", () => this.pickUp());
            document
                .getElementById("traderAssignment-deliver")
                .addEventListener("click", () => this.deliver());
        }
    },
    getTraderAssignmentCountdown() {
        // Fix this
        AdvApi.get<AssignmentCountdownResponse>('/').then((response) => {
            let endTime =
                (response.data.traderAssigmentCountdown + 14400) *
                1000;
            let x = setInterval(function () {
                let { remainder, hours, minutes, seconds } =
                    countdown.calculate(endTime);
                if (
                    document.getElementById(
                        "trader_assignments_countdown_time"
                    ) == null
                ) {
                    clearInterval(x);
                } else if (remainder < 1) {
                    document.getElementById(
                        "trader_assignments_countdown_time"
                    ).innerHTML =
                        "Re enter building to get new trader assignments";
                    clearInterval(x);
                } else {
                    document.getElementById(
                        "trader_assignments_countdown_time"
                    ).innerHTML =
                        hours + "h " + minutes + "m " + seconds + "s ";
                }
            }, 1000);
        });
    },
    selectTrade(event) {
        let target = event.currentTarget;
        [...document.getElementsByClassName("trader_assignment")].forEach(
            (element) => {
                if (
                    element === target &&
                    !element.classList.contains("trader_assignment_locked")
                ) {
                    element.classList.add("selected_trader_assignment");
                    if (element.querySelectorAll(".trader_assignment_id")[0]) {
                        this.selected = element
                            .querySelectorAll(".trader_assignment_id")[0]
                            .innerHTML.trim();
                    }
                } else {
                    element.classList.remove("selected_trader_assignment");
                }
            }
        );
    },
    newAssignment() {
        if (document.getElementById("traderAssignment_progressBar")) {
            gameLogger.addMessage("You already have an assigment", true);
        } else if (this.selected === null || !this.selected) {
            gameLogger.addMessage("This assignment is locked", true);
            return false;
        }

        let data = {
            assignment_id: this.selectTrade
        };

        AdvApi.post("/trader/assignment/new", data).then((response) => {
            // updateHunger(responseText.newHunger);

            // TODO: Fix this once tab has been resolved
            // updateCountdownTab();

            this.updateAssignmentInterface(
                response.html.hasOwnProperty("assigmment") ?
                    response.html.assignment :
                    ""
            );

            this.progressBar.calculateProgress();
            ClientOverlayInterface.adjustWrapperHeight();

            document
                .getElementById("traderAssignment-pick-up")
                .addEventListener("click", () => this.pickUp());
            document
                .getElementById("traderAssignment-deliver")
                .addEventListener("click", () => this.deliver());
        })
    },
    pickUp() {

        AdvApi.post<AssignmentPickupResponse>('/trader/assignment/update', {}).then((response) => {
            // TODO: Fix this once countdowntab has been resolved
            // updateCountdownTab();
            document.getElementById(
                "traderAssignment_cart_amount"
            ).innerHTML = "" + response.data.cartAmount;
        });
    },
    deliver() {
        if (checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }

        AdvApi.post<AssignmentDeliverResponse>('/trader/assignment/update', {}).then((response) => {
            // updateCountdownTab();
            if (response.data.isAssignmentFinished === true) {
                // Update trader assigment div with new assignment
                document.getElementById(
                    "traderAssignment_current"
                ).innerHTML =
                    response.html.hasOwnProperty("assigmment") ?
                        response.html.assignment :
                        "";

                Inventory.update();
                ClientOverlayInterface.adjustWrapperHeight();
            } else {
                document.getElementById(
                    "traderAssignment_cart_amount"
                ).innerHTML = "" + 0;

                // Fix this
                this.progressBar.setCurrentValue(response.data.delivered);

            }
        });
    },
    updateAssignmentInterface(html: string) {
        document.getElementById("traderAssignment_current").innerHTML = html;
    }
};
export default traderModule;
