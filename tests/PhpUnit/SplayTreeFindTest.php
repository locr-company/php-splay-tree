<?php

declare(strict_types=1);

namespace UnitTests;

use Locr\Lib\SplayTree\SplayTree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Locr\Lib\SplayTree\SplayTree
 * @coversDefaultClass Locr\Lib\SplayTree\SplayTree
 */
final class SplayTreeFindTest extends TestCase
{
    /**
     * should return key as the result of search
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testFind1(): void
    {
        $tree = new SplayTree();
        $this->assertNull($tree->find(1));
        $this->assertNull($tree->find(2));
        $this->assertNull($tree->find(3));
        $tree->insert(1, 4);
        $tree->insert(2, 5);
        $tree->insert(3, 6);

        $root = $tree->root;
        $this->assertEquals(4, $tree->find(1)->data);
        $this->assertNotEquals($tree->root, $root);
        $root = $tree->root;

        $this->assertEquals(5, $tree->find(2)->data);
        $this->assertNotEquals($tree->root, $root);
        $root = $tree->root;

        $this->assertEquals(6, $tree->find(3)->data);
        $this->assertNotEquals($tree->root, $root);
        $root = $tree->root;

        $this->assertNull($tree->find(8));
        $this->assertEquals($tree->root, $root);
    }

    /**
     * should allow finding node without splaying
     *
     * @covers Locr\Lib\SplayTree\Node::__construct
     */
    public function testFind2(): void
    {
        $tree = new SplayTree();
        $this->assertNull($tree->findStatic(1));
        $this->assertNull($tree->findStatic(2));
        $this->assertNull($tree->findStatic(3));
        $tree->insert(-2, 8);
        $tree->insert(1, 4);
        $tree->insert(2, 5);
        $tree->insert(3, 6);

        $tree->find(2);
        $root = $tree->root;
        $this->assertEquals(4, $tree->findStatic(1)->data);
        $this->assertEquals($tree->root, $root);

        $this->assertEquals(5, $tree->findStatic(2)->data);
        $this->assertEquals($tree->root, $root);

        $this->assertEquals(6, $tree->findStatic(3)->data);
        $this->assertEquals($tree->root, $root);

        $this->assertEquals(8, $tree->findStatic(-2)->data);

        $this->assertEquals($tree->root, $tree->find(2));
    }
}
