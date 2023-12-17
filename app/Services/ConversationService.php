<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Storage;

class ConversationService
{
    /**
     * @return array<string,mixed>|void
     */
    public function getConversation(string $person, string $nextKey, bool $start)
    {
        $conversationFile = $this->loadPerson($person);

        $newIndex = $conversationFile['index'];
        if ($start === true) {
            $newIndex = $conversationFile['index'];
        } else {
            $newIndex = session()->get('conversation_index');
            $newIndex .= $nextKey;
        }
        session()->put('conversation_index', $newIndex);

        $currentConversation = $this->getNextLine($newIndex, $conversationFile);
        if ($currentConversation === null) {
            throw new Exception(sprintf('conversation line %s not found', $newIndex), 422);
        }

        return $currentConversation;
    }

    /*
    Last letter in the index determines what type the last interaction was. For example "hfrq2" is answer to question 2

    Q - main questions
    q - question
    r - response
    rr - random response
    1-9 - number of response/question

    * person|conversation|nextIndex|Funcname/other/end
    *
    */
    /**
     * Last letter in the index determines what type the last interaction was. For example "hfrq2" is answer to question 2
     * Q - main questions
     * q - question
     * r - response
     * rr - random response
     * 1-9 - number of response/question
     * person|conversation|nextIndex|Funcname/other/end
     *
     * @param  array<string, mixed>  $conversation
     * @return array<string, mixed>|void
     */
    public function getNextLine(string $index, array $conversation)
    {
        foreach ($conversation as $key => $value) {
            if ($key === $index) {
                return $value;
                break;
            }
        }
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \InvalidArgumentException
     * @throws \League\Flysystem\FilesystemException
     * @throws \Exception
     */
    public function loadPerson(string $person)
    {
        $person = strtolower($person);

        $file = Storage::disk('gamedata')->json('conversations/'.$person.'.json');
        if (! is_array($file)) {
            throw new Exception('Conversation file not found', 422);
        }

        return $file;
    }
}
