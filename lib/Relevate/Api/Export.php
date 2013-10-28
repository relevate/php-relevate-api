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
     * Exports a list of products through an export with a given export id
     *
     * @param id int Export ID
     * @param products array List of product IDS
     * @param queue bool Whether the export should be queued or not.
     * @param params array of parameters (queue="true" will queue the export)
     */
    public function export($id, array $products, $queue = false, array $params = array()) {
        if($queue === true) {
            $params['queue'] = 'true';
        }
        $params['product'] = $products;
        return $this->get(sprintf(
            'exports/%s.json',
            $id
        ), $params, array(), false);
    }


    /**
     * Retrieve the status of a given element in the export queue by export and queue id.
     * @param export_id ID of the export
     * @param queue_id ID of the queue element
     */
    public function queue_status($export_id, $queue_id) {
        $response = $this->get(sprintf(
            'exports/%s/queue/%s.json',
            $export_id,
            $queue_id
        ), array(), array(), false);

        return $response->getContent();
    }
}
