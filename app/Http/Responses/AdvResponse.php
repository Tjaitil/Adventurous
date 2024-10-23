<?php

namespace App\Http\Responses;

use App\Enums\GameEvents;
use App\Enums\GameLogTypes;
use App\Traits\GameLogger;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdvResponse implements Responsable
{
    use GameLogger{
        GameLogger::addErrorMessage as BaseAddErrorMessage;
        GameLogger::addInfoMessage as BaseAddInfoMessage;
        GameLogger::addWarningMessage as BaseAddWarningMessage;
        GameLogger::addSuccessMessage as BaseAddSuccessMessage;
    }

    /**
     * @var array<string|int, mixed>
     */
    private array $data = [];

    /**
     * @param  array<string, mixed>  $data  Data key in the response
     *
     * @see https://wendelladriel.com/blog/standard-api-responses-with-laravel-responsables All credit
     */
    public function __construct(
        array $data = [],
        private int $code = Response::HTTP_OK,
        private array $headers = []
    ) {
        $this->data['data'] = $data;
    }

    public function addTemplate(string $index, string $template): self
    {
        $this->data['html'][$index] = $template;

        return $this;
    }

    public function addData(string $index, $data): self
    {
        $this->data['data'][$index] = $data;

        return $this;
    }

    public function setData(array $data): self
    {
        $this->data['data'] = array_merge($this->data['data'], $data);

        return $this;
    }

    public function addMessage(string $message): self
    {
        $this->data['gameMessage'][] = ['text' => $message, 'type' => GameLogTypes::INFO->value];

        return $this;
    }

    public function addInfoMessage(string $message): self
    {
        $this->BaseAddInfoMessage($message);
        $this->data['gameMessage'][] = ['text' => $message, 'type' => GameLogTypes::INFO->value];

        return $this;
    }

    public function addErrorMessage(string $message): self
    {
        $this->BaseAddErrorMessage($message);
        $this->data['gameMessage'][] = ['text' => $message, 'type' => GameLogTypes::ERROR->value];

        return $this;
    }

    public function addWarningMessage(string $message): self
    {
        $this->BaseAddWarningMessage($message);
        $this->data['gameMessage'][] = ['text' => $message, 'type' => GameLogTypes::WARNING->value];

        return $this;
    }

    public function addSuccessMessage(string $message): self
    {
        $this->BaseAddSuccessMessage($message);
        $this->data['gameMessage'][] = ['text' => $message, 'type' => GameLogTypes::SUCCESS->value];

        return $this;
    }

    public function addLevelUP(string $skillName, int $new_level): self
    {
        $this->data['levelUP'][] = [
            'skill' => $skillName,
            'new_level' => $new_level,
        ];

        return $this;
    }

    /**
     * @param  value-of<\App\Enums\GameEvents>|\App\Enums\GameEvents  $event
     */
    public function addEvent(string|GameEvents $event): self
    {
        $this->data['events'][] = $event;

        return $this;
    }

    /**
     * @param array<int, array{
     *  'skill': value-of<\App\Enums\SkillNames>,
     *  'new_level': int
     * }> $levelUPs
     */
    public function addLevelUPs(array $levelUPs): self
    {
        $this->data['levelUP'] = $levelUPs;

        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->code = $status;

        return $this;
    }

    /**
     * NOTE: we cannot type the argument as Request
     * becasue it conflicts with the interface
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json(
            $this->data,
            $this->code,
            $this->headers
        );
    }
}
