<?php

declare(strict_types=1);

namespace SplayTree;

class Node
{
    /**
     * @var int|float|array<mixed>|null
     */
    public int|float|array|null $key = null;
    public mixed $data = null;
    public ?self $left = null;
    public ?self $right = null;
    public ?self $next = null;

    /**
     * @param int|array<mixed>|null $key
     */
    public function __construct(int|float|array|null $key, mixed $data = null)
    {
        $this->key = $key;
        $this->data = $data;
    }
}
