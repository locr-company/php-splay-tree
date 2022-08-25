<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\SplayTree;

final class SplayTreeEmptyTest extends TestCase
{
    /**
     * should return whether the tree is empty
     */
    public function testEmpty1(): void
    {
        $tree = new SplayTree();

        $this->assertTrue($tree->isEmpty());
        $tree->insert(1);
        $this->assertFalse($tree->isEmpty());
        $tree->remove(1);
        $this->assertTrue($tree->isEmpty());
    }
}