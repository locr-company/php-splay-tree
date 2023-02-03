<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeContainsTest extends TestCase
{
    /**
     * should return false if the tree is empty
     */
    public function testContains1(): void
    {
        $tree = new SplayTree();
        $this->assertFalse($tree->contains(1));
    }

    /**
     * should return whether the tree contains a node
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testContains2(): void
    {
        $tree = new SplayTree();
        $this->assertFalse($tree->contains(1));
        $this->assertFalse($tree->contains(2));
        $this->assertFalse($tree->contains(3));
        $tree->insert(3);
        $tree->insert(1);
        $tree->insert(2);
        $this->assertTrue($tree->contains(1));
        $this->assertTrue($tree->contains(2));
        $this->assertTrue($tree->contains(3));
    }

    /**
     * should return false when the expected parent has no children
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testContains3(): void
    {
        $tree = new SplayTree();
        $tree->insert(2);
        $this->assertFalse($tree->contains(1));
        $this->assertFalse($tree->contains(3));
    }
}
