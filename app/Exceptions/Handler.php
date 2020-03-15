<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\OAuthServerException as PassportOAuthServerException;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueOAuthServerException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $e)
    {
        if (!(
            ($e instanceof PassportOAuthServerException && $e->getCode() == 10) ||
            ($e instanceof PassportOAuthServerException && $e->getCode() == 4) ||
            ($e instanceof LeagueOAuthServerException && $e->getCode() == 9)
        ))
            parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
