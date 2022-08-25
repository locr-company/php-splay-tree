<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\{Node, SplayTree};

final class SplayTreeTraversalTest extends TestCase
{
    /**
     * should traverse the tree in order
     */
    public function testTraversal1(): void
    {
        $tree = new SplayTree();
        $tree->insert(3);
        $tree->insert(1);
        $tree->insert(0);
        $tree->insert(2);

        $i = 0;
        foreach ($tree as $n) {
            $this->assertEquals($i++, $n->key);
        }
    }

    /**
     * should find predecessor for the node
     */
    public function testTraversal2(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        for ($i = 1; $i < 10; $i++) {
            $this->assertEquals($tree->find($i - 1)->key, $tree->previousNode($tree->find($i))->key);
        }
    }

    /**
     * should find successor for a node
     */
    public function testTraversal3(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        for ($i = 0; $i < 9; $i++) {
            $this->assertEquals($tree->find($i + 1), $tree->nextNode($tree->find($i)));
        }
    }

    /**
     * should return null for predecessor of the min node
     */
    public function testTraversal4(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        $min = $tree->minNode();
        $this->assertNull($tree->previousNode($min));
        $tree->remove($min->key);
        $min = $tree->minNode();
        $this->assertNull($tree->previousNode($min));
    }

    /**
     * should return null for successor of the max node
     */
    public function testTraversal5(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }
    
        $max = $tree->maxNode();
        $this->assertNull($tree->next($max));
        $tree->remove($max->key);
        $max = $tree->maxNode();
        $this->assertNull($tree->next($max));
    }

    /**
     * should reach end in walking
     */
    public function testTraversal6(): void
    {
        $tree = new SplayTree();
        $keys = [
            49153, 49154, 49156, 49157, 49158, 49159, 49160, 49161,
            49163, 49165, 49191, 49199, 49201, 49202, 49203, 49204,
            49206, 49207, 49208, 49209, 49210, 49212
        ];

        foreach ($keys as $k) {
            $tree->insert($k);
        }

        $min = $tree->minNode();

        foreach ($keys as $key) {
            $this->assertEquals($key, $min->key);
            $min = $tree->nextNode($min);
        }

        $this->assertNull($min);
    }

    /**
     * bidirectional stepping
     */
    public function testTraversal7(): void
    {
        $tree = new SplayTree();
        $keys = [
            49153, 49154, 49156, 49157, 49158, 49159, 49160, 49161,
            49163, 49165, 49191, 49199, 49201, 49202, 49203, 49204,
            49206, 49207, 49208, 49209, 49210, 49212
        ];

        $tree->load($keys);

        $min = $tree->minNode();

        foreach ($keys as $i => $key) {
            $this->assertEquals($key, $min->key);
            if ($i !== 0) {
                $this->assertEquals($key, $tree->nextNode($tree->previousNode($min))->key);
            }
            $min = $tree->nextNode($min);
        }

        $this->assertNull($min);
    }

    /**
     * should find successor and predecessor for 2-nodes tree
     */
    public function testTraversal8(): void
    {
        $tree = new SplayTree();
        $tree->insert(5);
        $tree->insert(10);

        $min = $tree->minNode();
        $this->assertEquals(5, $min->key);
        $this->assertNull($tree->previousNode($min));
        $this->assertEquals(10, $tree->nextNode($min)->key);

        $max = $tree->maxNode();
        $this->assertEquals(10, $max->key);
        $this->assertNull($tree->next($max));
        $this->assertEquals(5, $tree->previousNode($max)->key);
    }

    /**
     * should be able to get a node by its index
     */
    public function testTraversal9(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($i, $tree->at($i)->key);
        }

        $this->assertNull($tree->at(10));
        $this->assertNull($tree->at(-1));
    }

    /**
     * should support range walking
     */
    public function testTraversal10(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        $arr = [];
        $tree->range(3, 8, function (Node $n) use (&$arr) {
            $arr[] = $n->key;
        });
        $this->assertEquals([3, 4, 5, 6, 7, 8], $arr);
    }

    /**
     * should support range walking with non-existent low key
     */
    public function testTraversal11(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        $arr = [];
        $tree->range(-3, 5, function (Node $n) use (&$arr) {
            $arr[] = $n->key;
        });

        $this->assertEquals([0, 1, 2, 3, 4, 5], $arr);
    }

    /**
     * should support range walking with non-existent high key
     */
    public function testTraversal12(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        $arr = [];
        $tree->range(3, 15, function (Node $n) use (&$arr) {
            $arr[] = $n->key;
        });

        $this->assertEquals([3, 4, 5, 6, 7, 8, 9], $arr);
    }

    /**
     * should support range walking with both keys out of range
     */
    public function testTraversal13(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        $arr = [];
        $tree->range(10, 20, function (Node $n) use (&$arr) {
            $arr[] = $n->key;
        });

        $this->assertEquals(0, count($arr));

        $tree->range(-10, 20, function (Node $n) use (&$arr) {
            $arr[] = $n->key;
        });
        $this->assertEquals($tree->keys(), $arr);
    }

    /**
     * should support range walking with interruption
     */
    public function testTraversal14(): void
    {
        $tree = new SplayTree();
        for ($i = 0; $i < 10; $i++) {
            $tree->insert($i);
        }

        $arr = [];
        $tree->range(2, 8, function (Node $n) use (&$arr) {
            $arr[] = $n->key;
            if ($n->key === 5) {
                return true;
            }
        });

        $this->assertEquals([2, 3, 4, 5], $arr);
    }
}