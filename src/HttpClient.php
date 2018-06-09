<?php


namespace Siewwp\LaravelServiceConsumer;

use Siewwp\LaravelServiceConsumer\Contracts\HttpClient as HttpClientContract;
use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\KeyInterface;
use GuzzleHttp\HandlerStack;

class HttpClient extends \GuzzleHttp\Client implements HttpClientContract
{
    public $retry;
    protected $key;
    protected $baseUri;

    /**
     * Registration constructor.
     * @param array $config
     * @param KeyInterface $key
     * @param string $baseUri
     * @param int $retry
     */
    public function __construct(array $config, KeyInterface $key, $baseUri = '', $retry = 1)
    {
        $this->retry = $retry;
        $this->key = $key;
        $this->baseUri = $baseUri;
        
        parent::__construct(array_merge($this->defaultConfigs(), $config));
    }

    protected function getHmacHandler()
    {
        $middleware = new HmacAuthMiddleware($this->key);
        $stack = HandlerStack::create();
        $stack->push($middleware);
        return $stack;
    }

    /**
     * @param $method
     * @param string $path
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function request($method, $path = '', array $options = [])
    {
        return retry($this->retry, function () use ($method, $path, $options) {
            $response = parent::request($method, $path, $options);

            return json_decode((string)$response->getBody(), true);
        }, 1000);
    }

    protected function defaultConfigs()
    {
        return [
            'handler' => $this->getHmacHandler(),
            'base_uri' => $this->baseUri,
            'headers' => [
                'Accept' => 'application/json',
                'Service-Consumer-Id' => $this->key->getId()
            ]
        ];
    }
}