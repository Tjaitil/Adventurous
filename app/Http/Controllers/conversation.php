<?php

namespace App\Http\Controllers;

use App\libs\controller;
use App\Services\SessionService;

// TODO: Rewrite this
class conversation extends controller
{
    public $index;
    public $text;
    public $POST;
    public $file;
    function __construct(private SessionService $sessionService)
    {

        if (isset($_SESSION['conversation']['conv_index'])) {
            $this->index = $_SESSION['conversation']['conv_index'];
        } else {
            $this->index === false;
            $_SESSION['conversation']['conv_index'] = false;
        }
    }
    public function checkPerson($person)
    {
        $person = strtolower($person);
        $this->file = "../gamedata/conversations/" . $person . ".json";

        if (file_exists($this->file)) {
            return true;
        } else {
            return false;
        }
    }
    public function getConversation($person)
    {
        // Check if conversation with person exist
        if ($this->checkPerson($person)) {
            return file_get_contents($this->file);
        } else {
            return false;
        }
    }
    public function setPerson($POST, $set_index = false)
    {
        // Set new person
        $person = $POST['person'];
        $_SESSION['conversation']['person'] = $person;
        $this->conversationCheck();

        // If the index is false the conversation will begin at the start. If else the conversation will start at specified index
        if ($set_index === true) {
            $this->echoConversation(true);
        } else {
            $this->echoConversation();
        }
    }
    public function conversationCheck()
    {
        // Function to check for different situations which can change the conversation index
        $_SESSION['conversation']['conv_index'];
    }
    public function getNextLine($POST)
    {
        // Loop through the active_dialogues for the match for text clicked on and make next index

        /*
            Last letter in the index determines what type the last interaction was. For example "hfrq2" is answer to question 2
            
            Q - main questions
            q - question
            r - response
            rr - random response
            1-9 - number of response/question
            
            person|conversation|nextIndex|Funcname/other/end
           
           */

        $info = false;
        $this->text = $POST['index'];
        if (strpos($this->text, "|")) {
            $text_array = explode("|", $this->text);
            $info = $text_array[0];
            $item_match = false;
            foreach ($_SESSION['gamedata']['inventory'] as $key) {
                if ($key['item'] === $info) {
                    $item_match = true;
                    $this->POST = $info;
                    break;
                }
            }
            // Find last index
            if ($item_match === true) {
                $conversation = json_decode($this->getConversation($_SESSION['conversation']['person']), true);
                $this->text = explode("|", $conversation[$this->index])[1];
            }
        }
        // Search through current dialogue and find the active dialogue
        $active_dialogues = $_SESSION['conversation']['active_dialogues'];
        for ($i = 0; $i < count($active_dialogues); $i++) {
            if (strpos($active_dialogues[$i], $this->text)) {
                $dialogue_match = explode("|", $active_dialogues[$i]);
                // Check for end index in conversation
                if ($dialogue_match[2] === "end") {
                    $end = true;
                    break;
                }
                // Check for backend methods in dialogue
                if (isset($dialogue_match[4])) {
                    $split = explode("#", $dialogue_match[4]);
                } else {
                    // Set new index for conversation
                    $this->index = $_SESSION['conversation']['conv_index'] . $dialogue_match[2];
                }
                break;
            }
        }
        if (isset($end)) {
            return "end";
        } else if (isset($split)) {
            return array($info, $split);
        } else {
            return 'next';
        }
    }
    public function echoConversation($getIndex = false)
    {
        // Echo the conversation;
        // Get the file containing the conversations with a person
        $conversation = json_decode($this->getConversation($_SESSION['conversation']['person']), true);
        if ($conversation == false) {
            return false;
        }

        // If the index is false, it means the index is not set by getNextLine() and setPerson() is called
        if ($getIndex === true) {
            $_SESSION['conversation']['conv_index'] = $this->index = $conversation['index'];
        }
        if (!isset($conversation[$_SESSION['conversation']['conv_index']])) {
            // Find last Q (conversation)
            $strpos = strrpos($_SESSION['conversation']['conv_index'], "Q");
            $_SESSION['conversation']['conv_index'] = substr($_SESSION['conversation']['conv_index'], 0, $strpos + 1);
        }
        // Check if the index in $conversation is string, if true make it into array before echoing
        if (is_string($conversation[$_SESSION['conversation']['conv_index']])) {
            $active_conversation = $_SESSION['conversation']['active_dialogues'] =
                array($conversation[$_SESSION['conversation']['conv_index']]);
        } else {
            $active_conversation = $_SESSION['conversation']['active_dialogues'] =
                $conversation[$_SESSION['conversation']['conv_index']];
        }
        // Remvoe backend functions name from active conversation
        for ($i = 0; $i < count($active_conversation); $i++) {
            $for_index = explode("|", $active_conversation[$i]);
            if (isset($for_index[4])) {
                array_pop($for_index);
            }
            $active_conversation[$i] = implode("|", $for_index);
        }
        // Return false if the length of conversation array is not over 0;
        if (count($active_conversation) > 0) {
            // Replace @ in dialogue with data
            $active_conversation = $this->dataReplacer($active_conversation);
            echo json_encode(array("conversation" => $active_conversation, "conversationIndex" => $this->index));
        } else {
            return false;
        }
    }
    protected function dataReplacer($active_conversation)
    {
        foreach ($active_conversation as $key => $value) {
            if (strpos($value, '@name')) {
                $active_conversation[$key] = str_replace('@name', $_SESSION['username'], $value);
            }
            // If location is current and the current person is pesr or sailor
            if ((strpos($this->index, "sl") !== false || strpos($this->index, "pr") !== false) &&
                strpos(strtolower($active_conversation[$key]), $this->sessionService->getCurrentLocation()) !== false
            ) {
                unset($active_conversation[$key]);
            }
        }
        return array_values($active_conversation);
    }
}
