<?php
namespace Relevate\Api;

use Relevate\PagedList;

class Product extends AbstractApi
{
    /**
     * Finds all products.
     *
     * @param params array
     */
    public function all(array $params = array()) {
        $response = $this->get('products.json', $params, array(), false);
        return new PagedList($response->getContent()['elements'], $response->getPagination());
    }

    /**
     * Finds a single product by ID.
     *
     * @param id int Product ID
     * @param params array
     */
    public function find($id, array $params = array()) {
        return $this->get(sprintf('products/%s.json', $id), $params);
    }

    /**
     * Search for a single product by name.
     *
     * @param id string keyword
     * @param params array
     */
    public function search($keyword, array $params = array()) {
        $params['query'] = $keyword;
        return $this->all($params);
    }
}
