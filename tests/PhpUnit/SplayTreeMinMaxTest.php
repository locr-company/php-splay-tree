<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeMinMaxTest extends TestCase
{
    /**
     * should return the maximum key in the tree
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testMinMax1(): void
    {
        $tree = new SplayTree();
        $tree->insert(3);
        $tree->insert(5);
        $tree->insert(1);
        $tree->insert(4);
        $tree->insert(2);
        $this->assertEquals(5, $tree->max());
    }

    /**
     * should return null for max if the tree is empty
     */
    public function testMinMax2(): void
    {
        $tree = new SplayTree();
        $this->assertNull($tree->max());
    }

    /**
     * should return the minimum key in the tree
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testMinMax3(): void
    {
        $tree = new SplayTree();
        $tree->insert(5);
        $tree->insert(3);
        $tree->insert(1);
        $tree->insert(4);
        $tree->insert(2);
        $this->assertEquals(1, $tree->min());
    }

    /**
     * should return the max node
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testMinMax4(): void
    {
        $tree = new SplayTree();
        $tree->insert(3);
        $tree->insert(5, 10);
        $tree->insert(1);
        $tree->insert(4);
        $tree->insert(2);
        $node = $tree->maxNode();
        $this->assertEquals(5, $node->key);
        $this->assertEquals(10, $node->data);
    }

    /**
     * should return null for maxNode if the tree is empty
     */
    public function testMinMax5(): void
    {
        $tree = new SplayTree();
        $this->assertNull($tree->maxNode());
    }

    /**
     * should return the min node
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testMinMax6(): void
    {
        $tree = new SplayTree();
        $tree->insert(5);
        $tree->insert(3);
        $tree->insert(1, 20);
        $tree->insert(4);
        $tree->insert(2);
        $node = $tree->minNode();
        $this->assertEquals(1, $node->key);
        $this->assertEquals(20, $node->data);
    }

    /**
     * should return null for min if the tree is empty
     */
    public function testMinMax7(): void
    {
        $tree = new SplayTree();
        $this->assertNull($tree->min());
    }

    /**
     * should support removing min node
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testMinMax8(): void
    {
        $tree = new SplayTree();
        $tree->insert(5);
        $tree->insert(3);
        $tree->insert(1);
        $tree->insert(4);
        $tree->insert(2);
        $this->assertEquals(1, $tree->pop()['key']);
    }

    /**
     * should return null for minNode if the tree is empty
     */
    public function testMinMax9(): void
    {
        $tree = new SplayTree();
        $this->assertNull($tree->minNode());
    }
}
