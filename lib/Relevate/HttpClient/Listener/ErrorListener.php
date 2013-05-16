<?php

namespace Relevate\HttpClient\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Relevate\Exception\ApiLimitExceedException;
use Relevate\Exception\ErrorException;
use Relevate\Exception\RuntimeException;
use Relevate\Exception\ValidationFailedException;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ErrorListener implements ListenerInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function preSend(RequestInterface $request)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        /** @var $response \Relevate\HttpClient\Message\Response */
        if ($response->isClientError() || $response->isServerError()) {
            $content = $response->getContent();
            if (is_array($content) && isset($content['message'])) {
                if (400 == $response->getStatusCode()) {
                    throw new ErrorException($content['message'], 400);
                } else {
                    throw new ErrorException($content['message']);
                }
            }

            throw new ErrorException(sprintf("HTTP %s from %s", $response->getStatusCode(), $request->getUrl()));
        }
    }
}