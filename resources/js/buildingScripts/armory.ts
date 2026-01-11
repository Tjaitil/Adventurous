import type { ChangeArmorResponse } from '../types/Responses/ArmoryResponses';
import type { changeArmorRequest } from '../types/requests/ArmoryRequests';
import { Inventory } from './../clientScripts/inventory';
import { inputHandler } from '../clientScripts/inputHandler';
import { ItemSelector } from '../ItemSelector';
import { AdvApi } from '../AdvApi';
import type { ArmoryWarrior } from '@/types/Warrior';

export const ArmoryDataLoader = {
  warriors: () => AdvApi.get<ArmoryWarrior[]>('/armory/soldiers'),
};

const armoryModule = {
  init() {
    document
      .getElementById('news_content_main_content')
      .querySelectorAll('button')[0]
      .addEventListener('click', function () {
        inputHandler.fetchBuilding('armycamp');
      });
    ItemSelector.setup();
    ItemSelector.hideSelectedAmountInput();
    this.addClickEvents('all');
    document
      .getElementById('put_on_button')
      .addEventListener('click', () => this.wearArmor());
  },

  addClickEvents(childIndex: number | 'all') {
    let elements;
    if (typeof childIndex === 'string') {
      elements = [...document.getElementsByClassName('armory_view_part')];
    } else {
      // Find armory_view_part inside warriorDiv that has childIndex
      const warriorDiv = document
        .getElementById('warrior_container')
        .getElementsByClassName('armory_view')[childIndex];
      elements = [...warriorDiv.getElementsByClassName('armory_view_part')];
    }

    elements.forEach(element =>
      element.addEventListener('click', event => this.removeArmor(event)),
    );
  },

  toggleOption() {
    const element =
      document.getElementById('selected').children[0].children[1].innerHTML;
    if (element.search('Sword') != -1 || element.search('Dagger') != -1) {
      document.getElementById('type').style.visibility = 'visible';
      ItemSelector.hideSelectedAmountInput();
    } else if (
      element.search('Arrow') != -1 ||
      element.search('Knives') != -1
    ) {
      ItemSelector.showSelectedAmountInput();
    } else {
      document.getElementById('type').style.visibility = 'hidden';
      ItemSelector.hideSelectedAmountInput();
    }
  },

  wearArmor() {
    const selectElement = <HTMLSelectElement>(
      document.getElementById('select_warrior')
    );
    const warrior_id = selectElement.selectedIndex;

    const select = <HTMLSelectElement>document.getElementById('type');
    let hand;
    if (select.style.visibility == 'visible') {
      hand = select.options[select.selectedIndex].value;
    } else {
      hand = '';
    }

    if (!ItemSelector.isItemValid()) return false;

    const item = ItemSelector.selected;

    const data: changeArmorRequest = {
      warrior_id,
      item: item.name,
      hand,
      amount: item.amount,
      is_removing: false,
      part: '',
    };

    AdvApi.post<ChangeArmorResponse>('/api/armory/add', data)
      .then(response => {
        document.getElementById('selected').innerHTML = '';
        this.replaceWarriorContainer(
          response.html.warrior_armory,
          data.warrior_id,
        );
        Inventory.update();
      })
      .catch(() => false);
  },

  removeArmor(event: Event) {
    const partElement = <HTMLElement>event.currentTarget;

    if (!partElement) return false;
    const parent = partElement.closest('.armory_view');
    const warrior_id = parseInt(
      parent.querySelectorAll('.armory_view_warrior_id')[0].innerHTML.trim(),
    );

    if (!parent.querySelectorAll('.armory_view_warrior_id')[0]) return false;
    const part = partElement.classList[1];
    let hand = '';

    if (part === 'left_hand') {
      hand = 'left_hand';
    } else if (part === 'right_hand') {
      hand = 'right_hand';
    }
    const item = partElement.title;
    if (item === 'none') return false;

    const data: changeArmorRequest = {
      warrior_id,
      item,
      hand,
      amount: 1,
      is_removing: true,
      part,
    };

    AdvApi.post<ChangeArmorResponse>('/api/armory/remove', data)
      .then(response => {
        document.getElementById('selected').innerHTML = '';
        this.replaceWarriorContainer(
          response.html.warrior_armory,
          data.warrior_id,
        );
        Inventory.update();
      })
      .catch(() => false);
  },

  replaceWarriorContainer(newContainer: string, index: number) {
    const parentContainer = document.getElementById('warrior_container');
    const warriorContainer = document.getElementsByClassName('armory_view');
    const replaceIndex = index - 1;

    // Convert newContainer string into object
    const div = document.createElement('div');
    div.innerHTML = newContainer;
    if (!warriorContainer[replaceIndex]) return false;
    parentContainer.replaceChild(
      div.getElementsByClassName('armory_view')[0],
      warriorContainer[replaceIndex],
    );
    this.addClickEvents(replaceIndex);
  },
};
export default armoryModule;
