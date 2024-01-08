<?php

namespace App\Services;

use App\Enums\GameLogTypes;
use Carbon\Carbon;

trait GameLogger
{
    private array $logs = [];

    public function initLog()
    {
        $this->logs = [];
        session()->put('log', $this->logs);
    }

    public function addErrorMessage(string $message)
    {
        $this->addMessage($message, GameLogTypes::ERROR->value);
    }

    public function addInfoMessage(string $message)
    {
        $this->addMessage($message, GameLogTypes::INFO->value);
    }

    public function addWarningMessage(string $message)
    {
        $this->addMessage($message, GameLogTypes::WARNING->value);
    }

    public function addSuccessMessage(string $message)
    {
        $this->addMessage($message, GameLogTypes::SUCCESS->value);
    }

    /**
     * @param  mixed  $message
     * @param  value-of<GameLogTypes>  $type
     * @return void
     */
    public function addMessage($message, string $type)
    {
        $this->logs = session()->get('log') ?? [];

        $this->removeIfOverLength();

        $this->logs[] = [
            'type' => $type,
            'timestamp' => Carbon::now()->format('H:i:s'),
            'text' => $message,
        ];

        session()->put('log', $this->logs);
    }

    private function removeIfOverLength(): bool
    {
        if (count($this->logs) > 100) {
            array_shift($this->logs);

            return true;
        }

        return false;
    }
}
