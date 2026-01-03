import { Inventory } from './../clientScripts/inventory';
import { selectItemEvent } from '../ItemSelector';
import { GameLogger } from '../utilities/GameLogger';
import { AdvApi } from './../AdvApi';
import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface';
import { ProgressBar } from './../progressBar';
import {
  RecruitWorkerRequest,
  RestoreHealthRequest,
} from './../types/requests/TavernRequests';
import {
  GetHealDataResponse,
  RestoreHealthResponse,
} from './../types/Responses/TavernResponses';
import { HUD } from '../clientScripts/HUD';

const tavernModule = {
  init() {
    document
      .getElementById('tavern-eat-button')
      .addEventListener('click', () => this.eat());
    selectItemEvent.addSelectEvent();
    [...document.getElementsByClassName('tavern-worker-recrute')].forEach(
      element =>
        { element.addEventListener('click', event => this.recruitWorker(event)); },
    );
    const hungerBar = document.getElementById('hunger_progressBar');
    const tavernHungerBar = hungerBar.cloneNode(true);
    const eatDiv = document.getElementById('eat');
    eatDiv.insertBefore(tavernHungerBar, eatDiv.children[1]);
  },
  recruitWorker(event) {
    const element = event.target.closest('.tavern-worker');
    const type = element
      .querySelectorAll('.tavern-worker-type')[0]
      .innerText.trim();
    if (!type) return false;
    const level = element.querySelectorAll('.tavern-worker-level')[0]
      ? element.querySelectorAll('tavern-worker-level')[0].innerText.trim()
      : false;

    const data: RecruitWorkerRequest = {
      type,
      level,
    };

    AdvApi.post('/tavern/recruit', data).then(response => {
      const container = event.target.closest('.tavern-worker');
      document
        .getElementById('tavern-workers-grid-container')
        .removeChild(container);
    });
  },
  getHealingAmount(item) {
    if (item.length == 0) return false;

    AdvApi.get<GetHealDataResponse>(
      '/hunger/item/get' + new URLSearchParams().set('item', item),
    ).then(response => {
      if (response.data.heal === 0) {
        document.getElementById('item_healing_amount').innerText =
          'No healing from this item';
      } else {
        document.getElementById('item_healing_amount').innerText =
          'Healing per item ' + response.data.heal;
      }
      ClientOverlayInterface.adjustWrapperHeight();
    });
  },
  eat() {
    const item = document
      .getElementById('selected')
      .querySelectorAll('figure')[0]
      .children[1].innerHTML.toLowerCase();
    if (item.length == 0) {
      GameLogger.addMessage('ERROR: Select a item to eat!', true);
      return false;
    }
    const inputElement = <HTMLInputElement>(
      document.getElementById('healing-item-amount')
    );

    const amount = parseInt(inputElement.value);

    if (amount == 0 || amount == null) {
      GameLogger.addMessage('ERROR: Select a amount', true);
      return false;
    }
    const data: RestoreHealthRequest = {
      item,
      amount,
    };

    AdvApi.post<RestoreHealthResponse>('/hunger/restore', data).then(
      response => {
        HUD.elements.hungerProgressBar.setCurrentValue(
          response.data.new_hunger,
        );
        Inventory.update();

        document.getElementById('selected').innerHTML = '';
        const inputElement = <HTMLInputElement>(
          document.getElementById('healing-item-amount')
        );

        inputElement.value = '';
      },
    );
  },
};
export default tavernModule;
