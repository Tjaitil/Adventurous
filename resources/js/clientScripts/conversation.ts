import { gameTravel } from './gameTravel';
import { tutorial } from './tutorial';
import { Game } from '../advclient';
import { inputHandler } from './inputHandler';
import { GameLogger } from '../utilities/GameLogger';
import { GamePieces } from './gamePieces';
import viewport from './viewport';
import { ClientOverlayInterface } from './clientOverlayInterface';
import { CustomFetchApi } from '../CustomFetchApi';
import { AssetPaths } from './ImagePath';
import { jsUcWords } from '../utilities/uppercase';

addEventListener('load', function () {
  conversation.conversationDiv = document
    .getElementById('conversation-text-wrapper')
    .querySelectorAll('ul')[0];
  document
    .getElementById('conversation-container')
    .querySelectorAll('img')[0]
    .addEventListener('click', () => conversation.endConversation());
  /*conversation.toggleButton();*/
  conversation.button = <HTMLButtonElement>(
    document.getElementById('conv_button')
  );
  conversation.button.addEventListener('click', conversation.addNextEvent);
});
export class conversation {
  private static active = false;
  public static conversationDiv = null;
  public static button: HTMLButtonElement = null;
  private static buttonToggle = false;
  private static currrentPerson = null;
  private static persons = [];
  private static endEvents = [];
  private static personContainerA = null;
  private static personContainerB = null;
  private static currentConversationSegment: ConversationSegment = null;
  private static selectedConversationOption: ConversationOption = null;
  private static conversationHeader: HTMLElement = null;
  private static conversationContainer: HTMLElement = null;
  private static conversationTextWrapper: HTMLElement = null;

  public static get isActive() {
    return this.active;
  }

  public static setup() {
    this.conversationContainer = document.getElementById(
      'conversation-container',
    );
    this.conversationHeader = document.getElementById('conversation-header');
    this.personContainerA = document.getElementById('conversation-a');
    this.personContainerB = document.getElementById('conversation-b');
    this.conversationTextWrapper = document.getElementById(
      'conversation-text-wrapper',
    );
  }

  public static addNextEvent() {
    conversation.button.addEventListener('click', event =>
      conversation.handleUserEvent(event),
    );
  }
  public static checkConversation() {
    if (this.conversationContainer.style.visibility === 'visible') {
      return true;
    } else {
      return false;
    }
  }
  public static async loadConversation(character) {
    const characterObject = GamePieces.characters.find(object => {
      return object.displayName === character;
    });
    let person = (this.persons[0] = characterObject.displayName);
    person = character;
    if (person.length <= 2) {
      return false;
    }

    this.active = true;
    this.persons = [];
    // Close news to prevent players having conversation and also being in building
    ClientOverlayInterface.hide();

    // If index is undefined, set it to false
    const h = document.createElement('h2');
    h.innerText = 'Loading...';
    h.id = 'loading_message';
    if (this.conversationContainer.style.visibility !== 'visible') {
      this.conversationContainer.style.scale = '1';
      this.conversationContainer.style.visibility = 'visible';
    }
    this.conversationDiv.appendChild(h);
    if (Game.properties.device == 'mobile') {
      const conversationContainerHeight = viewport.height * 0.4;
      if (conversationContainerHeight > 170) {
        this.conversationContainer.style.height = '170px';
      } else {
        this.conversationContainer.style.height =
          conversationContainerHeight + 'px';
      }
    }

    const params = new URLSearchParams();
    this.currrentPerson = person;
    params.set('person', this.currrentPerson);
    params.set('is_starting', 'true');
    await this.getNextSegment('/conversation/next?' + params.toString());
  }
  public static endConversation() {
    for (const i of this.endEvents) {
      i();
    }
    this.conversationDiv.innerHTML = '';
    this.active = false;
    this.conversationContainer.style.visibility = 'hidden';
    this.conversationContainer.style.scale = '0.5';
    this.setButtonToggle(false);
    this.personContainerA.style.visibility = 'hidden';
    this.personContainerB.style.visibility = 'hidden';

    this.endEvents = [];
  }
  private static setButtonToggle(value: boolean) {
    this.buttonToggle = value;
  }
  private static handleToggleButton() {
    if (this.buttonToggle === false) {
      this.button.style.display = '';
      this.conversationHeader.innerText = '';
    } else {
      this.button.style.display = 'none';
      const text = (this.currentConversationSegment.header ??=
        'Select an answer');
      this.conversationHeader.innerText = text;
    }
  }
  private static handleCallbacks() {
    const objArray = [];
    let triggerEvent: CallableFunction = () => {};
    switch (this.selectedConversationOption.callback) {
      case 'fetchBuilding':
        triggerEvent = () => {
          inputHandler.fetchBuilding(String(objArray[1]));
        };
        break;
      case 'loadConversation':
        if (objArray[2] !== 'undefined') {
          conversation.loadConversation(String(objArray[1]));
        } else {
          conversation.loadConversation(String(objArray[1]));
        }
        break;
      case 'relocateHassen':
        triggerEvent = () => {
          tutorial.relocateHassen([Number(objArray[1]), Number(objArray[2])]);
        };
        break;
      case 'checkStep':
        triggerEvent = () => {
          tutorial.checkStep;
        };
        break;
      case 'setNextStep':
        triggerEvent = () => {
          tutorial.setNextStep();
        };
        break;
      case 'setHuntedStatus':
        triggerEvent = () => {
          GamePieces.player.setHuntedStatus(true);
        };
        break;
      case 'showBuilding':
        triggerEvent = () => {
          tutorial.showBuilding(objArray[1]);
        };
        break;
      case 'gameTravel.travel':
        triggerEvent = () => {
          gameTravel.travel(
            this.selectedConversationOption.option_value,
            this.currrentPerson,
          );
        };
        break;
      default:
        break;
    }
    triggerEvent();
  }
  private static async getNextSegment(url: string): Promise<void> {
    CustomFetchApi.get<conversationSegmentResponse>(url)
      .then(response => {
        this.currentConversationSegment = response.conversation_segment;
        this.selectedConversationOption = null;
        this.handleNextLine();
      })
      .catch(error => {
        console.log(error);
        GameLogger.addMessage('An error occured', true);
      });
  }
  private static async handleUserEvent(event: MouseEvent) {
    const eventTargetElement = <HTMLElement>event.currentTarget;
    let text = '';
    if (eventTargetElement.tagName === 'BUTTON') {
      text = this.conversationTextWrapper.querySelectorAll('li')[0].innerText;
    } else {
      text = eventTargetElement.innerText;
    }

    this.selectedConversationOption =
      this.currentConversationSegment.options.find(
        segment => segment.text === text,
      );

    this.handleCallbacks();
    if (this.selectedConversationOption.next_key == 'end') {
      this.endConversation();
      return false;
    }

    const params = new URLSearchParams();
    params.set('person', this.currrentPerson);
    params.set('nextKey', this.selectedConversationOption.next_key);
    this.getNextSegment('/conversation/next?' + params.toString());
  }
  private static handleNextLine() {
    if (this.currentConversationSegment.options.length === 1) {
      this.setButtonToggle(false);
      this.handleToggleButton();
    } else {
      this.setButtonToggle(true);
      this.handleToggleButton();
    }

    this.resetEventButton();
    this.makeLinks();
  }
  private static togglePerson() {
    if (this.selectedConversationOption?.person == null) {
      this.personContainerB.style.visibility = 'hidden';
      this.personContainerA.style.visibility = 'hidden';
      return;
    }

    if (
      this.selectedConversationOption.container === 'A' ||
      this.selectedConversationOption.person !== 'player'
    ) {
      this.personContainerA.style.visibility = 'visible';
      this.personContainerA.src = AssetPaths.getImagePath(
        this.selectedConversationOption.person + '.png',
      );
      this.conversationHeader.innerText = jsUcWords(
        this.selectedConversationOption.person,
      );
      this.personContainerB.style.visibility = 'hidden';
    } else {
      this.personContainerB.src = AssetPaths.getImagePath(
        'character image.png',
      );
      this.personContainerB.style.visibility = 'visible';
      this.conversationHeader.innerHTML = 'You';
      this.personContainerA.style.visibility = 'hidden';
    }
  }
  private static resetEventButton() {
    const clone = <HTMLButtonElement>this.button.cloneNode(true);
    clone.addEventListener('click', event => this.handleUserEvent(event));
    this.button.replaceWith(clone);
    this.button = clone;
  }
  private static makeLinks() {
    this.conversationDiv.innerHTML = '';
    if (this.currentConversationSegment.options.length > 1) {
      this.currentConversationSegment.options.forEach(segment => {
        const li = document.createElement('li');
        li.innerHTML = segment.text;
        li.className = 'conv-link';
        li.style.cursor = 'pointer';
        li.addEventListener('click', event => this.handleUserEvent(event));
        this.conversationDiv.appendChild(li);
      });
    } else {
      this.selectedConversationOption =
        this.currentConversationSegment.options[0];
      const li = document.createElement('li');
      li.innerHTML = this.selectedConversationOption.text;
      li.className = 'conv-link';
      li.style.cursor = 'auto';
      this.conversationDiv.appendChild(li);
    }
    this.togglePerson();
  }
}

// const highlightInventory = {
//     intervalID: null,
//     set() {
//         this.intervalID = setInterval(function () {
//             let inventory = document.getElementById("inventory");
//             if (inventory.style.backgroundColor === "" || inventory.style.backgroundColor == "rgb(76, 52, 26)") {
//                 inventory.style.backgroundColor = "#986834";
//             } else {
//                 inventory.style.backgroundColor = "#4c341a";
//             }
//         }, 1500);
//     },
//     clear() {
//         document.getElementById("inventory").style.backgroundColor = "#4c341a";
//         clearInterval(this.intervalID);
//     },
// };

interface conversationSegmentResponse {
  conversation_segment: ConversationSegment;
}

interface ConversationSegment {
  header?: string;
  options: ConversationOption[];
}
interface ConversationOption {
  person: string | null;
  text: string;
  next_key: 'Q' | 'q' | 'r' | 'end';
  container: 'A' | 'B';
  callback?: string;
  option_value?: string;
}

(<any>window).conversation = conversation;
