import { WarriorLevelsResource } from './WarriorLevelsResource';
import { TimeResource } from './TimeResource';

export interface WarriorResource {
    warrior_id: number;
    rest: boolean;
    type: string;
    helm: string;
    ammunition: string;
    ammunition_amount: string;
    left_hand: string;
    body: string;
    right_hand: string;
    legs: string;
    boots: string;
    attack: number;
    defence: number;
    fetch_report: boolean;
    army_mission: string;
    levels: WarriorLevelsResource;
    rest_start: number;
    training_countdown: number;
    training_type: string;
    health: number;
    location: string;
    attack_speed: number;
}