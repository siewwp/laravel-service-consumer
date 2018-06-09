<?php


namespace Siewwp\LaravelServiceConsumer\Contracts;


use Closure;

interface Authenticate
{
    public function handle($request, Closure $next);
    
    public function authenticate();
    
    public function signResponse($response);
}