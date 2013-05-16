<?php
namespace Relevate;

use Relevate\HttpClient\HttpClient;
use Relevate\HttpClient\HttpClientInterface;
use Relevate\HttpClient\Listener\AuthListener;

class Client
{
    /**
     * The Buzz instance used to communicate with Relevate
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Options
     */
    private $options = array();

    /**
     * Instantiate a new Relevate client
     *
     * @param null|HttpClientInterface $httpClient Relevate http client
     */
    public function __construct($api_base_url, $api_user, $api_key, HttpClientInterface $httpClient = null)
    {
        $this->options['base_url'] = $api_base_url;
        $this->httpClient = $httpClient ?: new HttpClient($this->options);
        $this->httpClient->addListener(
            new AuthListener($api_user, $api_key)
        );
    }

    /**
     * @param string $name
     *
     * @return ApiInterface
     *
     * @throws InvalidArgumentException
     */
    public function api($name) {
        switch ($name) {
            case 'products':
            case 'product':
                $api = new Api\Product($this);
                break;

            case 'exports':
            case 'export':
                $api = new Api\Export($this);
                break;

            default:
                throw new \InvalidArgumentException();
                break;
        }

        return $api;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->httpClient->clearHeaders();
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->httpClient->setHeaders($headers);
    }
}
