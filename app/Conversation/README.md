# Conversation Loop

- [Frontend] User starts request
- [Backend] load the conversation, set the conversation index and return the segment
- [Frontend] User selects an option
    - The selected option index is sent to server
- [Backend]
    1. Match the option index to a segment
    2. Caclulates the next conversation index based on next_key in segment object
    3. Call server_event if segment is specified as such. This will then jump to number 1 and start the process all over again. (See server_event)
    4. Invoke conditionals on conversation_segments if specified and then filter out
    5. Invoke text placeholders and switch out text
    6. The next segment is returned based on next_key.
- Frontend events
    - client_events, happens when the conversation is loaded and displayed
    - client_callback, tied to one option after one option is selected. NOTE: Remember to use next_key "end" if the user is navigated away from the conversation
- Next Key
    - q - question. Next segment is a question. Main question is Q. Often one will revert back to this if current conversation tree cannot be "progressed"
    - r - response. Next segment is a response. Can be numeric when multi-choice responses. E.g r1 / r2
    - S - server_event. Next segment is a server_response.
  - Server event
    - Call an invokable method that returns a new converationIndex based on result. E.g "Success" / "Too Little Gold" / "Full Inventory"
    - Option values
    - Some option specifies option_values that is saved on the ConversationTracker. This options are kept throughout the Conversation and can be used as parameters in server_event or conditional. Option_values are reset when new conversation is started
