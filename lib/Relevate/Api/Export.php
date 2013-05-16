<?php
namespace Relevate\Api;

use Relevate\PagedList;

class Export extends AbstractApi
{
    /**
     * Finds all exports.
     *
     * @param params array
     */
    public function all(array $params = array()) {
        return $this->get('exports.json', $params);
    }

    /**
     * Exports a list of products through a given export id
     *
     * @param id int Export ID
     * @param products array List of product IDS
     * @param params array
     */
    public function export($id, array $products, array $params = array()) {
        return $this->get(sprintf(
            'exports/%s?%s',
            $id,
            implode('&', array_map(function($e) { return sprintf("product[]=%s", $e); }, $products))
        ), $params, array(), false);
    }
}