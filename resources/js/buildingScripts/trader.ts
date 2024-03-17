import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface';
import { Inventory } from './../clientScripts/inventory';
import { ProgressBar } from './../progressBar';
import { AdvApi } from './../AdvApi';
import countdown from '../utilities/countdown';
import { GameLogger } from '../utilities/GameLogger';
import { advAPIResponse } from '../types/Responses/AdvResponse';
import { updateHunger } from '../clientScripts/hunger';

const traderModule = {
    selectedAssigmentID: null,
    progressBar: null as ProgressBar,
    init() {
        document
            .getElementById('start_trader_assignment')
            .addEventListener('click', () => this.newAssignment());
        [...document.getElementsByClassName('trader_assignment')].forEach(
            element =>
                element.addEventListener('click', event =>
                    traderModule.selectTrade(event),
                ),
        );

        this.getTraderAssignmentCountdown();
        this.setupProgressBar();
    },
    setupProgressBar() {
        const traderAssignmentProgressBar = document.getElementById(
            'traderAssignment_progressBar',
        );
        // Check if active trader assignment
        if (traderAssignmentProgressBar) {
            // Calculate progress
            this.progressBar = new ProgressBar(
                traderAssignmentProgressBar,
                { currentValue: 0, maxValue: 0 },
                false,
            );
            this.progressBar.getProgressValuesFromElement();
            this.addClickEvents();
            this.progressBar.calculateProgress();
        }
    },
    addClickEvents() {
        const pickupButton = document.getElementById(
            'traderAssignment-pick-up',
        );
        const deliverButton = document.getElementById(
            'traderAssignment-deliver',
        );
        if (pickupButton) {
            pickupButton.addEventListener('click', () => this.pickUp());
        }

        if (deliverButton) {
            deliverButton.addEventListener('click', () => this.deliver());
        }
    },
    getTraderAssignmentCountdown() {
        // Fix this
        AdvApi.get<AssignmentCountdownResponse>('/')
            .then(response => {
                const endTime =
                    (response.data.traderAssigmentCountdown + 14400) * 1000;
                const x = setInterval(function () {
                    const { remainder, hours, minutes, seconds } =
                        countdown.calculate(endTime);
                    if (
                        document.getElementById(
                            'trader_assignments_countdown_time',
                        ) == null
                    ) {
                        clearInterval(x);
                    } else if (remainder < 1) {
                        document.getElementById(
                            'trader_assignments_countdown_time',
                        ).innerHTML =
                            'Re enter building to get new trader assignments';
                        clearInterval(x);
                    } else {
                        document.getElementById(
                            'trader_assignments_countdown_time',
                        ).innerHTML =
                            hours + 'h ' + minutes + 'm ' + seconds + 's ';
                    }
                }, 1000);
            })
            .catch(() => false);
    },
    selectTrade(event) {
        const target = event.currentTarget;
        [...document.getElementsByClassName('trader_assignment')].forEach(
            element => {
                if (
                    element === target &&
                    !element.classList.contains('trader_assignment_locked')
                ) {
                    element.classList.add('brightness-150');
                    this.selectedAssigmentID =
                        element
                            .querySelectorAll('.trader_assignment_id')[0]
                            .innerHTML.trim() ?? null;
                } else {
                    element.classList.remove('brightness-150');
                }
            },
        );
    },
    newAssignment() {
        if (document.getElementById('traderAssignment_progressBar')) {
            GameLogger.addMessage('You already have an assigment', true);
        } else if (
            this.selectedAssigmentID === null ||
            !this.selectedAssigmentID
        ) {
            GameLogger.addMessage('This assignment is locked', true);
            return false;
        }

        const data = {
            assignment_id: this.selectedAssigmentID,
        };

        AdvApi.post<AssigmnentNewResponse>('/trader/assignment/new', data)
            .then(response => {
                updateHunger(0);

                // TODO: Fix this once tab has been resolved
                // updateCountdownTab();

                this.updateAssignmentInterface(response.html.TraderAssignment);
                this.setupProgressBar();
                ClientOverlayInterface.adjustWrapperHeight();
                this.addClickEvents();
            })
            .catch(() => false);
    },
    pickUp() {
        const data: AssignmentUpdateRequest = {
            is_delivering: false,
        };

        AdvApi.post<AssignmentPickupResponse>('/trader/assignment/update', data)
            .then(response => {
                // TODO: Fix this once countdowntab has been resolved
                // updateCountdownTab();
                document.getElementById(
                    'traderAssignment-cart-amount',
                ).innerHTML = '' + response.data.cartAmount;
            })
            .catch(() => false);
    },
    deliver() {
        const data: AssignmentUpdateRequest = {
            is_delivering: true,
        };

        AdvApi.post<AssignmentDeliverResponse>(
            '/trader/assignment/update',
            data,
        )
            .then(response => {
                // updateCountdownTab();
                if (response.data.isAssignmentFinished === true) {
                    this.updateAssignmentInterface(
                        response.html.TraderAssignment,
                    );
                    Inventory.update();
                    ClientOverlayInterface.adjustWrapperHeight();
                } else {
                    document.getElementById(
                        'traderAssignment-cart-amount',
                    ).innerHTML = '' + 0;
                    this.progressBar.setCurrentValue(response.data.delivered);
                }
            })
            .catch(() => false);
    },
    updateAssignmentInterface(html: string) {
        const div = document.createElement('div');
        div.innerHTML = html;

        document
            .getElementById('traderAssignment_current')
            .replaceWith(div.children[0]);
    },
};
export default traderModule;

interface AssignmentUpdateRequest {
    is_delivering: boolean;
}

interface AssignmentCountdownResponse extends advAPIResponse {
    data: {
        traderAssigmentCountdown: number;
    };
}

interface AssigmnentNewResponse extends advAPIResponse {
    html: {
        TraderAssignment: string;
    };
}

interface AssignmentDeliverResponse extends advAPIResponse {
    data: {
        isAssignmentFinished: boolean;
        delivered: number;
    };
    html: {
        TraderAssignment: string;
    };
}

interface AssignmentPickupResponse extends advAPIResponse {
    data: {
        cartAmount: number;
    };
}
