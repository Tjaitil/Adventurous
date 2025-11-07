import { GamePieces } from './gamePieces';
import { tutorial } from './tutorial';
import { ProgressBar } from '../progressBar';
import viewport from './viewport';
import { itemTitle } from '../utilities/itemTitle';
import { Inventory } from './inventory';
import { gameEventBus } from '@/gameEventsBus';

export const HUD = {
  container: null,
  elements: {
    // controlText: new HTMLElement(),
    controlText: null,
    control_text_building: null as HTMLElement,
    control_text_conversation: null as HTMLElement,
    // hungerProgressBar: new HTMLElement(),
    hungerProgressBar: null as ProgressBar,
    // healthProgressBar: new HTMLElement(),
    healthProgressBar: null as ProgressBar,
    // helpContainer: new HTMLElement(),
    helpContainer: null as HTMLElement,
    // huntedIcon: new HTMLElement(),
    huntedIcon: null as HTMLElement,
    // huntedLocator: new HTMLElement(),
    huntedLocator: null as HTMLElement,
    // iconContainer: new HTMLElement(),
    iconContainer: null as HTMLElement,
    tutorialProgressBar: null as ProgressBar,
  },
  setup(width: number, height: number, top: number, left: number) {
    itemTitle.init(true);

    // Setup game_hud container;
    const container = document.getElementById('game_hud');
    container.style.top = top + 'px';
    container.style.left = left + 'px';
    container.style.width = width + 'px';
    container.style.height = height + 'px';

    const HUDTopPosition = 15;
    const HUDLeftPosition = 20;
    const HUDrowHeight = 46;
    const HUDSpacer = 20;

    // Set width on elements that should be same with as canvas
    // const conversation_container = document.getElementById(
    //     'conversation-container',
    // );

    // if (!(conversation_container instanceof HTMLElement)) {
    //     throw new Error('conversation_container element not found');
    // }

    const gameBorderElement = document.getElementById('canvas-border');
    if (!(gameBorderElement instanceof HTMLElement)) {
      throw new Error('game_border element not found');
    }

    const gameBorderwidth =
      window.getComputedStyle(gameBorderElement).borderWidth;

    const gameCanvasElement = document.getElementById('game_canvas');
    if (!(gameCanvasElement instanceof HTMLElement)) {
      throw new Error('game_canvas element not found');
    }

    // conversation_container.style.top =
    //     gameCanvasElement.offsetTop +
    //     gameCanvasElement.offsetHeight -
    //     conversation_container.offsetHeight +
    //     parseInt(gameBorderwidth) * 2 +
    //     'px';
    document.getElementById('news_content').style.width = width + 'px';

    if (document.getElementById('control').style.display === 'block') {
      const control = document.getElementById('control');
      control.style.top = top + left - 125 + 'px';
      control.style.left = '10px';
      document.getElementById('inventory').style.top = top + 'px';
    }
    const control_text = document.getElementById('control_text');
    control_text.style.top = height - control_text.offsetHeight - 20 + 'px';
    control_text.style.left = left + 20 + 'px';

    // Position the hunger bar
    const hungerProgressBar = document.getElementById('hunger_progressBar');
    hungerProgressBar.style.top = HUDTopPosition + HUDrowHeight * 0 + 'px';
    hungerProgressBar.style.left = HUDLeftPosition + 'px';
    hungerProgressBar.style.width = '250px';
    hungerProgressBar.style.position = 'absolute';

    // const currentHunger = parseInt(
    //   hungerProgressBar.querySelectorAll('.progressBar_currentValue')[0]
    //     .innerHTML,
    // );
    // this.elements.hungerProgressBar = new ProgressBar(
    //   document.getElementById('hunger_progressBar'),
    //   {
    //     currentValue: currentHunger,
    //     maxValue: 100,
    //   },
    // );

    const healthProgressBar = document.getElementById('health_progressBar');
    healthProgressBar.style.top = HUDTopPosition + HUDrowHeight * 0 + 'px';
    healthProgressBar.style.left =
      parseInt(hungerProgressBar.style.left) +
      hungerProgressBar.clientWidth +
      HUDSpacer +
      'px';
    healthProgressBar.style.width = '100px';
    healthProgressBar.style.position = 'absolute';
    // healthProgressBar.querySelectorAll(
    //   '.progressBar_currentValue',
    // )[0].innerHTML = '' + GamePieces.player.health;

    // this.elements.healthProgressBar = new ProgressBar(
    //   document.getElementById('health_progressBar'),
    //   {
    //     currentValue: 100,
    //     maxValue: 100,
    //   },
    // );

    // Position help button and help container
    const help_button = document.getElementById('HUD_help_button');
    help_button.style.top = HUDTopPosition + HUDrowHeight * 0 + 'px';
    help_button.style.left = width - 10 - help_button.offsetWidth + 'px';

    // Assign hunted icon and hunted locator
    this.elements.huntedIcon = document.getElementById('HUD_hunted_icon');
    this.elements.huntedIcon.style.top = 70 + 'px';
    this.elements.huntedIcon.style.left = HUDLeftPosition + 'px';

    this.elements.huntedLocator = document.getElementById('HUD_hunted_locater');
    this.elements.huntedLocator.style.top =
      this.elements.huntedIcon.offsetHeight +
      this.elements.huntedIcon.offsetTop +
      10 +
      'px';
    this.elements.huntedLocator.style.left = HUDLeftPosition + 'px';

    // Position map related elements
    // document.getElementById("toggle_map_icon").style.top = "10px";
    this.elements.iconContainer = document.getElementById(
      'HUD-left-icons-container',
    );
    this.elements.iconContainer.style.left =
      width - this.elements.iconContainer.offsetWidth - 10 + 'px';
    const toggle_map_icon = document.getElementById('toggle_map_icon');

    toggle_map_icon.style.top = HUDTopPosition + HUDrowHeight * 0 + 'px';
    toggle_map_icon.style.left =
      width -
      toggle_map_icon.offsetWidth -
      20 -
      toggle_map_icon.offsetWidth -
      25 +
      'px';

    const mapContainer = document.getElementById('map_container');
    mapContainer.style.top = top + 'px';
    mapContainer.style.left = '98%';

    this.elements.control_text_conversation = document.getElementById(
      'control_text_conversation',
    );
    this.elements.control_text_building = document.getElementById(
      'control_text_building',
    );

    /* If screen is less than 830 set sidebar to be the same top as inventory so that the two are aligned
     * Also align cont_exit button in news content to middle instead of right */
    if (window.screen.width < 830) {
      document.getElementById('sidebar').style.top = top + 'px';
      document.getElementById('inv_toggle_button_container').style.top =
        top + 'px';
      const cont_exit_button = document.getElementById('cont_exit');
      cont_exit_button.style.zIndex = '1';
      cont_exit_button.style.cssFloat = '';
      cont_exit_button.style.margin = '0 auto';
      cont_exit_button.style.marginBottom = '20px';
    }

    gameEventBus.subscribe('PLAYER_HUNTED_UPDATE', ({ isHunted }) => {
      if (isHunted) {
        HUD.elements.huntedIcon.style.visibility = 'visible';
      } else {
        HUD.elements.huntedIcon.style.visibility = 'hidden';
      }
    });

    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', ({ health }) => {
      HUD.elements.healthProgressBar.setCurrentValue(health);
    });
  },
  makeTutorialHUD() {
    const tutorial_progressContainer = document.createElement('div');
    tutorial_progressContainer.setAttribute('id', 'tutorial_progressContainer');
    tutorial_progressContainer.style.top = 60 + 'px';
    tutorial_progressContainer.style.width = viewport.width * 0.75 + 'px';
    tutorial_progressContainer.style.height = 50 + 'px';
    tutorial_progressContainer.classList.add(
      'absolute',
      'right-0',
      'left-0',
      'text-white',
      'mx-auto',
    );
    tutorial_progressContainer.classList.add('right-0');
    const title = document.createElement('p');
    title.innerText = 'Tutorial progress';
    const under_title = document.createElement('p');
    under_title.innerText = '0. Introduction';

    // Create new progressBar and append in to progressContainer
    this.elements.tutorialProgressBar = new ProgressBar(
      document.getElementById('tutorial_progressBar'),
      {
        currentValue: tutorial.step,
        maxValue: tutorial.lastStep,
      },
    );
    tutorial_progressContainer.appendChild(title);
    tutorial_progressContainer.appendChild(
      this.elements.tutorialProgressBar.progressBarElement,
    );
    tutorial_progressContainer.appendChild(under_title);
    document.getElementById('game_hud').appendChild(tutorial_progressContainer);

    // Make exit button
    const button = document.createElement('button');
    button.appendChild(document.createTextNode('Skip tutorial'));
    button.addEventListener('click', tutorial.skipTutorial);
    button.style.position = 'absolute';
    button.style.top = 130 + 'px';
    button.style.left =
      document.getElementById('game_canvas').offsetLeft + 'px';
    button.style.zIndex = '' + 4;
    document.getElementById('game_hud').appendChild(button);
  },
  hideTutorialHUD() {
    document
      .getElementById('game_hud')
      .removeChild(document.getElementById('tutorial_progressContainer'));
  },
};
