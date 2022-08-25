<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\SplayTree;

final class SplayTreeDuplicateTest extends TestCase
{
    /**
     * should allow inserting of duplicate key
     */
    public function testDuplicate1(): void
    {
        $tree = new SplayTree();
        $values = [2, 12, 1, -6, 1];

        foreach ($values as $v) {
            $tree->insert($v);
        }

        $this->assertEquals([-6, 1, 1, 2, 12], $tree->keys());
        $this->assertEquals(5, $tree->size);
    }

    /**
     * should allow multiple duplicate keys in a row
     */
    public function testDuplicate2(): void
    {
        $tree = new SplayTree();
        $values = [2, 12, 1, 1, -6, 2, 1, 1, 13];

        foreach ($values as $v) {
            $tree->insert($v);
        }

        $this->assertEquals([ -6, 1, 1, 1, 1, 2, 2, 12, 13 ], $tree->keys());
        $this->assertEquals(9, $tree->size);
    }

    /**
     * should remove from a tree with duplicate keys correctly
     */
    public function testDuplicate3(): void
    {
        $tree = new SplayTree();
        $values = [2, 12, 1, 1, -6, 1, 1];

        foreach ($values as $v) {
            $tree->insert($v);
        }

        $size = $tree->size;
        for ($i = 0; $i < 4; $i++) {
            $tree->remove(1);

            if ($i < 3) {
                $this->assertTrue($tree->contains(1));
            }
            $this->assertEquals(--$size, $tree->size);
        }

        $this->assertFalse($tree->contains(1));
    }

    /**
     * should remove from a tree with multiple duplicate keys correctly
     */
    public function testDuplicate4(): void
    {
        $tree = new SplayTree();
        $values = [2, 12, 1, 1, -6, 1, 1, 2, 0, 2];

        foreach ($values as $v) {
            $tree->insert($v);
        }

        $size = $tree->size;
        while (!$tree->isEmpty()) {
            $tree->pop();
            $this->assertEquals(--$size, $tree->size);
        }
    }

    /**
     * should disallow duplicates if noDuplicates is set
     */
    public function testDuplicate5(): void
    {
        $tree = new SplayTree();
        $values = [2, 12, 1, -6, 1];

        foreach ($values as $v) {
            $tree->add($v);
        }

        $this->assertEquals([-6, 1, 2, 12], $tree->keys());
        $this->assertEquals(4, $tree->size);
    }

    /**
     * should add only if the key is not there
     */
    public function testDuplicate6(): void
    {
        $tree = new SplayTree();
        $tree->insert(1);
        $tree->insert(2);
        $tree->insert(3);

        $s = $tree->size;
        $tree->add(1);
        $this->assertEquals($s, $tree->size);
    }
}