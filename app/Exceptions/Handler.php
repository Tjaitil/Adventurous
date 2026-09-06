<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (InventoryFullException $e, Request $request) {
            $e->render($request);
        });

        $this->renderable(function (JsonException $e, Request $request) {
            $e->render($request);
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);
        $status = $response->getStatusCode();

        if (in_array($status, [403, 404, 500, 503], true)) {
            return inertia('ErrorPage', ['status' => $status])
                ->toResponse($request)
                ->setStatusCode($status);
        }

        if ($status === 419) {
            return back()->with('message', 'The page expired, please try again.');
        }

        return $response;
    }
}
