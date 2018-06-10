<?php


namespace Siewwp\LaravelServiceConsumer;

use Siewwp\LaravelServiceConsumer\Http\Middleware\Authenticate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

trait HandleWebhook
{
    public function __construct()
    {
        $this->middleware(Authenticate::class);
    }

    /**
     * Handle a webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $method = 'handle'.studly_case(str_replace('.', '_', $payload['type']));

        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        } else {
            return $this->missingMethod();
        }
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function missingMethod($parameters = [])
    {
        return new Response;
    }

}