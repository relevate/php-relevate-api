<?php

namespace Relevate\HttpClient\Cache;

use Relevate\HttpClient\Message\Response;

/**
 * Caches Relevate api responses
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
interface CacheInterface
{
    /**
     * @param  string $id   The id of the cached resource
     *
     * @return null|integer The modified since timestamp
     */
    public function getModifiedSince($id);

    /**
     * @param  string $id The id of the cached resource
     *
     * @return Response   The cached response object
     *
     * @throws \InvalidArgumentException If cache data don't exists
     */
    public function get($id);

    /**
     * @param string   $id       The id of the cached resource
     * @param Response $response The response to cache
     *
     * @throws \InvalidArgumentException If cache data cannot be saved
     */
    public function set($id, Response $response);
}
