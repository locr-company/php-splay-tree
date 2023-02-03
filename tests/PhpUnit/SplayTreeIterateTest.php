<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeIterateTest extends TestCase
{
    /**
     * should iterate the tree in order
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testIterate1(): void
    {
        $tree = new SplayTree();
        $tree->insert(3);
        $tree->insert(1);
        $tree->insert(0);
        $tree->insert(2);

        $i = 0;
        foreach ($tree as $k => $n) {
            $this->assertEquals($i++, $n->key);
        }
        $this->assertEquals(4, $i);
    }

    /**
     * should should support empty tree
     */
    public function testIterate2(): void
    {
        $tree = new SplayTree();

        $i = 0;
        foreach ($tree as $n) {
            $this->assertEquals($i++, $n->key);
        }
        $this->assertEquals(0, $i);
    }
}
