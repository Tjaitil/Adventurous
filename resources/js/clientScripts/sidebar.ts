import { clientSettings } from './clientSettings';

export const sidebar = {
  sidebarElement: document.getElementById('sidebar'),
  sidebarToggledButton: document.getElementById('sidebar_button_toggle'),
  isSidebarToggled: false,
  currentTab: null,
  adjustSidebar() {
    const parent = this.sidebarElement.parentElement;
    this.sidebarElement.style.maxWidth = parent.clientWidth + 'px';
  },
  addClickEvent() {
    this.sidebarToggledButton.addEventListener('click', () =>
      this.toggleSidebar(),
    );
    const sidebarTabs = <HTMLElement[]>[
      ...this.sidebarElement.querySelectorAll('.sidebar-tab'),
    ];
    sidebarTabs.forEach((element, index) => {
      if (index === 0) {
        this.currentTab = element.getAttribute('id');
      }
      element.addEventListener('click', event => this.showTab(event));
    });
    this.sidebarElement = document.getElementById('sidebar');
  },
  toggleSidebar() {
    if (this.isSidebarToggled === false) {
      this.sidebarElement.style.visibility = 'visible';
      this.sidebarElement.style.width =
        document.getElementsByTagName('section')[0].clientWidth * 0.4 + 'px';
      this.sidebarElement.style.maxWidth = 'initial';
      this.isSidebarToggled = true;
      this.sidebarToggledButton.style.visibility = 'visible';
      if (window.screen.width < 830) {
        this.sidebarToggledButton.style.cssFloat = 'right';
      }
    } else {
      this.hideTabs();
      this.adjustSidebar();
      this.isSidebarToggled = false;
      this.sidebarToggledButton.style.visibility = 'hidden';
    }
  },
  showTab(event: MouseEvent) {
    const element = <HTMLElement>event.currentTarget;
    if (this.isSidebarToggled === false) {
      this.toggleSidebar();
    }

    const targetTabPanelID = element.getAttribute('aria-controls');
    if (this.currentTab === targetTabPanelID) return;

    if (!targetTabPanelID) return;

    const tabpanels = <HTMLElement[]>[
      ...document
        .getElementById('sidebar-tabpanels')
        .querySelectorAll('.tabpanel'),
    ];
    tabpanels.forEach(element => {
      if (element.id === targetTabPanelID) {
        element.classList.remove('hidden');
        element.classList.remove('absolute');
      } else {
        element.classList.add('hidden');
        element.classList.add('absolute');
      }
    });
  },
  hideTabs() {
    const tabpanels = <HTMLElement[]>[
      ...document
        .getElementById('sidebar-tabpanels')
        .querySelectorAll('.tabpanel'),
    ];
    tabpanels.forEach(element => {
      element.classList.add('hidden');
    });
  },
};
clientSettings.init();
sidebar.adjustSidebar();
sidebar.addClickEvent();
