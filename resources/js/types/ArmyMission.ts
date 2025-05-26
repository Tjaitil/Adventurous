export interface ArmyMission {
  tabElement?: HTMLElement;
  missionID?: number;
  getReportButton?: HTMLButtonElement;
  cancelMissionReportButton?: HTMLButtonElement;
  timeContainer: HTMLElement;
  intervalID: null | number;
  endTime: number;
}
