<?php
require 'vendor/autoload.php';

use Relevate\Client;
use Relevate\HttpClient\CachedHttpClient;
use Relevate\HttpClient\Cache\FilesystemCache;

$c = new Client('https://client.relevate.dk/api.php/', 'api_username', 'api_key');
// With cache:
// $c = new Relevate\Client('https://client.relevate.dk/api.php/', 'api_username', 'api_key', new CachedHttpClient(array(), null, new FilesystemCache('/tmp/relevate-api-cache')));

// Products
$product_api = $c->api('products');

// Retrieve page one of the product list
$products = $product_api->all();
echo sprintf("Showing page %s of %s (%s total products):\n", $products->getCurrentPage(), $products->getTotalPages(), $products->getTotalElements());
foreach($products as $product) {
    echo sprintf("\t(%s) %s\n", $product['id'], $product['name']);
}

// Retrieve a single product (the first in the previous list)
$product_1 = $product_api->find($products[0]['id']);
print_r($product_1);

// Search for a product by keyword "olive oil"
$product_search = $product_api->search('olive oil');
print_r($product_search);

// List the exports available in the system
$exports = $c->api('exports')->all();
foreach($exports as $export) {
    print_r($export);
}

// Do a single export of product 789 and 890.
$export = $c->api('exports')->export(6, array(789, 890));

// Pass the headers and content on to the client.
header(sprintf('Content-type: %s', $export->getHeader('Content-type')));
echo $export->getContent();
