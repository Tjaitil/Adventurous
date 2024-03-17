import selectContainer from '../js/selectContainer.js';
import countdown from '../utilities/countdown.js';
scriptLoader.loadScript(['jsonForm'], 'utility');

const adventuresModule = {
    init() {
        if (
            document.getElementById('provide') != null &&
            document.getElementById('provide').children.length > 0
        ) {
            document
                .getElementById('provide')
                .querySelectorAll(':scope > button')[0]
                .addEventListener('click', provide);
            [
                ...document.getElementsByClassName('adventure-provide-button'),
            ].forEach(element =>
                element.addEventListener('click', () => this.provide()),
            );
            // If .warriors is present the player has a warrior role in adventure;
            if (document.querySelectorAll('.warriors').length > 0) {
                addWarriorEvents();
            } else {
                selectItemEvent.addSelectEvent();
            }
        }
        if (document.getElementById('time')) {
            this.getCountdown();
        }
        if (document.getElementById('invite')) {
            let buttons = document
                .getElementById('invite')
                .querySelectorAll('button');
            for (var i = 0; i < buttons.length; i++) {
                if (buttons[i].innerText.indexOf('Toggle') != -1) {
                    buttons[i].addEventListener('click', toggleInvite);
                    break;
                }
            }
            document
                .getElementById('hire-citizen')
                .addEventListener('click', () => this.hireCitizen());
        }
        // Add event to the warriors
        this.addWarriorEvents();

        document
            .getElementById('adventure-start-event')
            .addEventListener('click', () => this.newAdventure());
        document
            .getElementById('adventure-get-report-event')
            .addEventListener('click', () => this.newAdventure());
    },
    intervalID: null,
    getCountdown() {
        let data = 'model=Adventures' + '&method=getCountdown';
        ajaxJS(data, function (response) {
            if (response[0] != false) {
                let responseText = response[1];
                let endTime = responseText['adventure_countdown'] * 1000;
                let status = responseText['adventure_status'];

                this.intervalID = setInterval(() => {
                    let { remainder, hours, minutes, seconds } =
                        countdown.calculate(endTime);

                    document.getElementById('time').innerHTML =
                        hours + 'h ' + minutes + 'm ' + seconds + 's ';
                    if (remainder < 0 && status === '1') {
                        clearInterval(this.intervalID);
                        document.getElementById(
                            'adventure-get-report-event',
                        ).style.display = 'block';
                        document.getElementById('time').innerHTML = 'Finished';
                        document.getElementById(
                            'adventure-leave-event',
                        ).style.display = 'none';
                    } else if (remainder < 0) {
                        clearInterval(this.intervalID);
                        document.getElementById(
                            'adventure-get-report-event',
                        ).style.display = 'none';
                        document.getElementById('time').innerHTML = '';
                    }
                }, 1000);
            }
        });
    },
    newAdventure() {
        let form = document.getElementById('data_form');
        if (!form.reportValidity()) {
            gameLogger.addMessage(
                'Please fill out all required inputs in the form',
                true,
            );
            return false;
        }
        let JSON_data = JSONForm(form);
        let data =
            'model=SetAdventure' +
            '&method=newAdventure' +
            '&JSON_data=' +
            JSON_data;
        ajaxP(data, function (response) {
            if (response[0] !== false) {
                inputHandler.fetchBuilding('adventures');
            }
        });
    },
    handle(figure) {
        console.log(figure);
        select(figures[i]);
        show_title(figures[i], false);
    },
    checkLevel() {
        var select = document.getElementById('diff_select');
        var difficulty = select[select.selectedIndex].value;
        var difficulties = {
            medium: 5.0,
            hard: 12,
        };
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if (this.responseText.indexOf('ERROR') != -1) {
                    return false;
                }
                if (this.responseText < difficulties[difficulty]) {
                    document
                        .getElementById('new_adventure')
                        .querySelectorAll('button')[0].disabled = true;
                    document
                        .getElementById('diff_select')
                        .setCustomValidity(
                            'Adventurer respect too low for this difficulty',
                        );
                } else {
                    document
                        .getElementById('new_adventure')
                        .querySelectorAll('button')[0].disabled = false;
                    document
                        .getElementById('diff_select')
                        .setCustomValidity('');
                }
            }
        };
        ajaxRequest.open(
            'GET',
            'handlers/handler_ses.php?variable=adventurer_respect',
        );
        ajaxRequest.send();
    },
    toggleInvite() {
        let data = 'model=SetAdventure' + '&method=toggleInvite';
        ajaxP(data, function (response) {
            if (response[0] != false) {
                let div = document.getElementById('invite');

                let p = div.querySelectorAll('p');
                for (var i = 0; i < p.length; i++) {
                    if (p[i].innerText.indexOf('invite') != -1) {
                        p[i].innerHTML = 'Leader invite only: ' + response[1];
                        break;
                    }
                }
                if (response[1] == 'off') {
                    for (i = 0; i < 4; i++) {
                        div.children[i].style.display = 'none';
                    }
                } else {
                    for (var x = 0; x < 4; x++) {
                        div.children[x].style.display = 'inline';
                    }
                }
            }
        });
    },
    hireCitizen() {
        let selectElement = document.getElementById('adventure-citizen-role');
        let role = selectElement.options[selectElement.selectedIndex].value;
        if (!role.length > 0) {
            gameLogger.addMessage('You need to select a role');
            gameLogger.logMessages();
            return false;
        }
        let people = document
            .getElementById('people')
            .querySelectorAll('figcaption');
        // Check if role has been filled
        if (people[selectElement.selectedIndex - 1].innerHTML !== 'None') {
            gameLogger.addMessage('That role is already filled');
            gameLogger.logMessages();
            return false;
        } else if (selectElement.selectedIndex - 1 === 3) {
            gameLogger.addMessage(
                "Citizens can't be hired for the warrior role",
            );
            gameLogger.logMessages();
            return false;
        }

        let data =
            'model=SetAdventure' + '&method=hireCitizen' + '&role=' + role;
        ajaxP(data, response => {
            if (response[0] !== false) {
                inputHandler.fetchBuilding('adventures');
                updateInventory();
            }
        });
    },
    showAdventure(id) {
        if (!id) return false;
        let data = 'model=Adventures' + '&method=getAdventure' + '&id=' + id;
        ajaxG(data, response => {
            if (response[0] != false) {
                data = response[1].split('|');
                console.log(data);
                var div = document.getElementById('show_adventure');
                var tr = div.getElementsByTagName('TR')[1];
                var td = tr.children;
                for (var i = 0; i < data.length; i++) {
                    td[i].innerHTML = data[i];
                }
                div.style = 'display: inline';
            }
        });
    },
    joinAdventure(id) {
        if (!id) return false;
        let data =
            'model=AdventureRequest' + '&method=joinAdventure' + '&id=' + id;
        ajaxP(data, function (response) {
            console.log(response);
            if (response[0] != false) {
                inputHandler.fetchBuilding('adventures');
            }
        });
    },
    timer: null,
    chk_me() {
        clearTimeout(timer);
        this.timer = setTimeout(() => this.checkUser(), 1000);
    },
    checkUser() {
        let div = document.getElementById('invite');
        let input = div.querySelectorAll('input')[0].value;
        let field = div.querySelectorAll('span')[0];
        let data =
            'model=Adventures' + '&method=checkUser' + '&username=' + input;
        ajaxG(data, response => {
            if (response[0] != false) {
                if (response[1] == '') {
                    field.innerHTML = "User doesn't exists!";
                } else {
                    field.innerHTML =
                        jsUcfirst(input) + ' ' + 'is a' + ' ' + response[1];
                }
            }
        });
    },
    adventureRequest(id, route) {
        if (!id || !route) return false;
        var name = false;
        if (route == 'invite') {
            name = document.getElementById('invite').children[0].value;
        }
        var data =
            'model=AdventureRequest' +
            '&method=request' +
            '&id=' +
            id +
            '&route=' +
            route +
            '&invitee=' +
            name;
        ajaxP(data, function (response) {
            if (response[0] !== false) {
                // reset form
            }
        });
    },
    provide() {
        let item = false;
        let quantity = false;
        let warrior_check = false;

        // If warriors is 0 then the players has another role than warrior
        if (document.getElementsByClassName('warriors').length === 0) {
            if (document.getElementById('selected').children.length == 0) {
                gameLogger.addMessage('Please select a item');
                gameLogger.logMessages();
                return false;
            }

            // Retrieve selected item
            let itemData = selectedCheck(true);
            if (!itemData.item) {
                gameLogger.addMessage('Please select a valid item', true);
                return false;
            } else if (!itemData.amount) {
                gameLogger.addMessage('Please select a valid amount', true);
                return false;
            }
        } else {
            warrior_check = warriorsCheck();
            if (warrior_check.length == 0) {
                gameLogger.addMessage('Please select warriors', true);
                return false;
            }
        }
        let data =
            'model=SetAdventure' +
            '&method=provide' +
            '&item=' +
            item +
            '&quantity=' +
            quantity +
            '&warrior_check=' +
            warrior_check;
        ajaxP(data, response => {
            if (response[0] !== false) {
                let responseText = response[1];
                document
                    .getElementById('requirements')
                    .getElementsByTagName('tbody')[0].innerHTML =
                    responseText.html[0];
                if (typeof warrior_check !== 'undefined') {
                    document.getElementById('provide').innerHTML =
                        responseText.html[1];
                } else {
                    document.getElementById('selected').innerHTML = '';
                    updateInventory();
                    document
                        .getElementById('provide')
                        .querySelectorAll('input')[0].value = '';
                }
            }
        });
    },
    updateAdventure() {
        let data = 'model=UpdateAdventure' + '&method=updateAdventure';
        ajaxP(data, response => {
            if (response[0] !== false) {
                let responseText = response[1];
                openNews(responseText.html, true);
                // Add events to item elements
                itemTitle.addItemClassEvents();
                document
                    .getElementById('adventure-navigate-back')
                    .addEventListener('click', () =>
                        inputHandler.fetchBuilding('adventures'),
                    );
                updateInventory();
            }
        });
    },
    leaveAdventure() {
        let data = 'model=Adventures' + '&method=leaveAdventure';
        ajaxP(data, response => {
            if (response[0] !== false) {
                inputHandler.fetchBuilding('adventures');
            }
        });
    },
};

export default adventuresModule;
