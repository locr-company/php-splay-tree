<?php

declare(strict_types=1);

namespace SplayTree;

class Node
{
    public ?int $key = null;
    public mixed $data = null;
    public ?self $left = null;
    public ?self $right = null;
    public ?self $next = null;

    public function __construct(?int $key, mixed $data = null)
    {
        $this->key = $key;
        $this->data = $data;
    }
}
