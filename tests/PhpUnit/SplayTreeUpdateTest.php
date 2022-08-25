<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SplayTree\{Node, SplayTree};

final class SplayTreeUpdateTest extends TestCase
{
    private function createTree(array $values): SplayTree
    {
        $t = new SplayTree();
        foreach ($values as $v) {
            $t->insert($v);
        }

        return $t;
    }

    private function toArray(?Node $tree, array &$arr = []): array
    {
        if (!is_null($tree)) {
            $this->toArray($tree->left, $arr);
            $arr[] = $tree->key;
            $this->toArray($tree->right, $arr);
        }

        return $arr;
    }

    /**
     * split
     */
    public function testUpdate1(): void
    {
        $t = $this->createTree([1, 2, 3]);
        $split = $t->split(0);
        $this->assertNull($split['left']);
        $this->assertEquals([1, 2, 3], $this->toArray($split['right']));

        $t = $this->createTree([1, 2, 3]);
        $split = $t->split(2.5);
        $this->assertEquals([1, 2], $this->toArray($split['left']));
        $this->assertEquals([3], $this->toArray($split['right']));

        $t = $this->createTree([1, 2, 3]);
        $split = $t->split(2);
        $this->assertEquals([1], $this->toArray($split['left']));
        $this->assertEquals([3], $this->toArray($split['right']));

        $t = $this->createTree([1, 2, 3]);
        $split = $t->split(1);
        $this->assertEquals([], $this->toArray($split['left']));
        $this->assertEquals([2, 3], $this->toArray($split['right']));

        $t = $this->createTree([1, 2, 3]);
        $split = $t->split(3);
        $this->assertEquals([1, 2], $this->toArray($split['left']));
        $this->assertEquals([], $this->toArray($split['right']));
    }

    /**
     * merge
     */
    public function testUpdate2(): void
    {
        $t = $this->createTree([1, 2, 3, 4, 5]);
        $t->update(3, 6);
        $this->assertEquals([1, 2, 4, 5, 6], $t->keys());
        $t->update(2, 0);
        $this->assertEquals([0, 1, 4, 5, 6], $t->keys());
        $t->update(0, 7);
        $this->assertEquals([1, 4, 5, 6, 7], $t->keys());
        $t->update(7, -3);
        $this->assertEquals([-3, 1, 4, 5, 6], $t->keys());
    }
}