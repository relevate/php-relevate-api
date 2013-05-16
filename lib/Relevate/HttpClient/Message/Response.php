<?php

namespace Relevate\HttpClient\Message;

use Buzz\Message\Response as BaseResponse;

use Relevate\Exception\ApiLimitExceedException;

class Response extends BaseResponse
{
    /**
     * @var integer
     */
    public $remainingCalls;

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        $response = parent::getContent();
        $content  = json_decode($response, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $response;
        }

        return $content;
    }

    /**
     * @return array|null
     */
    public function getPagination()
    {
        $current_page = $this->getHeader('x-current-page');
        if (empty($current_page)) {
            return null;
        }

        return array(
            'total_elements' => $this->getHeader('x-total-elements'),
            'current_page' => $this->getHeader('x-current-page'),
            'total_pages' => $this->getHeader('x-total-pages'),
            'page_size' => $this->getHeader('x-page-size'),
        );
    }

    /**
     * Is not modified
     *
     * @return Boolean
     */
    public function isNotModified()
    {
        return 304 === $this->getStatusCode();
    }
}