<?php

namespace Relevate\HttpClient;

use Buzz\Client\ClientInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Listener\ListenerInterface;

use Relevate\Exception\ErrorException;
use Relevate\Exception\RuntimeException;
use Relevate\HttpClient\Listener\ErrorListener;
use Relevate\HttpClient\Message\Request;
use Relevate\HttpClient\Message\Response;
use Buzz\Client\Curl;

/**
 * Performs requests on Relevate API. API documentation should be self-explanatory.
 */
class HttpClient implements HttpClientInterface
{
    /**
     * @var array
     */
    protected $options = array(
        'base_url'    => 'http://55.55.55.10/api_dev.php/',

        'user_agent'  => 'relevate-api php client (http://relevate.dk)',
        'timeout'     => 10,

        'cache_dir'   => null
    );
    /**
     * @var array
     */
    protected $listeners = array();
    /**
     * @var array
     */
    protected $headers = array();

    private $lastResponse;
    private $lastRequest;

    /**
     * @param array           $options
     * @param ClientInterface $client
     */
    public function __construct(array $options = array(), ClientInterface $client = null)
    {
        $client = $client ?: new Curl();
        $client->setTimeout($this->options['timeout']);
        $client->setVerifyPeer(false);

        $this->options = array_merge($this->options, $options);
        $this->client  = $client;

        $this->addListener(new ErrorListener($this->options));

        $this->clearHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->headers = array('Accept: application/json');
    }

    /**
     * @param ListenerInterface $listener
     */
    public function addListener(ListenerInterface $listener)
    {
        $this->listeners[get_class($listener)] = $listener;
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        if (0 < count($parameters)) {
            $path .= (false === strpos($path, '?') ? '?' : '&').http_build_query($parameters, '', '&');
        }

        return $this->request($path, array(), 'GET', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'POST', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'PATCH', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'PUT', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array())
    {
        $path = trim($this->options['base_url'].$path, '/');

        $request = $this->createRequest($httpMethod, $path);
        $request->addHeaders($headers);
        $request->setContent(json_encode($parameters));

        $hasListeners = 0 < count($this->listeners);
        if ($hasListeners) {
            foreach ($this->listeners as $listener) {
                $listener->preSend($request);
            }
        }

        $response = $this->createResponse();

        try {
            $this->client->send($request, $response);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage());
        } catch (\RuntimeException $e) {
            throw new RuntimeException(sprintf('URL: %s, Message: ', $path, $e->getMessage()));
        }

        $this->lastRequest  = $request;
        $this->lastResponse = $response;

        if ($hasListeners) {
            foreach ($this->listeners as $listener) {
                $listener->postSend($request, $response);
            }
        }

        return $response;
    }

    /**
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @param string $httpMethod
     * @param string $url
     *
     * @return Request
     */
    protected function createRequest($httpMethod, $url)
    {
        $request = new Request($httpMethod);
        $request->setHeaders($this->headers);
        $request->fromUrl($url);

        return $request;
    }

    /**
     * @return Response
     */
    protected function createResponse()
    {
        return new Response();
    }
}