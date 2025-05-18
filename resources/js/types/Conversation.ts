export interface conversationSegmentResponse {
    conversation_segment: ConversationSegment;
}

export interface ConversationSegment {
    header?: string;
    index: string;
    options: ConversationOption[];
    client_events?: ConversationClientEvent[];
}
export interface ConversationOption {
    person: string | null;
    text: string;
    next_key: 'Q' | 'q' | 'r' | 'S' | 'end';
    container: 'A' | 'B';
    client_callback?: ConversationCallback;
    option_values?: object;
    id: number;
}
export interface ConversationRequest {
    person: string;
    is_starting: boolean;
    selected_option?: number;
}

export type ConversationCallback =
    | 'GameTravelCallback'
    | 'LoadZinsStoreCallback';

export type ConversationClientEvent = 'InventoryChangedEvent';
