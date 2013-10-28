# Relevate API Client

A simple object oriented wrapper of the [Relevate API](http://relevate.dk).

The structure of the library is heavily inspired by [KnpLabs' Github API wrapper](https://github.com/KnpLabs/php-github-api).

## Requirements

* PHP >= 5.3.2 with [cURL](http://php.net/manual/en/book.curl.php) extension,
* [Buzz](https://github.com/kriswallsmith/Buzz) library,

## Getting Started

### Creating a client

A client can be created by including the Client class and creating a new instance without the API username and key as parameters. These can be created through the web interface.

```php
$client = new Relevate\Client('https://demo.relevate.dk/api.php/', 'api_user', 'api_key');
```

Also, it will be beneficial to use the Buzz CachedHttpClient class in order to cache the responses according to the cache headers returned by the Relevate API. This can be done by passing a CachedHttpClient object to the client.

```php
use Relevate\HttpClient\CachedHttpClient;
use Relevate\HttpClient\Cache\FilesystemCache;
$c = new Relevate\Client('https://account.relevate.dk/api.php/', 'api_user', 'api_key', new CachedHttpClient(array(), null, new FilesystemCache('/tmp/relevate-api-cache')));
```

## Products API

### List products

*Note:* The product list will return a `Relevate\PagedList` object to ease pagination. Please use this object to retrieve pagination information.

Retrieve the first page:

```php
$products = $client->api('products')->all();

echo sprintf("Showing page %s of %s (%s total products):\n", $products->getCurrentPage(), $products->getTotalPages(), $products->getTotalElements());
foreach($products as $product) {
    echo sprintf("\t(%s) %s\n", $product['id'], $product['name']);
}
```

To go to other pages or change the page size, simply change the parameters to the `all()` method:

```php
$products = $client->api('products')->all(array('page' => 3, 'page_size' => 20));
```

### Finding products

If you have the ID of a product, use the `find` method to retrieve all information about the product:

```php
$products = $client->api('products')->find(1);
```

If, however, you don't, use the the `search` method to do a keyword search to find a specific product. Please note that the keyword must be at least three characters long:

```php
$products = $client->api('products')->search('olive oil');
```

## Exports API

### List exports

```php
$exports = $client->api('exports')->all();
```

### Do a single export without queueing

*Note:* `Relevate\Api\Export::export(...)` returns a [Buzz](https://github.com/kriswallsmith/Buzz) [Response](https://github.com/kriswallsmith/Buzz/blob/master/lib/Buzz/Message/Response.php), so that you can retrieve the proper Content-type header from the response and pass that on to the client.

```php
// Do a single export
$export = $client->api('exports')->export(6, array(789, 890));

// Pass the headers and content on to the client.
header(sprintf('Content-type: %s', $export->getHeader('Content-type')));
echo $export->getContent();
```

### Do a single export with queue

```php
// Create the exports API object
$export_api = $client->api('exports');

// Queue the export
$export_id = 1;
$export = $export_api->export($export_id, [1, 2, 3, 4], true);

// Parse the export queue id
preg_match('/\/([0-9]+)\.json/', $export->getHeader('Location'), $res);
$queue_id = $res[1];

// Wait until the export is done
echo "Export queued...\n";
$done = false;
while(!$done) {
    sleep(3);
    echo "Running...\n";

    // Retrieve the queue status
    $export_queue = $export_api->queue_status($export_id, $queue_id);

    // Check if it is done
    $done = $export_queue['completion_percent'] == 100;
}

// Display the download URL
$url = $export_queue['file_location'];
echo sprintf("Export can be found at %s\n", $url);
```
