<?php

namespace Relevate\HttpClient\Listener;

use Relevate\Client;
use \InvalidArgumentException;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

class AuthListener implements ListenerInterface
{
    /**
     * @var string
     */
    private $api_user;
    /**
     * @var string
     */
    private $api_key;

    /**
     * @param string $api_user
     * @param string $api_key
     */
    public function __construct($api_user, $api_key)
    {
        $this->api_user  = $api_user;
        $this->api_key = $api_key;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     */
    public function preSend(RequestInterface $request)
    {
        $request->addHeader('Authorization: Basic '. base64_encode($this->api_user .':'. $this->api_key));
    }

    /**
     * {@inheritDoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}