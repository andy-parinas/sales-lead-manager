<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // return parent::render($request, $exception);
        if($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof ModelNotFoundException){

            $modelName = class_basename($exception->getModel());

            return $this->errorResponse("$modelName does not exist with specified indicator", 404);
        }

        if ($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException){
            return $this->errorResponse("Unauthorized", 403);
        }

        if ($exception instanceof NotFoundHttpException){
            return $this->errorResponse("Specified URL cannot be found", 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse("Specified method for the request is not allowed", 405);
        }

        if ($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if ($exception instanceof QueryException){

            $errorCode = $exception->getCode();

            if($errorCode == '23000'){

                return $this->errorResponse("Cannot remove resource permanently. It is related with another resource", 409);

            }else {

                if(config('app.debug')){

                    return $this->errorResponse($exception->getMessage(), 500);
                }

                return $this->errorResponse("Error in database operations. Please try again", 500);
            }

        }

        if(config('app.debug')){
            
            return $this->errorResponse($exception->getMessage(), 500);
        }

        return $this->errorResponse('Unexpected Error. Please Try again', 500);
        
    }


    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse("Unauthenticated", 401);
    }
}
