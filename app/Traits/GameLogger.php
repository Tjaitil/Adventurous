<?php

namespace App\Traits;

use App\Enums\GameLogTypes;
use Carbon\Carbon;

trait GameLogger
{
    /**
     * @var array<array{type: string, timestamp: Carbon, text: string}>>
     */
    private array $logs = [];

    public function initLog(): void
    {
        $this->logs = [];
        session()->put('log', $this->logs);
    }

    public function addErrorMessage(string $message): void
    {
        $this->logMessage($message, GameLogTypes::ERROR->value);
    }

    public function addInfoMessage(string $message): void
    {
        $this->logMessage($message, GameLogTypes::INFO->value);
    }

    public function addWarningMessage(string $message): void
    {
        $this->logMessage($message, GameLogTypes::WARNING->value);
    }

    public function addSuccessMessage(string $message): void
    {
        $this->logMessage($message, GameLogTypes::SUCCESS->value);
    }

    /**
     * @param  mixed  $message
     * @param  value-of<GameLogTypes>  $type
     */
    public function logMessage($message, string $type): void
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
