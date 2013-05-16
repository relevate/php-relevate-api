<?php
namespace Relevate;

use IteratorAggregate;
use ArrayAccess;
use ArrayIterator;
use Countable;

class PagedList implements ArrayAccess, IteratorAggregate, Countable
{
    private $container;
    private $pagination;

    public function __construct($container, $pagination) {
        $this->container = $container;
        $this->pagination = $pagination;
    }

    public function getCurrentPage() { return $this->pagination['current_page']; }
    public function getTotalPages() { return $this->pagination['total_pages']; }
    public function getPageSize() { return $this->pagination['page_size']; }
    public function getTotalElements() { return $this->pagination['total_elements']; }

    /* ArrayAccess functions */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /* IteratorAggregate functions */
    public function getIterator() {
        return new ArrayIterator($this->container);
    }

    public function count() {
        return count($this->container);
    }
}