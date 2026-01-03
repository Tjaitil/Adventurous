import { ajaxJS } from '../ajax';

const warriorSelect = {
  addWarriorEvents() {
    [...document.getElementsByClassName('warrior-select-card')].forEach(
      element => {
        element.addEventListener('click', event => { this.selectWarrior(event); });
        element
          .querySelectorAll('button')[0]
          .addEventListener('click', event => { this.flipWarriorCard(event); });
      },
    );
  },
  selectWarrior(event) {
    const div = event.currentTarget;
    const checkbox = <HTMLInputElement>(
      div.querySelectorAll('input[type=checkbox]')[0]
    );

    // If event target is not the checkbox, toggle the checked property
    if (!checkbox.checked) {
      checkbox.checked = true;
      document.getElementById(div.id).style.border = '3px ridge #5f4121';
      this.selectedWarriorAmount += 1;
    } else {
      document.getElementById(div.id).style.border = '1px ridge #5f4121';
      checkbox.checked = false;
      this.selectedWarriorAmount -= 1;
    }
    this.updateSelectedWarriors();
  },
  warriorsCheck() {
    const warriors_div = document.getElementsByClassName('warrior-select-card');
    const warrior_check = [];
    document.getElementsByClassName('warrior-select-card');
    for (let i = 0; i < warriors_div.length; i++) {
      const checkbox = <HTMLInputElement>(
        warriors_div[i].querySelectorAll('input[type=checkbox]')[0]
      );
      if (checkbox.checked) {
        const warrior = warriors_div[i].id;
        const warror_id = warrior.replace('warrior_', '');
        warrior_check.push(warror_id);
      }
    }
    return warrior_check;
  },
  flipWarriorCard(event) {
    const div = event.currentTarget;
    if (div.style.transform.indexOf('180') !== -1) {
      div.style.transform = 'rotateY(0deg)';
    } else {
      div.style.transform = 'rotateY(180deg)';
    }
  },
  selectedWarriorAmount: 0,
  updateSelectedWarriors() {
    const warrior_amount = <HTMLElement>(
      document.getElementById('selected_warrior_amount')
    );
    if (!warrior_amount) return false;

    warrior_amount.innerHTML = '' + this.selectedWarriorAmount;
  },
  getAvailableWarriors() {
    if (!document.getElementById('warriors-select-wrapper')) return false;
    const data = 'model=Warriors' + '&method=' + 'getAvailableWarriors';
    ajaxJS(data, response => {
      const responsetext = response[1];
      const div = document.createElement('div');
      div.innerHTML = responsetext.html;
      console.log(div.children[0]);
      document
        .getElementById('warriors-select-wrapper')
        .replaceWith(div.children[0]);
      this.addWarriorEvents();
    });
  },
};
export default warriorSelect;
