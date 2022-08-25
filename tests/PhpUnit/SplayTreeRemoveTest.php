<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\SplayTree;

final class SplayTreeRemoveTest extends TestCase
{
    /**
     * should not change the size of empty tree
     */
    public function testRemove1(): void
    {
        $tree = new SplayTree();
        $tree->remove(1);
        $this->assertEquals(0, $tree->size);
    }

    /**
     * should remove a single key
     */
    public function testRemove2(): void
    {
        $tree = new SplayTree();
        $tree->insert(1);
        $tree->remove(1);
        $this->assertTrue($tree->isEmpty());
    }

    /**
     * should ignore a single key which is not there
     */
    public function testRemove3(): void
    {
        $tree = new SplayTree();
        $tree->insert(1);
        $tree->remove(2);
        $this->assertEquals(1, $tree->size);
    }

    /**
     * should take the right child if the left does not exist
     */
    public function testRemove4(): void
    {
        $tree = new SplayTree();
        $tree->insert(1);
        $tree->insert(2);
        $tree->remove(1);
        $this->assertEquals(2, $tree->root->key);
    }

    /**
     * should take the left child if the right does not exist
     */
    public function testRemove5(): void
    {
        $tree = new SplayTree();
        $tree->insert(2);
        $tree->insert(1);
        $tree->remove(2);
        $this->assertEquals(1, $tree->root->key);
    }

    /**
     * should not break the existing pointers to nodes
     */
    public function testRemove6(): void
    {
        $tree = new SplayTree();

        $n1 = $tree->insert(1);
        $n2 = $tree->insert(2);
        $n3 = $tree->insert(3);

        $tree->remove(2);

        $this->assertEquals(2, $n2->key);
        $this->assertEquals(3, $n3->key);
    }

    /**
     * pop()
     */
    public function testRemove7(): void
    {
        $tree = new SplayTree();
        $tree->insert(2);
        $tree->insert(1);
        $tree->remove(2);

        $removed = $tree->pop();
        $this->assertEquals(['key' => 1, 'data' => null], $removed);
        $this->assertNull($tree->pop());
    }

    /**
     * should support clear operation
     */
    public function testRemove8(): void
    {
        $tree = new SplayTree();
        $tree->insert(2);
        $tree->insert(1);
        $tree->remove(2);

        $tree->clear();

        $this->assertNull($tree->root);
        $this->assertEquals(0, $tree->size);
    }
}