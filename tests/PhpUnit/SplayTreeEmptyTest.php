<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeEmptyTest extends TestCase
{
    /**
     * should return whether the tree is empty
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
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
