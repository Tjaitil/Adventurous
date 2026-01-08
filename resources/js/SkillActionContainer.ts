import { AdvApi } from './AdvApi';
import { Game } from './advclient';
import { jsUcWords, jsUcfirst } from './utilities/uppercase';
import type { CropResource } from './types/CropResource';
import { LevelManager } from './LevelManager';
import countdown from './utilities/countdown';
import { ClientOverlayInterface } from './clientScripts/clientOverlayInterface';
import { AssetPaths } from './clientScripts/ImagePath';

export class SkillActionContainer {
  private typeData = <CropResource[] | MineralResource[]>[];
  public workforceData: object;
  private workforceElement = <HTMLInputElement>(
    document.getElementById('workforce_amount')
  );
  public intervalID: number;
  public cancelActionButton = <HTMLElement>(
    document.getElementById('cancel-action')
  );
  public startActionButton = <HTMLElement>document.getElementById('do-action');
  public finishActionButton = <HTMLElement>(
    document.getElementById('finish-action')
  );
  public infoActionElement = <HTMLElement>(
    document.getElementById('info-action-element')
  );
  public countdownElement = <HTMLElement>document.getElementById('time');
  public countdownCancelledManually: boolean = false;

  public actionText: string;
  public noActionText: string;

  public selectedActionType = <HTMLInputElement>(
    document.getElementById('selected-action-type')
  );

  constructor(actionText: string, noActionText: string) {
    this.actionText = actionText;
    this.noActionText = noActionText;
  }

  public addSelectEvent() {
    [...document.getElementsByClassName('item-type')].forEach(element =>
      { element.addEventListener('click', event => { this.showSelect(event); }); },
    );
  }

  public getWorkforceAmount(): number {
    if (!this.workforceElement) {
      return 0;
    }
    return parseInt(this.workforceElement.value);
  }

  public getSelectedType(): string {
    return this.selectedActionType.value;
  }

  public setAvailableWorkforce(amount: number) {
    const available_workforce_input = document.getElementById(
      'data_container_avail_workforce',
    );

    available_workforce_input.innerText = '(' + amount + ')';
  }

  public showSelect(event) {
    const targetElement = event.currentTarget;
    const clone = targetElement.cloneNode(true);
    clone.removeAttribute('onclick');
    const item = targetElement.getAttribute('alt');
    console.log(item);

    [...document.getElementsByClassName('item-type')].forEach(element => {
      if (targetElement === element) {
        element.classList.add('item_selected');
      } else {
        element.classList.remove('item_selected');
      }
    });

    clone.style.border = 'none';
    const div = document.getElementById('data_form');
    div.style.visibility = 'visible';
    document.getElementById('data').style.visibility = 'visible';
    this.selectedActionType.value = jsUcWords(item);

    const levelInputField = <HTMLInputElement>(
      document.getElementsByName('level')[0]
    );

    let matchedType: CropResource | MineralResource;

    // Check wether or not the player are in crops or mine
    if (Game.getProperty('building') === 'crops') {
      const data = this.typeData as CropResource[];
      matchedType = data.find(type => type.crop_type === item);
      if (matchedType === undefined) return;

      const seedInputField = <HTMLInputElement>(
        document.getElementsByName('seeds')[0]
      );

      seedInputField.value = matchedType.seed_required + '';

      levelInputField.value = matchedType.farmer_level + '';
    } else if (Game.getProperty('building') === 'mine') {
      const data = this.typeData as MineralResource[];
      console.log(data);
      matchedType = data.find(type => type.mineral_ore === item);
      if (matchedType === undefined) return;

      const permitsInputField = <HTMLInputElement>(
        document.getElementsByName('permits')[0]
      );
      permitsInputField.value = matchedType.permit_cost + '';

      levelInputField.value = matchedType.miner_level + '';
    } else {
      return;
    }
    let levelRequired: number;

    const baseReduction = Number(
      matchedType.time * (this.workforceData['efficiency_level'] * 0.01),
    ).toFixed(2);
    const perWorkforce = (Number(matchedType.time) * 0.005).toFixed(2);

    const input = document.getElementsByName('level')[0];
    LevelManager.showHasLevelRequired('miner', levelRequired, input);

    document.getElementById('reduction_time').innerText =
      '- ' + baseReduction + 's ' + '& - ' + perWorkforce + 's each worker';

    const experienceInputField = <HTMLInputElement>(
      document.getElementsByName('experience')[0]
    );
    const locationInputField = <HTMLInputElement>(
      document.getElementsByName('location')[0]
    );

    experienceInputField.value = matchedType.experience + '';
    locationInputField.value = jsUcfirst(matchedType.location);

    const timeInputField = <HTMLInputElement>(
      document.getElementsByName('time')[0]
    );
    timeInputField.value = matchedType.time + ' s';

    const selectedFigure = document.getElementById('selected_item');
    if (selectedFigure.children.length == 0) {
      selectedFigure.appendChild(clone);
    } else {
      const img = <HTMLImageElement>selectedFigure.children[0];
      img.src = AssetPaths.getImagePath(item + '.png');
      img.alt = item;
      img.style.border = '';
    }
  }
  public fetchData(site: 'crops' | 'mine') {
    const url = site === 'crops' ? '/crops/data' : '/mine/data';

    AdvApi.get<GetSkillActionDataRequest>(url).then(response => {
      this.workforceData = response.workforce;
      if (Game.getProperty('building') === 'crops') {
        this.typeData = response.crops;
      } else {
        this.typeData = response.minerals;
      }
      document
        .getElementById('workforce_amount')
        .setAttribute('max', response.workforce.avail_workforce + '');
    });
  }

  public startCountdownAndUpdateUI({
    endTime,
    type,
  }: SkillActionCountdownData) {
    this.countdownCancelledManually = false;

    this.intervalID = setInterval(() => {
      const { remainder, hours, minutes, seconds } =
        countdown.calculate(endTime);
      if (this.countdownCancelledManually) {
        clearInterval(this.intervalID);
        return;
      }
      if (remainder < 0 && type) {
        clearInterval(this.intervalID);
        this.cancelActionButton.style.display = 'none';
        this.finishActionButton.style.display = 'inline-block';
        this.infoActionElement.innerHTML = 'Finished';
        this.countdownElement.innerHTML = '';
      } else if (remainder < 0 || !type) {
        this.countdownCancelledManually = true;
        clearInterval(this.intervalID);
        this.cancelActionButton.style.display = 'none';
        this.finishActionButton.style.display = 'none';
        this.infoActionElement.innerHTML = this.noActionText;
        this.countdownElement.innerHTML = '';
      } else {
        this.cancelActionButton.style.display = 'inline-block';
        this.finishActionButton.style.display = 'none';
        this.infoActionElement.innerHTML =
          this.actionText + ' ' + jsUcWords(type);
        this.countdownElement.innerHTML =
          hours + 'h ' + minutes + 'm ' + seconds + 's ';
      }
    }, 1000);

    setTimeout(() => { ClientOverlayInterface.adjustWrapperHeight(); }, 1100);
  }

  public clearCountdownAndUpdateUI() {
    this.countdownCancelledManually = true;
    clearInterval(this.intervalID);
    this.cancelActionButton.style.display = 'none';
    this.finishActionButton.style.display = 'none';
    this.infoActionElement.innerHTML = this.noActionText;
    this.countdownElement.innerHTML = '';
  }
}

export interface SkillActionCountdownData {
  endTime: number;
  type: string;
}

export interface GetSkillActionDataRequest {
  workforce: {
    avail_workforce: number;
  };
  crops: CropResource[];
  minerals: [];
}

export interface MineralResource {
  mineral_type: string;
  mineral_ore: string;
  miner_level: number;
  experience: number;
  time: number;
  min_per_period: number;
  max_per_period: number;
  permit_cost: number;
  location: string;
}
