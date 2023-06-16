import { advAPIResponse } from './../Responses/AdvResponse';
export interface AssignmentCountdownResponse extends advAPIResponse {
    data: {
        traderAssigmentCountdown: number;
    }
}

export interface AssignmentDeliverResponse extends advAPIResponse {
    data: {
        isAssignmentFinished: boolean;
        delivered: number;
    }
}

export interface AssignmentPickupResponse extends advAPIResponse {
    data: {
        cartAmount: number;
    }
}