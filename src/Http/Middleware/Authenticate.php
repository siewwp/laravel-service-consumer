<?php


namespace Siewwp\LaravelServiceConsumer\Http\Middleware;

use Siewwp\LaravelServiceConsumer\Contracts\Authenticate as AuthenticateContract;
use Acquia\Hmac\Exception\InvalidSignatureException;
use Acquia\Hmac\KeyInterface;
use Acquia\Hmac\KeyLoader;
use Acquia\Hmac\RequestAuthenticator;
use Acquia\Hmac\ResponseSigner;
use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class Authenticate implements AuthenticateContract
{
    private $serverRequest;
    protected $key;

    public function __construct(ServerRequestInterface $serverRequest, KeyInterface $key)
    {
        $this->serverRequest = $serverRequest;
        $this->key = $key;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authenticate();
        
        $response = $next($request);

        return $this->signResponse($response);
    }

    public function authenticate()
    {
        $keyLoader = new KeyLoader([$this->key->getId() => $this->key->getSecret()]);

        $authenticator = new RequestAuthenticator($keyLoader);

        try{
            $authenticator->authenticate($this->serverRequest);
        } catch (InvalidSignatureException $e) {
            abort(401, 'Unauthorized');
        }
    }

    public function signResponse($response)
    {
        $response = (new DiactorosFactory)->createResponse($response);

        $signer = new ResponseSigner($this->key, $this->serverRequest);

        return $signer->signResponse($response);
    }
}