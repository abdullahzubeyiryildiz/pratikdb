<?php

class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public static function make($items)
    {
        return new static($items);
    }

    public function pluck($key)
    {
        $values = [];
        foreach ($this->items as $item) {
            if (array_key_exists($key, $item)) {
                $values[] = $item[$key];
            }
        }
        return new static($values);
    }

    public function toArray()
    {
        return $this->items;
    }

    
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    
    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
    
    public function count(): int
    {
        return count($this->items);
    }
    
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}