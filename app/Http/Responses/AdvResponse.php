<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdvResponse implements Responsable
{
    /**
     * @see https://wendelladriel.com/blog/standard-api-responses-with-laravel-responsables All credit
     */
    public function __construct(
        private array $data = [],
        private int $code = Response::HTTP_OK,
        private array $headers = []
    ) {
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
        $this->data = $data;

        return $this;
    }

    public function addMessage(string $message): self
    {
        $this->data['gameMessage'][] = $message;

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

    public function setStatus(int $status): self
    {
        $this->code = $status;

        return $this;
    }

    /**
     * @param  \Illuminate\Http\Request  $request,
     *  NOTE: we cannot type the argument as Request
     *  becasue it conflicts with the interface
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
