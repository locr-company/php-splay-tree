<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\SplayTree;

final class SplayTreeKeyValuesTest extends TestCase
{
    /**
     * should return sorted keys
     */
    public function testKeyValues1(): void
    {
        $t = new SplayTree(function ($a, $b) {
            return $b - $a;
        });
        $t->insert(5);
        $t->insert(-10);
        $t->insert(0);
        $t->insert(33);
        $t->insert(2);

        $this->assertEquals([33, 5, 2, 0, -10], $t->keys());
    }

    /**
     * should return sorted keys
     */
    public function testKeyValues2(): void
    {
        $t = new SplayTree();
        $t->insert(5);
        $t->insert(-10);
        $t->insert(0);
        $t->insert(33);
        $t->insert(2);

        $this->assertEquals([-10, 0, 2, 5, 33], $t->keys());
    }

    /**
     * should return sorted values
     */
    public function testKeyValues3(): void
    {
        $t = new SplayTree();
        $t->insert(5,   'D');
        $t->insert(-10, 'A');
        $t->insert(0,   'B');
        $t->insert(33,  'E');
        $t->insert(2,   'C');
    
        $this->assertEquals([-10, 0, 2, 5, 33], $t->keys());
        $this->assertEquals(['A', 'B', 'C', 'D', 'E'], $t->values());
    }

    /**
     * should return sorted values
     */
    public function testKeyValues4(): void
    {
        $t = new SplayTree(function ($a, $b) {
            return $b - $a;
        });
        $t->insert(5,   'D');
        $t->insert(-10, 'A');
        $t->insert(0,   'B');
        $t->insert(33,  'E');
        $t->insert(2,   'C');

        $this->assertEquals([33, 5, 2, 0, -10], $t->keys());
        $this->assertEquals(['E', 'D', 'C', 'B', 'A'], $t->values());
    }

    /**
     * should return sorted values after bulk insert
     */
    public function testKeyValues5(): void
    {
        $t = new SplayTree();
        $t->load([5, -10, 0, 33, 2], ['D', 'A', 'B', 'E', 'C'], true);

        $this->assertEquals([-10, 0, 2, 5, 33], $t->keys());
        $this->assertEquals(['A', 'B', 'C', 'D', 'E'], $t->values());
    }

    /**
     * here we are testing recursion approach
     * should be able to bulk-load 10000 items
     */
    public function testKeyValues6(): void
    {
        $t = new SplayTree();

        $keys = [];
        for ($i = 0; $i < 10_000; $i++) {
            $keys[$i] = $i;
        }

        $t->load($keys);

        $this->assertEquals(array_slice($keys, 0, 20), array_slice($t->keys(), 0, 20));
    }
}