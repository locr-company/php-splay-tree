<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\SplayTree;

final class SplayTreeBulkTest extends TestCase
{
    /**
     * should allow bulk-insert
     */
    public function testBulk1(): void
    {
        $tree = new SplayTree();
        $keys = [1, 2, 3, 4];
        $values = [4, 3, 2, 1];
        $tree->load($keys, $values);

        $this->assertEquals($keys, $tree->keys());
        $this->assertEquals($values, $tree->values());
    }

    /**
     * should allow bulk-insert without values
     */
    public function testBulk2(): void
    {
        $tree = new SplayTree();
        $keys = [1, 2, 3, 4];
        $tree->load($keys);

        $this->assertEquals($keys, $tree->keys());
        $this->assertEquals(array_map(function ($_) { return null; }, $keys), $tree->values());
    }

    /**
     * should be able to load into a tree with contents
     */
    public function testBulk3(): void
    {
        $t = new SplayTree();
        $t->load([22, 56, 0, -10, 12], [], true);

        $t->load([100, 500, -400, 20, 10], [], true);
        $this->assertEquals([-400, -10, 0, 10, 12, 20, 22, 56, 100, 500], $t->keys());
    }

    /**
     * should be able to load less contents into a tree with contents
     */
    public function testBulk4(): void
    {
        $t = new SplayTree();
        $t->load([100, 500, -400, 20, 10], [], true);

        $t->load([22], [], true);
        $this->assertEquals([-400, 10, 20, 22, 100, 500], $t->keys());
    }

    /**
     * should be able to load more contents into a tree with less contents
     */
    public function testBulk5(): void
    {
        $t = new SplayTree();
        $t->load([22], [], true);

        $t->load([100, 500, -400, 20, 10], [], true);
        $this->assertEquals([-400, 10, 20, 22, 100, 500], $t->keys());
    }

    /**
     * should be able to load into a tree with contents (interleave)
     */
    public function testBulk6(): void
    {
        $array1 = [];
        $array2 = [];
        for ($i = 0; $i < 10; $i++) {
            $array1[] = $i * 10;
            $array2[] = 5 + 10 * $i;
        }

        $expectedArray = [];
        for ($i = 0; $i < 20; $i++) {
            $expectedArray[] = 5 * $i;
        }

        $t = new SplayTree();
        $t->load($array1);
        $t->load($array2);
        $this->assertEquals($expectedArray, $t->keys());
    }
}