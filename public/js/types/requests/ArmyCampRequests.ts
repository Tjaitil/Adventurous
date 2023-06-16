export interface BaseRunWarriorActionRequest {
    warrior_ids: number[];
}

export interface RunSingleWarriorActionRequest {
    warrior_id: number;
}

export interface StartTrainingRequest extends BaseRunWarriorActionRequest {
    training_type: string;
}

export interface ChangeWarriorTypeRequest extends RunSingleWarriorActionRequest {
    new_warrior_type: string;
}

export interface HealWarriorRequest extends RunSingleWarriorActionRequest {
    item: string;
    amount: number;
}

export interface RestWarriorsRequest extends BaseRunWarriorActionRequest {
    is_starting_rest: boolean;
}