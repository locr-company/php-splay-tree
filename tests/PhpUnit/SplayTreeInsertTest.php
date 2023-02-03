<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeInsertTest extends TestCase
{
    /**
     * should return the size of the tree
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testInsert1(): void
    {
        $tree = new SplayTree();
        $tree->insert(1);
        $tree->insert(2);
        $tree->insert(3);
        $tree->insert(4);
        $tree->insert(5);
        $this->assertEquals(5, $tree->size);
    }

    /**
     * should return the pointer
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testInsert2(): void
    {
        $tree = new SplayTree();
        $n1 = $tree->insert(1);
        $n2 = $tree->insert(2);
        $n3 = $tree->insert(3);

        $this->assertEquals(1, $n1->key);
        $this->assertEquals(2, $n2->key);
        $this->assertEquals(3, $n3->key);
    }
}
