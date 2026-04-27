<?php

namespace App\Exceptions;

use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response
    {
        if ($request->is('api/*')) {
            return $this->handleApiExceptions($e);
        }

        return parent::render($request, $e);
    }

    private function handleApiExceptions(Throwable $e): Response
    {
        return match (true) {
            $e instanceof AuthenticationException => ApiResponse::error(
                ApiMessages::AUTH_UNAUTHORIZED,
                Response::HTTP_UNAUTHORIZED
            ),

            $e instanceof ValidationException => ApiResponse::error(
                $e->validator->errors()->first(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            ),

            $e instanceof NotFoundHttpException, $e instanceof RouteNotFoundException => ApiResponse::error(
                ApiMessages::RESOURCE_NOT_FOUND,
                Response::HTTP_NOT_FOUND
            ),

            $e instanceof HttpException => ApiResponse::error(
                $e->getMessage(),
                $e->getStatusCode()
            ),

            default => ApiResponse::error(
                ApiMessages::AN_ERROR_OCCURRED,
                Response::HTTP_INTERNAL_SERVER_ERROR
            ),
        };
    }
}
